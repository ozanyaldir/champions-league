<?php

namespace Tests\Unit;

use App\Models\Fixture;
use App\Models\Game;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class GameTest extends TestCase
{
    /** @test */
    public function it_can_fill_attributes()
    {
        $game = (new Game)->forceFill([
            'fixture_id' => 10,
            'home_goals' => 2,
            'away_goals' => 1,
            'created_at' => Carbon::parse('2025-11-30 10:00:00'),
            'updated_at' => Carbon::parse('2025-11-30 11:00:00'),
        ]);

        $this->assertEquals(10, $game->fixture_id);
        $this->assertEquals(2, $game->home_goals);
        $this->assertEquals(1, $game->away_goals);
        $this->assertEquals('2025-11-30 10:00:00', $game->created_at->format('Y-m-d H:i:s'));
        $this->assertEquals('2025-11-30 11:00:00', $game->updated_at->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $game = new Game([
            'fixture_id' => '5',
            'home_goals' => '3',
            'away_goals' => '2',
        ]);

        $this->assertIsInt($game->fixture_id);
        $this->assertIsInt($game->home_goals);
        $this->assertIsInt($game->away_goals);
    }

    /** @test */
    public function it_returns_fixture_relation()
    {
        $fixture = new Fixture;
        $fixture->id = 10;

        $game = new Game(['fixture_id' => 10]);
        $game->setRelation('fixture', $fixture);

        $this->assertInstanceOf(Fixture::class, $game->fixture);
        $this->assertEquals(10, $game->fixture->id);
    }
}
