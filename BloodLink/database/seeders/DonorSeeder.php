<?php

namespace Database\Seeders;

use App\Models\Donor;
use Illuminate\Database\Seeder;

class DonorSeeder extends Seeder
{
    public function run(): void
    {
        Donor::create([
            'user_id' => 3,
            'blood_type' => 'A+',
            'city' => 'Fes',
            'availability' => true,
            'last_donation_date' => now()->subMonths(4),
            'medical_history' => 'Aucune maladie'
        ]);
    }
}
