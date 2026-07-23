<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BasicFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_register()
    {
        $this->assertGuest();

        $response = $this->post('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'testregister@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['user', 'token'],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'testregister@example.com',
        ]);
    }

    public function test_can_login()
    {
        $this->assertGuest();
        $user = User::factory()->create([
            'password' => Hash::make('12345678'),
        ]);

        $response = $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => '12345678',
        ]);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['token']]);
    }

    public function test_user_cannot_login_with_incorrect_password()
    {
        $this->assertGuest();
        $user = User::factory()->create([
            'password' => Hash::make('12345678'),
        ]);

        $response = $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => 'invalid-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

    }

    public function test_can_get_profile_me()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'name', 'email'],
            ]);
    }
}
