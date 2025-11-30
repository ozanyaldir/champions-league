<?php

namespace Tests\Unit;

use App\Repositories\FixtureRepository;
use App\Repositories\GameRepository;
use App\Repositories\TeamRepository;
use App\Services\FixtureService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Mockery;
use Tests\TestCase;

class FixtureServiceTest extends TestCase
{
    protected $fixtureRepository;

    protected $gameRepository;

    protected $teamRepository;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtureRepository = Mockery::mock(FixtureRepository::class);
        $this->gameRepository = Mockery::mock(GameRepository::class);
        $this->teamRepository = Mockery::mock(TeamRepository::class);

        $this->service = new FixtureService(
            $this->fixtureRepository,
            $this->gameRepository,
            $this->teamRepository
        );
    }

    /** @test */
    public function it_resets_simulation_data()
    {
        $this->gameRepository->shouldReceive('deleteAll')->once();
        $this->fixtureRepository->shouldReceive('deleteAll')->once();

        $this->service->resetSimulationData();
    }

    /** @test */
    public function it_fetches_and_shuffles_team_ids()
    {
        $teams = new EloquentCollection([
            (object) ['id' => 1],
            (object) ['id' => 2],
            (object) ['id' => 3],
        ]);

        $this->teamRepository
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($teams);

        $ids = $this->invokeMethod($this->service, 'fetchAndShuffleTeamIds');

        $this->assertCount(3, $ids);
        $this->assertEqualsCanonicalizing([1, 2, 3], $ids);
    }

    /** @test */
    public function it_builds_groups_evenly()
    {
        $groups = $this->invokeMethod(
            $this->service,
            'buildGroups',
            [[1, 2, 3, 4, 5, 6, 7, 8], 4]
        );

        $this->assertCount(2, $groups);
        $this->assertEquals([1, 2, 3, 4], $groups[0]);
        $this->assertEquals([5, 6, 7, 8], $groups[1]);
    }

    /** @test */
    public function it_builds_single_group_when_not_even()
    {
        $groups = $this->invokeMethod(
            $this->service,
            'buildGroups',
            [[1, 2, 3, 4, 5], 4]
        );

        $this->assertCount(1, $groups);
        $this->assertEquals([1, 2, 3, 4, 5], $groups[0]);
    }

    /** @test */
    public function it_generates_round_robin_fixtures_single_leg()
    {
        $fixtures = $this->invokeMethod(
            $this->service,
            'roundRobinFixtures',
            [[1, 2, 3, 4], 1, false, 2]
        );

        $this->assertCount(6, $fixtures);

        $weeks = array_column($fixtures, 'week');
        $this->assertContains(1, $weeks);
        $this->assertContains(2, $weeks);
        $this->assertContains(3, $weeks);
    }

    /** @test */
    public function it_generates_round_robin_fixtures_double_leg()
    {
        $fixtures = $this->invokeMethod(
            $this->service,
            'roundRobinFixtures',
            [[1, 2, 3, 4], 1, true, 2]
        );

        $this->assertCount(12, $fixtures);
    }

    /** @test */
    public function it_adds_bye_if_odd_team_count()
    {
        $result = $this->invokeMethod(
            $this->service,
            'ensureEvenTeams',
            [[1, 2, 3]]
        );

        $this->assertCount(4, $result);
        $this->assertContains(null, $result);
    }

    /** @test */
    public function it_calculates_next_group_week_correctly()
    {
        $fixtures = [
            ['week' => 2],
            ['week' => 2],
            ['week' => 3],
        ];

        $next = $this->invokeMethod(
            $this->service,
            'calculateNextGroupWeek',
            [$fixtures]
        );

        $this->assertEquals(4, $next);
    }

    /** @test */
    public function it_generates_all_group_fixtures()
    {
        $groups = [
            [1, 2, 3, 4],
            [5, 6, 7, 8],
        ];

        $fixtures = $this->invokeMethod(
            $this->service,
            'generateAllGroupFixtures',
            [$groups, 2, true]
        );

        $this->assertCount(24, $fixtures);
    }

    /** @test */
    public function it_inserts_fixtures_when_generating()
    {
        $teams = new EloquentCollection([
            (object) ['id' => 1],
            (object) ['id' => 2],
            (object) ['id' => 3],
            (object) ['id' => 4],
        ]);

        $this->teamRepository
            ->shouldReceive('getAll')
            ->andReturn($teams);

        $this->gameRepository->shouldReceive('deleteAll')->once();
        $this->fixtureRepository->shouldReceive('deleteAll')->once();

        $this->fixtureRepository->shouldReceive('insertMany')
            ->once()
            ->with(Mockery::type('array'));

        $this->service->generateFixtures();
    }

    /** @test */
    public function it_groups_fixtures_by_week()
    {
        $fixtures = new EloquentCollection([
            (object) [
                'week' => 1,
                'homeTeam' => (object) ['name' => 'A'],
                'awayTeam' => (object) ['name' => 'B'],
            ],
            (object) [
                'week' => 1,
                'homeTeam' => (object) ['name' => 'C'],
                'awayTeam' => null,
            ],
            (object) [
                'week' => 2,
                'homeTeam' => (object) ['name' => 'D'],
                'awayTeam' => (object) ['name' => 'E'],
            ],
        ]);

        $this->fixtureRepository
            ->shouldReceive('allWithTeams')
            ->once()
            ->andReturn($fixtures);

        $weeks = $this->service->getFixturesGroupedByWeek();

        $this->assertCount(2, $weeks);
        $this->assertCount(2, $weeks[1]);
        $this->assertEquals('Bye', $weeks[1][1]['away']);
    }

    protected function invokeMethod($object, string $method, array $params = [])
    {
        $ref = new \ReflectionClass($object);
        $m = $ref->getMethod($method);
        $m->setAccessible(true);

        return $m->invokeArgs($object, $params);
    }
}
