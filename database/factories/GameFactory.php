<?php

namespace Database\Factories;

use App\Models\Fixture;
use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Game>
 */
class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition(): array
    {
        return [
            'fixture_id' => Fixture::factory(),
            'home_goals' => $this->faker->numberBetween(0, 5),
            'away_goals' => $this->faker->numberBetween(0, 5),
        ];
    }

    public function unplayed(): Factory
    {
        return $this->state(fn () => [
            'home_goals' => 0,
            'away_goals' => 0,
        ]);
    }

    public function played(): Factory
    {
        return $this->state(fn () => [
            'home_goals' => $this->faker->numberBetween(0, 5),
            'away_goals' => $this->faker->numberBetween(0, 5),
        ]);
    }
}
