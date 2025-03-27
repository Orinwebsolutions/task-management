<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_register_with_valid_data(): void
    {
        $password = 'Password123!';
        $userData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $password,
            'password_confirmation' => $password
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'token'
            ]);
    }

    public function test_user_cannot_register_with_existing_email(): void
    {
        $password = 'Password123!';
        $email = $this->faker->unique()->safeEmail();
        User::factory()->create([
            'email' => $email
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'Another User',
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
    public function test_user_cannot_login_with_incorrect_password(): void
    {
        // Create a user
        $email = $this->faker->unique()->safeEmail();
        $user = User::factory()->create([
            'email' => $email,
            'password' => bcrypt('Password123!')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $email,
            'password' => 'WrongPassword123!'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials'
            ]);
    }

    public function test_unauthenticated_user_cannot_access_protected_route(): void
    {
        $response = $this->getJson('/api/tasks');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

}