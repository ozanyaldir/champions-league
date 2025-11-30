<?php

namespace Tests\Unit;

use App\Models\Fixture;
use App\Orchestrators\SimulationOrchestrator;
use App\Services\ChampionshipPredictorService;
use App\Services\FixtureService;
use App\Services\LeagueTableBuilderService;
use App\Services\SimulationService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Mockery;
use Tests\TestCase;

class SimulationOrchestratorTest extends TestCase
{
    protected $fixtureService;

    protected $simulationService;

    protected $championshipPredictorService;

    protected $leagueTableBuilderService;

    protected $orchestrator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtureService = Mockery::mock(FixtureService::class);
        $this->simulationService = Mockery::mock(SimulationService::class);
        $this->championshipPredictorService = Mockery::mock(ChampionshipPredictorService::class);
        $this->leagueTableBuilderService = Mockery::mock(LeagueTableBuilderService::class);

        $this->orchestrator = new SimulationOrchestrator(
            $this->fixtureService,
            $this->simulationService,
            $this->championshipPredictorService,
            $this->leagueTableBuilderService
        );
    }

    /** @test */
    public function it_resets_simulation()
    {
        $this->simulationService->shouldReceive('reset')->once();

        $this->orchestrator->reset();
    }

    /** @test */
    public function it_plays_all_weeks()
    {
        $this->simulationService->shouldReceive('playAll')->once();

        $this->orchestrator->playAll();
    }

    /** @test */
    public function it_plays_next_week_and_returns_week_number()
    {
        $this->simulationService
            ->shouldReceive('playNextWeek')
            ->once()
            ->andReturn(5);

        $week = $this->orchestrator->playNextWeek();

        $this->assertEquals(5, $week);
    }

    /** @test */
    public function it_builds_league_table()
    {
        $table = new EloquentCollection([
            (object) ['team' => 'A', 'points' => 10],
            (object) ['team' => 'B', 'points' => 8],
        ]);

        $this->leagueTableBuilderService
            ->shouldReceive('buildLeagueTable')
            ->once()
            ->andReturn($table);

        $result = $this->orchestrator->buildLeagueTable();

        $this->assertInstanceOf(EloquentCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals('A', $result[0]->team);
    }

    /** @test */
    public function it_gets_current_week_fixtures()
    {
        $fixtures = [
            1 => [
                new Fixture(['home_team_id' => 1, 'away_team_id' => 2, 'week' => 1]),
                new Fixture(['home_team_id' => 3, 'away_team_id' => 4, 'week' => 1]),
            ],
        ];

        $this->fixtureService
            ->shouldReceive('getCurrentWeekFixtures')
            ->once()
            ->andReturn($fixtures);

        $result = $this->orchestrator->getCurrentWeekFixtures();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertCount(2, $result[1]);
        $this->assertInstanceOf(Fixture::class, $result[1][0]);
    }

    /** @test */
    public function it_predicts_championship()
    {
        $prediction = [
            ['team' => 'A', 'probability' => 0.4],
            ['team' => 'B', 'probability' => 0.3],
        ];

        $this->championshipPredictorService
            ->shouldReceive('predictChampionship')
            ->once()
            ->andReturn($prediction);

        $result = $this->orchestrator->predictChampionship();

        $this->assertIsArray($result);
        $this->assertEquals(0.4, $result[0]['probability']);
    }
}
