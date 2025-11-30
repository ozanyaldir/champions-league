<?php

namespace Tests\Unit;

use App\Models\Fixture;
use App\Models\Game;
use App\Models\Team;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class FixtureTest extends TestCase
{
    /** @test */
    public function it_can_fill_attributes()
    {
        $fixture = (new Fixture)->forceFill([
            'home_team_id' => 1,
            'away_team_id' => 2,
            'week' => 3,
            'created_at' => Carbon::parse('2025-11-30 10:00:00'),
            'updated_at' => Carbon::parse('2025-11-30 11:00:00'),
        ]);

        $this->assertEquals(1, $fixture->home_team_id);
        $this->assertEquals(2, $fixture->away_team_id);
        $this->assertEquals(3, $fixture->week);
        $this->assertEquals('2025-11-30 10:00:00', $fixture->created_at->format('Y-m-d H:i:s'));
        $this->assertEquals('2025-11-30 11:00:00', $fixture->updated_at->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $fixture = new Fixture([
            'home_team_id' => '1',
            'away_team_id' => '2',
            'week' => '5',
        ]);

        $this->assertIsInt($fixture->home_team_id);
        $this->assertIsInt($fixture->away_team_id);
        $this->assertIsInt($fixture->week);
    }

    /** @test */
    public function it_returns_home_team_relation()
    {
        $homeTeam = new Team(['id' => 1, 'name' => 'Team A']);
        $fixture = new Fixture(['home_team_id' => 1]);
        $fixture->setRelation('homeTeam', $homeTeam);

        $this->assertInstanceOf(Team::class, $fixture->homeTeam);
        $this->assertEquals('Team A', $fixture->homeTeam->name);
    }

    /** @test */
    public function it_returns_away_team_relation()
    {
        $awayTeam = new Team(['id' => 2, 'name' => 'Team B']);
        $fixture = new Fixture(['away_team_id' => 2]);
        $fixture->setRelation('awayTeam', $awayTeam);

        $this->assertInstanceOf(Team::class, $fixture->awayTeam);
        $this->assertEquals('Team B', $fixture->awayTeam->name);
    }

    /** @test */
    public function it_returns_game_relation()
    {
        $game = new Game(['id' => 10, 'home_goals' => 2, 'away_goals' => 1]);
        $fixture = new Fixture(['id' => 5]);
        $fixture->setRelation('game', $game);

        $this->assertInstanceOf(Game::class, $fixture->game);
        $this->assertEquals(2, $fixture->game->home_goals);
        $this->assertEquals(1, $fixture->game->away_goals);
    }
}
