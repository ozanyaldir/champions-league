<?php

namespace App\Repositories;

use App\Models\Team;

class TeamRepository
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return Team::all();
    }
}
