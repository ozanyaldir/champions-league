<?php

namespace App\Repositories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Collection;

class GameRepository
{
    protected Game $model;

    public function __construct(Game $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getAll(): Collection
    {
        return $this->model->query()->get();
    }

    public function deleteAll(): void
    {
        $this->model->query()->delete();
    }

    /**
     * Create a new Game record
     */
    public function create(array $data): Game
    {
        return $this->model->create($data);
    }
}
