<?php

namespace Database\Factories;

use App\Models\DonorResponse;
use App\Models\Donor;
use App\Models\BloodRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class DonorResponseFactory extends Factory
{
    protected $model = DonorResponse::class;

    public function definition(): array
    {
        return [
            'donor_id' => Donor::factory(),
            'blood_request_id' => BloodRequest::factory(),
            'status' => fake()->randomElement(['pending', 'accepted', 'rejected']),
            'response_date' => now(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
