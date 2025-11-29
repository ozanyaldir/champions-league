<?php

namespace App\Repositories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Collection;

class GameRepository
{
    /**
     * @return Collection<int, Game>
     */
    public function getAll(): Collection
    {
        return Game::all();
    }

    /**
     * @return void
     */
    public function deleteAll()
    {
        Game::query()->delete();
    }

    /**
     * @return void
     */
    public function create(array $m)
    {
        return Game::create($m);
    }
}
