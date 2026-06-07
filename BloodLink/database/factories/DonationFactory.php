<?php

namespace Database\Factories;

use App\Models\Donation;
use App\Models\Donor;
use App\Models\BloodRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class DonationFactory extends Factory
{
    protected $model = Donation::class;

    public function definition(): array
    {
        return [
            'donor_id' => Donor::factory(),
            'blood_request_id' => BloodRequest::factory(),
            'donation_date' => fake()->date(),
            'quantity' => fake()->numberBetween(200, 500),
        ];
    }
}
