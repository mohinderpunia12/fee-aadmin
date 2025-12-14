<?php

namespace Database\Factories;

use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\School>
 */
class SchoolFactory extends Factory
{
    protected $model = School::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->company() . ' School';

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'domain' => $this->faker->optional()->url(),
            'support_email' => $this->faker->optional()->companyEmail(),
            'support_phone' => $this->faker->optional()->phoneNumber(),
            'support_address' => $this->faker->optional()->address(),
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addDays(7),
        ];
    }

    /**
     * Indicate that the school has an active subscription.
     */
    public function withActiveSubscription(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_status' => 'active',
            'subscription_expires_at' => now()->addMonths(1),
            'trial_ends_at' => null,
        ]);
    }

    /**
     * Indicate that the school's subscription has expired.
     */
    public function withExpiredSubscription(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_status' => 'expired',
            'subscription_expires_at' => now()->subDays(1),
            'trial_ends_at' => null,
        ]);
    }
}

