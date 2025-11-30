<?php

namespace App\Services;

use App\Repositories\FixtureRepository;
use App\Repositories\GameRepository;
use App\Repositories\TeamRepository;
use Illuminate\Database\Eloquent\Collection;

class LeagueTableBuilderService
{
    protected $fixtureRepository;

    protected $gameRepository;

    protected $teamRepository;

    public function __construct(
        TeamRepository $teamRepository,
        FixtureRepository $fixtureRepository,
        GameRepository $gameRepository,
    ) {
        $this->teamRepository = $teamRepository;
        $this->fixtureRepository = $fixtureRepository;
        $this->gameRepository = $gameRepository;
    }

    public function buildLeagueTable(): Collection
    {
        $teams = $this->teamRepository->getAll();
        $allGames = $this->gameRepository->getAll();

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
}
