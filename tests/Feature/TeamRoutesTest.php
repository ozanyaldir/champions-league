<?php

namespace Tests\Feature\Routes;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamRoutesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function teams_index_route_loads_successfully()
    {
        $response = $this->get('/teams');

        $response->assertStatus(200);
        $response->assertViewIs('teams');
    }

    /** @test */
    public function teams_index_displays_teams()
    {
        \App\Models\Team::factory()->count(3)->create();

        $response = $this->get('/teams');

        $response->assertStatus(200);
        $response->assertViewIs('teams');
        $response->assertSeeText($response->original->getData()['teams'][0]->name);
    }
}
