<?php

namespace App\Repositories;

use App\Models\Game;

class GameRepository
{
    public function deleteAll()
    {
        Game::query()->delete();
    }

    public function create(array $data)
    {
        return Game::create($data);
    }
}
