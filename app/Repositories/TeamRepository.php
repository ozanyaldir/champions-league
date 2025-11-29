<?php

namespace App\Repositories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class TeamRepository
{
    protected $model;

    public function __construct(Team $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getAll(): Collection
    {
        return $this->model->all();
    }
}
