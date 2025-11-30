<?php

namespace Tests\Unit;

use App\Repositories\TeamRepository;
use App\Services\TeamService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Mockery;
use Tests\TestCase;

class TeamServiceTest extends TestCase
{
    protected $teamRepository;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teamRepository = Mockery::mock(TeamRepository::class);
        $this->service = new TeamService($this->teamRepository);
    }

    /** @test */
    public function it_returns_all_teams()
    {
        $teams = new EloquentCollection([
            (object) ['id' => 1, 'name' => 'Team A'],
            (object) ['id' => 2, 'name' => 'Team B'],
        ]);

        $this->teamRepository
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($teams);

        $result = $this->service->getAllTeams();

        $this->assertInstanceOf(EloquentCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals('Team A', $result[0]->name);
        $this->assertEquals('Team B', $result[1]->name);
    }
}
