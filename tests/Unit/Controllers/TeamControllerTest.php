<?php

namespace Tests\Unit;

use App\Http\Controllers\TeamController;
use App\Services\TeamService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class TeamControllerTest extends TestCase
{
    protected $teamService;

    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teamService = Mockery::mock(TeamService::class);
        $this->controller = new TeamController($this->teamService);
    }

    /** @test */
    public function index_returns_view_with_team_resources()
    {
        $teams = new Collection([
            (object) ['id' => 1, 'name' => 'Team A'],
            (object) ['id' => 2, 'name' => 'Team B'],
        ]);

        $this->teamService
            ->shouldReceive('getAllTeams')
            ->once()
            ->andReturn($teams);

        $response = $this->controller->index();

        $this->assertEquals('teams', $response->name());

        $data = $response->getData();
        $this->assertCount(2, $data['teams']);
        $this->assertEquals('Team A', $data['teams'][0]->name);
        $this->assertEquals('Team B', $data['teams'][1]->name);
    }
}
