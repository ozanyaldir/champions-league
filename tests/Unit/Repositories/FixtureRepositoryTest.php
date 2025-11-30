<?php

namespace Tests\Unit;

use App\Models\Fixture;
use App\Repositories\FixtureRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Mockery;
use Tests\TestCase;

class FixtureRepositoryTest extends TestCase
{
    protected $model;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = Mockery::mock(Fixture::class);
        $this->repository = new FixtureRepository($this->model);
    }

    /** @test */
    public function it_fetches_all_fixtures_with_teams_ordered_by_week()
    {
        $queryMock = Mockery::mock(Builder::class);

        $fixtures = new EloquentCollection([
            (object) ['week' => 1, 'home_team_id' => 1, 'away_team_id' => 2],
            (object) ['week' => 2, 'home_team_id' => 3, 'away_team_id' => 4],
        ]);

        $this->model
            ->shouldReceive('with')
            ->once()
            ->with(['homeTeam', 'awayTeam'])
            ->andReturn($queryMock);

        $queryMock
            ->shouldReceive('orderBy')
            ->once()
            ->with('week')
            ->andReturn($queryMock);

        $queryMock
            ->shouldReceive('get')
            ->once()
            ->andReturn($fixtures);

        $result = $this->repository->allWithTeams();

        $this->assertInstanceOf(EloquentCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]->week);
    }

    /** @test */
    public function it_deletes_all_fixtures()
    {
        $queryMock = Mockery::mock(Builder::class);

        $this->model
            ->shouldReceive('query')
            ->once()
            ->andReturn($queryMock);

        $queryMock
            ->shouldReceive('delete')
            ->once();

        $this->repository->deleteAll();
    }

    /** @test */
    public function it_inserts_multiple_fixtures()
    {
        $data = [
            ['home_team_id' => 1, 'away_team_id' => 2, 'week' => 1],
            ['home_team_id' => 3, 'away_team_id' => 4, 'week' => 1],
        ];

        $this->model
            ->shouldReceive('insert')
            ->once()
            ->with($data);

        $this->repository->insertMany($data);
    }
}
