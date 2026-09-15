<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\App\Auth\AuthUser;
use Illuminate\Support\Facades\Hash;

class ExampleFeatureTest extends FeatureTestCase
{
    /**
     * A basic feature test example.
     */
    public function test_access_api(): void
    {
        $response = $this->getJson('/api/healthcheck');

        $response->assertStatus(200);
    }

    /**
     * A basic feature test with DB example.
     */
    public function test_create_user_in_db(): void
    {
        $user = AuthUser::factory()->create([
            'name' => 'Test User',
            'password' => Hash::make('password123'),
            'email' => 'test@example.com',
            'google_id' => 'dummy-google-id',
        ]);
        $this->assertDatabaseHas('auth_user', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
