<?php

namespace Tests\Unit;

use App\Models\Team;
use App\Repositories\TeamRepository;
use App\Services\TeamService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class TeamServiceTest extends TestCase
{
    public function test_get_all_returns_teams()
    {
        $team1 = new Team(['name' => 'Team A']);
        $team2 = new Team(['name' => 'Team B']);

        $fakeCollection = new Collection([$team1, $team2]);

        $repositoryMock = Mockery::mock(TeamRepository::class);
        $repositoryMock->shouldReceive('getAll')
            ->once()
            ->andReturn($fakeCollection);

        $service = new TeamService($repositoryMock);

        $result = $service->getAllTeams();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals('Team A', $result[0]->name);
        $this->assertEquals('Team B', $result[1]->name);
    }
}
