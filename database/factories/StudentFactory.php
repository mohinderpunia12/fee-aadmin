<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

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
            'enrollment_no' => $this->faker->unique()->numerify('ENR####'),
            'class' => $this->faker->randomElement(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12']),
            'section' => $this->faker->randomElement(['A', 'B', 'C', null]),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'address' => $this->faker->optional()->address(),
            'parent_name' => $this->faker->name(),
            'parent_phone' => $this->faker->phoneNumber(),
            'parent_phone_secondary' => $this->faker->optional()->phoneNumber(),
        ];
    }

    /**
     * Associate the student with a specific school.
     */
    public function forSchool(School $school): static
    {
        return $this->state(fn (array $attributes) => [
            'school_id' => $school->id,
        ]);
    }
}

