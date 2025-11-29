<?php

namespace App\Repositories;

use App\Models\Fixture;
use Illuminate\Database\Eloquent\Collection;

class FixtureRepository
{
    /**
     * @return Collection<int, Fixture>
     */
    public function allWithTeams(): Collection
    {
        return Fixture::with(['homeTeam', 'awayTeam'])
                      ->orderBy('week')
                      ->get();
    }

    /**
     * @return void
     */
    public function deleteAll()
    {
        Fixture::query()->delete();
    }

    /**
     * @return void
     */
    public function insertMany(array $m)
    {
        Fixture::insert($m);
    }
}
