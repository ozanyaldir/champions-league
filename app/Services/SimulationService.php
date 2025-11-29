<?php

namespace App\Services;

use App\Repositories\FixtureRepository;
use App\Repositories\GameRepository;
use App\Repositories\TeamRepository;
use Illuminate\Database\Eloquent\Collection;

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

    /**
     * @return void
     */
    public function reset()
    {
        $this->gameRepository->deleteAll();
        $this->fixtureRepository->deleteAll();
    }

    /**
     * Simulate next unplayed week (create Game rows)
     *
     * @return int|null week number simulated or null if none left
     */
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
                    $homeGoals = $this->sampleGoals($homeExp);
                    $awayGoals = $this->sampleGoals($awayExp);

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

    /**
     * Simulate all remaining weeks
     */
    public function playAll(): void
    {
        while ($this->playNextWeek() !== null) {
            // continue until none left
        }
    }

    /**
     * Simple sampling function for goals: rounds Poisson-ish value
     */
    protected function sampleGoals(float $lambda): int
    {
        // Quick Poisson approximation: draw by repeated exp method would be heavy;
        // use a small randomization around lambda to produce integer goals.
        $v = \max(0, round($lambda + (mt_rand(-100, 100) / 100.0) * 0.6));

        return (int) $v;
    }

    /**
     * Build league table by scanning Game records and fixtures (returns collection of team models with stats attached)
     */
    public function buildLeagueTable(): Collection
    {
        $teams = $this->teamRepository->getAll(); // eager-load if needed
        $allGames = $this->gameRepository->getAll(); // eager-load fixture relation in repo

        // init stats map
        $stats = [];
        foreach ($teams as $t) {
            $stats[$t->id] = [
                'played' => 0, 'won' => 0, 'draw' => 0, 'lost' => 0, 'points' => 0, 'gf' => 0, 'ga' => 0,
            ];
        }

        foreach ($allGames as $g) {
            $fixture = $g->fixture;
            if (! $fixture) {
                continue;
            }

            $homeId = $fixture->home_team_id;
            $awayId = $fixture->away_team_id;
            if (! isset($stats[$homeId]) || ! isset($stats[$awayId])) {
                continue;
            }

            $hg = (int) $g->home_goals;
            $ag = (int) $g->away_goals;

            $stats[$homeId]['played']++;
            $stats[$awayId]['played']++;

            $stats[$homeId]['gf'] += $hg;
            $stats[$homeId]['ga'] += $ag;

            $stats[$awayId]['gf'] += $ag;
            $stats[$awayId]['ga'] += $hg;

            if ($hg === $ag) {
                $stats[$homeId]['draw']++;
                $stats[$awayId]['draw']++;
                $stats[$homeId]['points']++;
                $stats[$awayId]['points']++;
            } elseif ($hg > $ag) {
                $stats[$homeId]['won']++;
                $stats[$awayId]['lost']++;
                $stats[$homeId]['points'] += 3;
            } else {
                $stats[$awayId]['won']++;
                $stats[$homeId]['lost']++;
                $stats[$awayId]['points'] += 3;
            }
        }

        // attach stats to team models (in-memory)
        foreach ($teams as $t) {
            $s = $stats[$t->id];
            $t->played = $s['played'];
            $t->won = $s['won'];
            $t->draw = $s['draw'];
            $t->lost = $s['lost'];
            $t->points = $s['points'];
            $t->gf = $s['gf'];
            $t->ga = $s['ga'];
            $t->gd = $s['gf'] - $s['ga'];
        }

        return $teams;
    }

    /**
     * Predict championship probabilities using a simple Monte-Carlo-ish approach:
     * - For small number of remaining weeks we can do deterministic branching,
     * - Simpler: run N random fast simulations of remaining matches and see who finishes top.
     *
     * Here we run Monte Carlo with default 500 iterations (cheap).
     */
    public function predictChampionship(int $iterations = 500): array
    {
        $teams = $this->teamRepository->getAll()->keyBy('id');
        $allFixtures = $this->fixtureRepository->allWithTeams();
        $played = $this->gameRepository->getAll(); // games persisted

        // prepare current points
        $currentPoints = [];
        foreach ($teams as $id => $team) {
            $currentPoints[$id] = 0;
        }
        foreach ($played as $g) {
            $f = $g->fixture;
            if (! $f) {
                continue;
            }
            $hg = (int) $g->home_goals;
            $ag = (int) $g->away_goals;
            if ($hg === $ag) {
                $currentPoints[$f->home_team_id] += 1;
                $currentPoints[$f->away_team_id] += 1;
            } elseif ($hg > $ag) {
                $currentPoints[$f->home_team_id] += 3;
            } else {
                $currentPoints[$f->away_team_id] += 3;
            }
        }

        // collect remaining fixtures (fixture models that don't have a game)
        $remainingFixtures = $allFixtures->filter(function ($f) use ($played) {
            return ! $played->first(fn ($g) => $g->fixture_id == $f->id);
        })->values();

        if ($remainingFixtures->isEmpty()) {
            // season finished -> top team's 100%
            $finalPoints = $currentPoints;
            arsort($finalPoints);
            $topId = array_key_first($finalPoints);
            $res = [];
            foreach ($teams as $t) {
                $res[$t->name] = ($t->id == $topId) ? 100.0 : 0.0;
            }

            return $res;
        }

        // Monte Carlo runs
        $wins = array_fill_keys($teams->keys()->all(), 0);

        $iterations = max(50, $iterations); // at least 50
        for ($it = 0; $it < $iterations; $it++) {
            // copy points
            $points = $currentPoints;

            // simulate remaining fixtures quickly (random but weighted)
            foreach ($remainingFixtures as $f) {
                $homePower = $f->homeTeam->power ?? 50;
                $awayPower = $f->awayTeam->power ?? 50;
                $avg = ($homePower + $awayPower) / 2.0;

                $homeExp = $this->baseGoalRate * ($homePower / max(1, $avg)) * $this->homeAdvantage;
                $awayExp = $this->baseGoalRate * ($awayPower / max(1, $avg));

                $hg = $this->sampleGoals($homeExp);
                $ag = $this->sampleGoals($awayExp);

                if ($hg === $ag) {
                    $points[$f->home_team_id] += 1;
                    $points[$f->away_team_id] += 1;
                } elseif ($hg > $ag) {
                    $points[$f->home_team_id] += 3;
                } else {
                    $points[$f->away_team_id] += 3;
                }
            }

            // determine winner (highest points, tie-breaker random)
            arsort($points);
            $topId = array_key_first($points);
            $wins[$topId] = $wins[$topId] + 1;
        }

        // compute percentages
        $res = [];
        foreach ($teams as $id => $team) {
            $res[$team->name] = round(($wins[$id] / $iterations) * 100, 1);
        }

        return $res;
    }
}
