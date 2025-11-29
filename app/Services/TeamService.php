<?php

namespace App\Services;

use App\Repositories\TeamRepository;
use Illuminate\Database\Eloquent\Collection;

class TeamService
{
    protected $teamRepository;

    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getAll(): Collection
    {
        return $this->teamRepository->getAll();
    }
}
