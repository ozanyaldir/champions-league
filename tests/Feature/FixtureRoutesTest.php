<?php

namespace Tests\Feature\Routes;

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FixtureRoutesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function fixtures_index_route_loads_successfully()
    {
        $response = $this->get('/fixtures');

        $response->assertStatus(200);
        $response->assertViewIs('fixtures');
    }

    /** @test */
    public function fixtures_generate_route_works()
    {
        Team::factory()->count(8)->create();

        $response = $this->post('/fixtures/generate');

        $response->assertStatus(302);
        $response->assertRedirect(route('fixtures.index'));
    }
}
