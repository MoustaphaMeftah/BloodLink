<?php

namespace Database\Factories;

use App\Models\Donor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DonorFactory extends Factory
{
    protected $model = Donor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'blood_type' => fake()->randomElement(['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-']),
            'city' => fake()->city(),
            'availability' => true,
            'last_donation_date' => null,
            'medical_history' => null,
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'contact_verified' => true,
        ];
    }

    public function donor(): static
    {
        return $this->state(fn (array $attributes) => [
            'availability' => true,
        ]);
    }
}
