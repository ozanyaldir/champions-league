<?php

namespace App\Repositories;

use App\Models\Fixture;

class FixtureRepository
{
    public function allWithTeams()
    {
        return Fixture::with(['homeTeam', 'awayTeam'])
                      ->orderBy('week')
                      ->get();
    }

    public function deleteAll()
    {
        Fixture::query()->delete();
    }

    public function insertMany(array $fixtures)
    {
        Fixture::insert($fixtures);
    }
}
