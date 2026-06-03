<?php

namespace Database\Seeders;

use App\Models\Hospital;
use Illuminate\Database\Seeder;

class HospitalSeeder extends Seeder
{
    public function run(): void
    {
        Hospital::create([
            'user_id' => 2,
            'name' => 'CHU Hassan II',
            'address' => 'Route Sidi Harazem, Fes',
            'phone' => '0535000000',
            'contact_person' => 'Dr Ahmed'
        ]);
    }
}
