<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@bloodlink.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '0600000000',
            'city' => 'Fes',
        ]);

        User::create([
            'name' => 'Hospital User',
            'email' => 'hospital@bloodlink.com',
            'password' => Hash::make('password'),
            'role' => 'hospital',
            'phone' => '0611111111',
            'city' => 'Fes',
        ]);

        User::create([
            'name' => 'Donor User',
            'email' => 'donor@bloodlink.com',
            'password' => Hash::make('password'),
            'role' => 'donor',
            'phone' => '0622222222',
            'city' => 'Fes',
        ]);
    }
}
