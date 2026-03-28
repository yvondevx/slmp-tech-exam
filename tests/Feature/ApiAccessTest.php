<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_token(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('secret'),
            'api_token' => hash('sha256', 'secret-token'),
        ]);

        $response = $this->postJson('/api/login', ['email' => 'admin@example.com', 'password' => 'secret']);

        $response->assertStatus(200);
        $response->assertJson(['token' => $user->api_token]);
    }

    public function test_api_protected_routes_require_token(): void
    {
        $user = User::factory()->create([
            'api_token' => hash('sha256', 'secret-token'),
        ]);

        $response = $this->withHeaders(['Authorization' => 'Bearer secret-token'])->getJson('/api/users');
        $response->assertStatus(200);
    }
}
