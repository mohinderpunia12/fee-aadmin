<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a user can register a new school via API.
     */
    public function test_user_can_register_school_via_api(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'school_name' => 'Test School',
            'school_slug' => 'test-school',
            'admin_name' => 'John Doe',
            'admin_email' => 'admin@testschool.com',
            'admin_password' => 'password123',
            'admin_password_confirmation' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user',
                'school',
            ]);

        $this->assertDatabaseHas('schools', [
            'name' => 'Test School',
            'slug' => 'test-school',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@testschool.com',
            'role' => User::ROLE_SCHOOL_ADMIN,
        ]);
    }

    /**
     * Test that a user can login via API.
     */
    public function test_user_can_login_via_api(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->forSchool($school)->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user',
            ]);
    }

    /**
     * Test that login fails with invalid credentials.
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test that authenticated user can access profile.
     */
    public function test_authenticated_user_can_access_profile(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->forSchool($school)->create();

        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/auth/profile');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'email' => $user->email,
            ]);
    }

    /**
     * Test that unauthenticated user cannot access profile.
     */
    public function test_unauthenticated_user_cannot_access_profile(): void
    {
        $response = $this->getJson('/api/auth/profile');

        $response->assertStatus(401);
    }
}

