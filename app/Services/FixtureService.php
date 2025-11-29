<?php

namespace App\Services;

use App\Repositories\FixtureRepository;
use App\Repositories\GameRepository;
use App\Repositories\TeamRepository;

class FixtureService
{
    protected $fixtureRepository;
    protected $gameRepository;
    protected $teamRepository;

    public function __construct(
        FixtureRepository $fixtureRepository,
        GameRepository $gameRepository,
        TeamRepository $teamRepository
    ) {
        $this->fixtureRepository = $fixtureRepository;
        $this->gameRepository = $gameRepository;
        $this->teamRepository = $teamRepository;
    }

    /**
     * Get all fixtures grouped by week
     */
    public function getFixturesGroupedByWeek()
    {
        $fixtures = $this->fixtureRepository->allWithTeams();

        $weeks = [];
        foreach ($fixtures as $fixture) {
            $weeks[$fixture->week][] = [
                'home' => $fixture->homeTeam->name,
                'away' => $fixture->awayTeam ? $fixture->awayTeam->name : 'Bye',
            ];
        }

        return $weeks;
    }

    /**
     * Generate fixtures for all teams (round-robin)
     */
    public function generateFixtures()
    {
        $this->gameRepository->deleteAll();
        $this->fixtureRepository->deleteAll();

        $teams = $this->teamRepository->getAll();

        if ($teams->count() < 2) {
            return false;
        }

        $teamIds = $teams->pluck('id')->toArray();
        shuffle($teamIds);

        $fixtures = [];
        $week = 1;

        // Round-robin: each team plays each other once
        for ($i = 0; $i < count($teamIds); $i++) {
            for ($j = $i + 1; $j < count($teamIds); $j++) {
                $fixtures[] = [
                    'home_team_id' => $teamIds[$i],
                    'away_team_id' => $teamIds[$j],
                    'week' => $week,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $week++;
            }
        }

        // Insert fixtures
        $this->fixtureRepository->insertMany($fixtures);

        return true;
    }
}
