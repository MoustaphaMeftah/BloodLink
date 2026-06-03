<?php

namespace Database\Seeders;

use App\Models\BloodRequest;
use Illuminate\Database\Seeder;

class BloodRequestSeeder extends Seeder
{
    public function run(): void
    {
        BloodRequest::create([
            'hospital_id' => 1,
            'blood_type' => 'A+',
            'quantity' => 5,
            'urgency' => 'high',
            'location' => 'Fes',
            'status' => 'open'
        ]);
    }
}
