<?php

namespace Tests\Feature\Routes;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Team;
use App\Models\Fixture;

class SimulationRoutesTest extends TestCase
{
    use RefreshDatabase;

    private function prepareSimulationData(): void
    {
        Team::factory()->count(8)->create();

        foreach (range(1, 7) as $week) {
            Fixture::factory()->create([
                'week' => $week,
            ]);
        }
    }

    /** @test */
    public function simulation_index_route_loads_successfully()
    {
        $response = $this->get('/simulation');

        $response->assertStatus(200);
        $response->assertViewIs('simulation');
    }

    /** @test */
    public function simulation_start_route_works()
    {
        $this->prepareSimulationData();

        $response = $this->post('/simulation/start');

        $response->assertStatus(302);
        $response->assertRedirect(route('simulation.index'));
    }

    /** @test */
    public function simulation_play_all_route_works()
    {
        $this->prepareSimulationData();

        $response = $this->post('/simulation/play-all');

        $response->assertStatus(302);
        $response->assertRedirect(route('simulation.index'));
    }

    /** @test */
    public function simulation_play_next_week_route_works()
    {
        $this->prepareSimulationData();

        $response = $this->post('/simulation/play-next-week');

        $response->assertStatus(302);
        $response->assertRedirect(route('simulation.index'));
    }

    /** @test */
    public function simulation_reset_route_works()
    {
        $this->prepareSimulationData();

        $response = $this->post('/simulation/reset');

        $response->assertStatus(302);
        $response->assertRedirect(route('teams.index'));
    }
}
