<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\TeamService;
use App\Repositories\TeamRepository;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Mockery;

class TeamServiceTest extends TestCase
{
    public function test_get_all_returns_teams()
    {
        // 1. Arrange
        // Create fake team models in memory (not saved)
        $team1 = new Team(['name' => 'Team A']);
        $team2 = new Team(['name' => 'Team B']);

        $fakeCollection = new Collection([$team1, $team2]);

        // Mock TeamRepository
        $repositoryMock = Mockery::mock(TeamRepository::class);
        $repositoryMock->shouldReceive('getAll')
            ->once()
            ->andReturn($fakeCollection);

        // Inject mock into service
        $service = new TeamService($repositoryMock);

        // 2. Act
        $result = $service->getAllTeams();

        // 3. Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals('Team A', $result[0]->name);
        $this->assertEquals('Team B', $result[1]->name);
    }
}
