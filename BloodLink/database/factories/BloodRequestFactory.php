<?php

namespace Database\Factories;

use App\Models\BloodRequest;
use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;

class BloodRequestFactory extends Factory
{
    protected $model = BloodRequest::class;

    public function definition(): array
    {
        return [
            'hospital_id' => Hospital::factory(),
            'blood_type' => fake()->randomElement(['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-']),
            'quantity' => fake()->numberBetween(100, 5000),
            'urgency' => fake()->randomElement(['low', 'medium', 'high', 'critical']),
            'status' => 'open',
            'location' => fake()->city(),
        ];
    }
}
