<?php

namespace App\Services;

use App\Repositories\TeamRepository;

class TeamService
{
    protected $teamRepository;

    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    /**
     * Return all teams
     */
    public function getAllTeams()
    {
        return $this->teamRepository->getAll();
    }
}
