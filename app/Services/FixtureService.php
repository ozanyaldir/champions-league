<?php

namespace App\Services;

use App\Repositories\FixtureRepository;
use App\Repositories\GameRepository;
use App\Repositories\TeamRepository;

class FixtureService
{
    protected $fixtureRepo;
    protected $gameRepo;
    protected $teamRepo;

    public function __construct(
        FixtureRepository $fixtureRepo,
        GameRepository $gameRepo,
        TeamRepository $teamRepo
    ) {
        $this->fixtureRepo = $fixtureRepo;
        $this->gameRepo = $gameRepo;
        $this->teamRepo = $teamRepo;
    }

    /**
     * Get all fixtures grouped by week
     */
    public function getFixturesGroupedByWeek()
    {
        $fixtures = $this->fixtureRepo->allWithTeams();

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
     * Get all teams
     */
    public function getAllTeams()
    {
        return $this->teamRepo->getAll();
    }

    /**
     * Generate fixtures for all teams (round-robin)
     */
    public function generateFixtures()
    {
        // 1. Delete old games and fixtures
        $this->gameRepo->deleteAll();
        $this->fixtureRepo->deleteAll();

        $teams = $this->teamRepo->getAll();

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
        $this->fixtureRepo->insertMany($fixtures);

        return true;
    }
}
