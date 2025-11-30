<?php

namespace Tests\Unit;

use App\Http\Controllers\SimulationController;
use App\Orchestrators\SimulationOrchestrator;
use App\Services\FixtureService;
use App\Services\TeamService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class SimulationControllerTest extends TestCase
{
    protected $teamService;

    protected $fixtureService;

    protected $simulationOrchestrator;

    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teamService = Mockery::mock(TeamService::class);
        $this->fixtureService = Mockery::mock(FixtureService::class);
        $this->simulationOrchestrator = Mockery::mock(SimulationOrchestrator::class);

        $this->controller = new SimulationController(
            $this->teamService,
            $this->fixtureService,
            $this->simulationOrchestrator
        );
    }

    /** @test */
    public function index_returns_view_with_data()
    {
        $table = new Collection;
        $fixtures = [
            1 => [
                (object) ['id' => 1, 'homeTeam' => (object) ['name' => 'A'], 'awayTeam' => (object) ['name' => 'B']],
            ],
        ];
        $predictions = ['TeamA' => 0.5];

        $this->simulationOrchestrator
            ->shouldReceive('buildLeagueTable')->once()->andReturn($table);
        $this->simulationOrchestrator
            ->shouldReceive('getCurrentWeekFixtures')->once()->andReturn($fixtures);
        $this->simulationOrchestrator
            ->shouldReceive('predictChampionship')->once()->andReturn($predictions);

        $response = $this->controller->index();

        $this->assertEquals('simulation', $response->name());
        $data = $response->getData();
        $this->assertSame($table, $data['table']);
        $this->assertSame($fixtures, $data['currentWeekFixtures']);
        $this->assertSame($predictions, $data['predictions']);
    }

    /** @test */
    public function start_redirects_to_simulation_index()
    {
        $response = $this->controller->start();
        $this->assertEquals(route('simulation.index'), $response->getTargetUrl());
    }

    /** @test */
    public function play_all_calls_orchestrator_and_redirects()
    {
        $this->simulationOrchestrator->shouldReceive('playAll')->once();

        $response = $this->controller->playAll();

        $this->assertEquals(route('simulation.index'), $response->getTargetUrl());
    }

    /** @test */
    public function play_next_week_calls_orchestrator_and_redirects()
    {
        $this->simulationOrchestrator->shouldReceive('playNextWeek')->once();

        $response = $this->controller->playNextWeek();

        $this->assertEquals(route('simulation.index'), $response->getTargetUrl());
    }

    /** @test */
    public function reset_calls_orchestrator_and_redirects_to_teams_index()
    {
        $this->simulationOrchestrator->shouldReceive('reset')->once();

        $response = $this->controller->reset();

        $this->assertEquals(route('teams.index'), $response->getTargetUrl());
    }
}
