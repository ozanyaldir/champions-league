<?php

namespace Database\Factories;

use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class FixtureFactory extends Factory
{
    protected $model = Fixture::class;

    public function definition(): array
    {
        return [
            'home_team_id' => Team::factory(),
            'away_team_id' => Team::factory(),
            'week' => $this->faker->numberBetween(1, 38),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Fixture $fixture) {
            if ($fixture->home_team_id === $fixture->away_team_id) {
                $fixture->away_team_id = Team::factory()->create()->id;
            }
        });
    }
}
