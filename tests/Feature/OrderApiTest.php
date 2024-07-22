<?php
namespace Tests\Feature;

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_validates_order_request_invalid_price()
    {
        $response = $this->postJson('/api/orders', [
            'id' => 'A0000001',
            'name' => 'melody holiday inn',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road'
            ],
            'price' => 'ABC',
            'currency' => 'EUR'
        ]);

        // laravel return 422 status code when validation fails
        $response->assertStatus(422);
    }

    /** @test */
    public function it_validates_order_request_missing_name()
    {
        $response = $this->postJson('/api/orders', [
            'id' => 'A0000001',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road'
            ],
            'price' => 500,
            'currency' => 'EUR'
        ]);

        // laravel return 422 status code when validation fails
        $response->assertStatus(422);
    }

    /** @test */
    public function it_processes_order_successfully()
    {
        $response = $this->postJson('/api/orders', [
            'id' => 'A0000001',
            'name' => 'Melody Holiday Inn',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road'
            ],
            'price' => '1500',
            'currency' => 'USD'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => 'A0000001',
                'name' => 'Melody Holiday Inn',
                'address' => [
                    'city' => 'taipei-city',
                    'district' => 'da-an-district',
                    'street' => 'fuxing-south-road'
                ],
                'price' => 46500,
                'currency' => 'TWD'
            ]);
    }
}
