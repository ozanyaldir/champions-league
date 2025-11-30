<?php

namespace App\Services;

use App\Repositories\FixtureRepository;
use App\Repositories\GameRepository;
use App\Repositories\TeamRepository;

class ChampionshipPredictorService
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

    protected function sampleGoals(float $lambda): int
    {
        $v = \max(0, round($lambda + (mt_rand(-100, 100) / 100.0) * 0.6));

        return (int) $v;
    }
}
