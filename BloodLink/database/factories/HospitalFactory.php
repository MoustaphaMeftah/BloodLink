<?php

namespace Database\Factories;

use App\Models\Hospital;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class HospitalFactory extends Factory
{
    protected $model = Hospital::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->company() . ' Hospital',
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'contact_person' => fake()->name(),
        ];
    }
}
