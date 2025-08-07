<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotelSearchTest extends TestCase
{
    /** @test */
    public function it_returns_successful_response()
    {
        $response = $this->getJson('/api/hotels/search?location=Paris&check_in=2025-08-10&check_out=2025-08-15');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['name', 'location', 'price', 'rating', 'supplier']
                 ]);
    }
}
