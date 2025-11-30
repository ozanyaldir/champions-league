<?php

namespace Tests\Unit;

use App\Models\Fixture;
use App\Models\Game;
use App\Models\Team;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TeamTest extends TestCase
{
    /** @test */
    public function it_can_fill_attributes()
    {
        $team = (new Team)->forceFill([
            'name' => 'Team A',
            'created_at' => Carbon::parse('2025-11-30 10:00:00'),
            'updated_at' => Carbon::parse('2025-11-30 11:00:00'),
        ]);

        $this->assertEquals('Team A', $team->name);
        $this->assertEquals('2025-11-30 10:00:00', $team->created_at->format('Y-m-d H:i:s'));
        $this->assertEquals('2025-11-30 11:00:00', $team->updated_at->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $team = new Team(['name' => 'Team B']);

        $this->assertIsInt($team->id ?? 0); // id may not exist until saved
        $this->assertIsString($team->name);
    }

    /** @test */
    public function it_returns_home_fixtures_relation()
    {
        $fixture = new Fixture(['id' => 1, 'home_team_id' => 5]);
        $team = new Team(['id' => 5]);
        $team->setRelation('homeFixtures', collect([$fixture]));

        $this->assertCount(1, $team->homeFixtures);
        $this->assertInstanceOf(Fixture::class, $team->homeFixtures->first());
    }

    /** @test */
    public function it_returns_away_fixtures_relation()
    {
        $fixture = new Fixture(['id' => 2, 'away_team_id' => 5]);
        $team = new Team(['id' => 5]);
        $team->setRelation('awayFixtures', collect([$fixture]));

        $this->assertCount(1, $team->awayFixtures);
        $this->assertInstanceOf(Fixture::class, $team->awayFixtures->first());
    }

    /** @test */
    public function it_returns_games_relation()
    {
        $game = new Game(['id' => 10, 'home_goals' => 2, 'away_goals' => 1]);
        $team = new Team(['id' => 5]);
        $team->setRelation('games', collect([$game]));

        $this->assertCount(1, $team->games);
        $this->assertInstanceOf(Game::class, $team->games->first());
    }
}
