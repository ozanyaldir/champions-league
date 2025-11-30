<?php

namespace Tests\Unit;

use App\Models\Team;
use App\Repositories\TeamRepository;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Mockery;
use Tests\TestCase;

class TeamRepositoryTest extends TestCase
{
    protected $model;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = Mockery::mock(Team::class);
        $this->repository = new TeamRepository($this->model);
    }

    /** @test */
    public function it_returns_all_teams()
    {
        $teams = new EloquentCollection([
            (object) ['id' => 1, 'name' => 'Team A'],
            (object) ['id' => 2, 'name' => 'Team B'],
        ]);

        $this->model
            ->shouldReceive('all')
            ->once()
            ->andReturn($teams);

        $result = $this->repository->getAll();

        $this->assertInstanceOf(EloquentCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals('Team A', $result[0]->name);
    }
}
