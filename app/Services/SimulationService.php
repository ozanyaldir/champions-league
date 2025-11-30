<?php

namespace App\Services;

use App\Repositories\FixtureRepository;
use App\Repositories\GameRepository;
use App\Repositories\TeamRepository;
use App\Support\MathUtils;

class SimulationService
{
    protected $fixtureRepository;

    protected $gameRepository;

    protected $teamRepository;

    protected float $homeAdvantage = 1.15;

    protected float $baseGoalRate = 1.3;

    public function __construct(
        TeamRepository $teamRepository,
        FixtureRepository $fixtureRepository,
        GameRepository $gameRepository,
    ) {
        $this->teamRepository = $teamRepository;
        $this->fixtureRepository = $fixtureRepository;
        $this->gameRepository = $gameRepository;
    }

    public function reset(): void
    {
        $this->gameRepository->deleteAll();
        $this->fixtureRepository->deleteAll();
    }

    public function playAll(): void
    {
        while ($this->playNextWeek() !== null) {
            // continue until none left
        }
    }

    public function playNextWeek(): ?int
    {
        // load fixtures and games
        $allFixtures = $this->fixtureRepository->allWithTeams(); // collection of Fixture models with homeTeam, awayTeam and week
        $playedGames = $this->gameRepository->getAll(); // collection of Game models with fixture relation

        // find weeks that have fixtures
        $weeks = $allFixtures->groupBy('week')->keys()->sort()->values();

        // find the first week where at least one fixture has no game created
        foreach ($weeks as $week) {
            $fixturesThisWeek = $allFixtures->where('week', $week);
            // if every fixture has a corresponding game, skip
            $allPlayed = true;
            foreach ($fixturesThisWeek as $fixture) {
                // check if a game exists for this fixture
                $found = $playedGames->first(fn ($g) => $g->fixture_id == $fixture->id);
                if (! $found) {
                    $allPlayed = false;
                    break;
                }
            }
            if (! $allPlayed) {
                // simulate this week's fixtures
                foreach ($fixturesThisWeek as $fixture) {
                    // skip fixtures that already have a game
                    $existing = $playedGames->first(fn ($g) => $g->fixture_id == $fixture->id);
                    if ($existing) {
                        continue;
                    }

                    $home = $fixture->homeTeam;
                    $away = $fixture->awayTeam;

                    // compute expected goals (simple model)
                    $homePower = $home->power ?? 50;
                    $awayPower = $away->power ?? 50;
                    $avgPower = ($homePower + $awayPower) / 2.0;

                    $homeExp = $this->baseGoalRate * ($homePower / max(1, $avgPower)) * $this->homeAdvantage;
                    $awayExp = $this->baseGoalRate * ($awayPower / max(1, $avgPower));

                    // ensure minimum expected
                    $homeExp = max(0.2, $homeExp);
                    $awayExp = max(0.2, $awayExp);

                    // convert expected to actual goals (small random Poisson-ish)
                    $homeGoals = MathUtils::sampleGoals($homeExp);
                    $awayGoals = MathUtils::sampleGoals($awayExp);

                    // create game record (persist)
                    $this->gameRepository->create([
                        'fixture_id' => $fixture->id,
                        'home_goals' => $homeGoals,
                        'away_goals' => $awayGoals,
                    ]);
                }

                return $week;
            }
        }

        // no unplayed weeks left
        return null;
    }
}
