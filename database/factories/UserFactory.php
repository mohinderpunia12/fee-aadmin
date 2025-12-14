<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'school_id' => null,
            'role' => User::ROLE_SCHOOL_ADMIN,
        ];
    }

    /**
     * Indicate that the user is a superuser.
     */
    public function superuser(): static
    {
        return $this->state(fn (array $attributes) => [
            'school_id' => null,
            'role' => User::ROLE_SUPERUSER,
        ]);
    }

    /**
     * Indicate that the user is a school admin.
     */
    public function schoolAdmin(): static
    {
        return $this->state(function (array $attributes) {
            $school = School::factory()->create();

            return [
                'school_id' => $school->id,
                'role' => User::ROLE_SCHOOL_ADMIN,
            ];
        });
    }

    /**
     * Indicate that the user is staff.
     */
    public function staff(): static
    {
        return $this->state(function (array $attributes) {
            $school = School::factory()->create();

            return [
                'school_id' => $school->id,
                'role' => User::ROLE_STAFF,
            ];
        });
    }

    /**
     * Indicate that the user is a student.
     */
    public function student(): static
    {
        return $this->state(function (array $attributes) {
            $school = School::factory()->create();

            return [
                'school_id' => $school->id,
                'role' => User::ROLE_STUDENT,
            ];
        });
    }

    /**
     * Associate the user with a specific school.
     */
    public function forSchool(School $school): static
    {
        return $this->state(fn (array $attributes) => [
            'school_id' => $school->id,
        ]);
    }

    /**
     * Indicate that the user's email address is unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

