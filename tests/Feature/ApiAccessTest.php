<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Company;
use App\Models\User;
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

    public function test_users_endpoint_includes_related_address_and_company(): void
    {
        config(['app.api_key' => 'test-api-key']);

        $user = User::create([
            'external_id' => 1,
            'name' => 'Leanne Graham',
            'username' => 'Bret',
            'email' => 'sincere@april.biz',
            'phone' => '1-770-736-8031 x56442',
            'website' => 'hildegard.org',
            'password' => 'password',
            'api_token' => 'test-token',
        ]);

        Address::create([
            'user_id' => $user->id,
            'street' => 'Kulas Light',
            'suite' => 'Apt. 556',
            'city' => 'Gwenborough',
            'zipcode' => '92998-3874',
            'lat' => '-37.3159',
            'lng' => '81.1496',
        ]);

        Company::create([
            'user_id' => $user->id,
            'name' => 'Romaguera-Crona',
            'catch_phrase' => 'Multi-layered client-server neural-net',
            'bs' => 'harness real-time e-markets',
        ]);

        $response = $this->withHeaders(['X-API-KEY' => 'test-api-key'])->getJson('/api/users');

        $response
            ->assertOk()
            ->assertJsonPath('0.username', 'Bret')
            ->assertJsonPath('0.phone', '1-770-736-8031 x56442')
            ->assertJsonPath('0.website', 'hildegard.org')
            ->assertJsonPath('0.address.street', 'Kulas Light')
            ->assertJsonPath('0.address.lat', '-37.3159')
            ->assertJsonPath('0.company.name', 'Romaguera-Crona')
            ->assertJsonPath('0.company.catch_phrase', 'Multi-layered client-server neural-net');
    }
}
