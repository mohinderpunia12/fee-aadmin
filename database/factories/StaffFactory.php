<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Staff>
 */
class StaffFactory extends Factory
{
    protected $model = Staff::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->optional()->safeEmail(),
            'role' => $this->faker->randomElement(['Teacher', 'Principal', 'Accountant', 'Admin']),
            'salary' => $this->faker->numberBetween(20000, 100000),
            'badge_number' => $this->faker->unique()->numerify('BADGE####'),
        ];
    }

    /**
     * Associate the staff with a specific school.
     */
    public function forSchool(School $school): static
    {
        return $this->state(fn (array $attributes) => [
            'school_id' => $school->id,
        ]);
    }
}

