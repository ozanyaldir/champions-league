<?php

namespace App\Repositories;

use App\Models\Fixture;
use Illuminate\Database\Eloquent\Collection;

class FixtureRepository
{
    protected Fixture $model;

    public function __construct(Fixture $model)
    {
        $this->model = $model;
    }

    /**
     * Get all fixtures with their teams, ordered by week
     *
     * @return Collection<int, Fixture>
     */
    public function allWithTeams(): Collection
    {
        return $this->model->with(['homeTeam', 'awayTeam'])
            ->orderBy('week')
            ->get();
    }

    /**
     * Delete all fixtures
     */
    public function deleteAll(): void
    {
        $this->model->query()->delete();
    }

    /**
     * Insert multiple fixtures
     */
    public function insertMany(array $data): void
    {
        $this->model->insert($data);
    }
}
