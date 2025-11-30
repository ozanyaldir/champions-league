<?php

namespace Tests\Unit;

use App\Http\Controllers\FixtureController;
use App\Services\FixtureService;
use Mockery;
use Tests\TestCase;

class FixtureControllerTest extends TestCase
{
    protected $fixtureService;

    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtureService = Mockery::mock(FixtureService::class);
        $this->controller = new FixtureController($this->fixtureService);
    }

    /** @test */
    public function index_calls_get_fixtures_grouped_by_week_and_returns_view()
    {
        $weeks = [
            1 => [
                (object) ['id' => 1, 'homeTeam' => (object) ['name' => 'A'], 'awayTeam' => (object) ['name' => 'B']],
            ],
        ];

        $this->fixtureService
            ->shouldReceive('getFixturesGroupedByWeek')
            ->once()
            ->andReturn($weeks);

        $response = $this->controller->index();

        $this->assertEquals('fixtures', $response->name());
        $this->assertArrayHasKey('weeks', $response->getData());
        $this->assertEquals($weeks, $response->getData()['weeks']);
    }

    /** @test */
    public function generate_calls_generate_fixtures_and_redirects()
    {
        $this->fixtureService
            ->shouldReceive('generateFixtures')
            ->once();

        $response = $this->controller->generate();

        $this->assertEquals(route('fixtures.index'), $response->getTargetUrl());
    }
}
