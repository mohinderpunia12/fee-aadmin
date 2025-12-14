<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that login endpoint is rate limited.
     */
    public function test_login_endpoint_is_rate_limited(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->forSchool($school)->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Make 5 requests (should succeed)
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/auth/login', [
                'email' => 'wrong@example.com',
                'password' => 'wrongpassword',
            ]);
            
            // All should return 422 (validation error, not rate limit)
            $response->assertStatus(422);
        }

        // 6th request should be rate limited
        $response = $this->postJson('/api/auth/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(429); // Too Many Requests
    }

    /**
     * Test that register endpoint is rate limited.
     */
    public function test_register_endpoint_is_rate_limited(): void
    {
        // Make 5 requests (should succeed)
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/auth/register', [
                'school_name' => 'Test School ' . $i,
                'school_slug' => 'test-school-' . $i,
                'admin_name' => 'Admin',
                'admin_email' => 'admin' . $i . '@example.com',
                'admin_password' => 'password123',
                'admin_password_confirmation' => 'password123',
            ]);
            
            // Should succeed (201 or 200)
            $this->assertContains($response->status(), [200, 201]);
        }

        // 6th request should be rate limited
        $response = $this->postJson('/api/auth/register', [
            'school_name' => 'Test School 6',
            'school_slug' => 'test-school-6',
            'admin_name' => 'Admin',
            'admin_email' => 'admin6@example.com',
            'admin_password' => 'password123',
            'admin_password_confirmation' => 'password123',
        ]);

        $response->assertStatus(429); // Too Many Requests
    }
}

