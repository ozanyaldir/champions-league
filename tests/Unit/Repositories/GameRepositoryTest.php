<?php

namespace Tests\Unit;

use App\Models\Game;
use App\Repositories\GameRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Mockery;
use Tests\TestCase;

class GameRepositoryTest extends TestCase
{
    protected $model;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = Mockery::mock(Game::class);
        $this->repository = new GameRepository($this->model);
    }

    /** @test */
    public function it_returns_all_games()
    {
        $queryMock = Mockery::mock(Builder::class);

        $games = new EloquentCollection([
            (object) ['id' => 1, 'fixture_id' => 1],
            (object) ['id' => 2, 'fixture_id' => 2],
        ]);

        $this->model
            ->shouldReceive('query')
            ->once()
            ->andReturn($queryMock);

        $queryMock
            ->shouldReceive('get')
            ->once()
            ->andReturn($games);

        $result = $this->repository->getAll();

        $this->assertInstanceOf(EloquentCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]->fixture_id);
    }

    /** @test */
    public function it_returns_played_fixture_ids()
    {
        $this->model
            ->shouldReceive('pluck')
            ->once()
            ->with('fixture_id')
            ->andReturn(collect([1, 2, 3]));

        $result = $this->repository->getPlayedFixtureIds();

        $this->assertIsArray($result);
        $this->assertEquals([1, 2, 3], $result);
    }

    /** @test */
    public function it_deletes_all_games()
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
    public function it_creates_a_game()
    {
        $data = ['fixture_id' => 1, 'home_goals' => 2, 'away_goals' => 1];

        $gameMock = Mockery::mock(Game::class);

        $this->model
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($gameMock);

        $result = $this->repository->create($data);

        $this->assertSame($gameMock, $result);
    }
}
