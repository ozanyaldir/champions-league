<?php

namespace App\Repositories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class TeamRepository
{
    /**
     * @return Collection<int, Team>
     */
    public function getAll(): Collection
    {
        return Team::all();
    }
}
