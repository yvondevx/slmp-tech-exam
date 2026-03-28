<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_protected_routes_require_api_key(): void
    {
        config(['app.api_key' => 'test-api-key']);

        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }

    public function test_api_protected_routes_allow_valid_api_key(): void
    {
        config(['app.api_key' => 'test-api-key']);

        $response = $this->withHeaders(['X-API-KEY' => 'test-api-key'])->getJson('/api/users');
        $response->assertStatus(200);
    }
}
