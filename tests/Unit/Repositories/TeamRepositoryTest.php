<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Team;
use Mockery;
use App\Repositories\TeamRepository;
use Illuminate\Database\Eloquent\Collection;

class TeamRepositoryTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_returns_team_collection()
    {
        // Fake return collection
        $fakeTeams = new Collection([
            new Team(['name' => 'Team A']),
            new Team(['name' => 'Team B']),
        ]);

        // Mock the Team model instance
        $teamModelMock = Mockery::mock(Team::class);
        $teamModelMock->shouldReceive('all')
                      ->once()
                      ->andReturn($fakeTeams);

        // Inject mock into repository
        $repo = new TeamRepository($teamModelMock);

        // Act
        $result = $repo->getAll();

        // Assertions
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals('Team A', $result[0]->name);
        $this->assertEquals('Team B', $result[1]->name);
    }
}
