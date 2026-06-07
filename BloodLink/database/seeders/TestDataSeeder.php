<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // ========== EXTRA USERS ==========
        $extraUsers = [
            ['name' => 'Dr. Fatima Zahra', 'email' => 'fatima@hopital.ma', 'role' => 'hospital', 'phone' => '0612345678', 'city' => 'Casablanca'],
            ['name' => 'Ahmed Benali', 'email' => 'ahmed.benali@email.com', 'role' => 'donor', 'phone' => '0623456789', 'city' => 'Casablanca'],
            ['name' => 'Sara El Amrani', 'email' => 'sara.elamrani@email.com', 'role' => 'donor', 'phone' => '0634567890', 'city' => 'Rabat'],
            ['name' => 'Omar Tazi', 'email' => 'omar.tazi@email.com', 'role' => 'donor', 'phone' => '0645678901', 'city' => 'Marrakech'],
            ['name' => 'Leila Bencheikh', 'email' => 'leila.bencheikh@email.com', 'role' => 'donor', 'phone' => '0656789012', 'city' => 'Fes'],
            ['name' => 'Youssef El Idrissi', 'email' => 'youssef.idrissi@email.com', 'role' => 'donor', 'phone' => '0667890123', 'city' => 'Tanger'],
            ['name' => 'Houda El Fassi', 'email' => 'houda.elfassi@email.com', 'role' => 'donor', 'phone' => '0678901234', 'city' => 'Oujda'],
            ['name' => 'CHU Ibn Rochd', 'email' => 'contact@chuirochd.ma', 'role' => 'hospital', 'phone' => '0522000001', 'city' => 'Casablanca'],
            ['name' => 'Hopital Cheikh Zaid', 'email' => 'contact@cheikhzaid.ma', 'role' => 'hospital', 'phone' => '0522000002', 'city' => 'Rabat'],
        ];

        $hospitalUsers = [];
        $donorUsers = [];
        foreach ($extraUsers as $u) {
            $existing = DB::table('users')->where('email', $u['email'])->first();
            if (! $existing) {
                $id = DB::table('users')->insertGetId([
                    'name' => $u['name'],
                    'email' => $u['email'],
                    'password' => Hash::make('password'),
                    'role' => $u['role'],
                    'phone' => $u['phone'],
                    'city' => $u['city'],
                    'email_verified_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                if ($u['role'] === 'hospital') {
                    $hospitalUsers[] = $id;
                }
                if ($u['role'] === 'donor') {
                    $donorUsers[] = $id;
                }
            } else {
                if ($existing->role === 'hospital') {
                    $hospitalUsers[] = $existing->id;
                }
                if ($existing->role === 'donor') {
                    $donorUsers[] = $existing->id;
                }
            }
        }

        // ========== EXTRA HOSPITALS ==========
        $extraHospitals = [
            ['user_id' => $hospitalUsers[0] ?? null, 'name' => 'Hopital Averroes', 'address' => 'Casablanca', 'phone' => '0522000010', 'contact_person' => 'Dr. Fatima Zahra'],
            ['user_id' => $hospitalUsers[1] ?? null, 'name' => 'CHU Ibn Rochd', 'address' => 'Casablanca', 'phone' => '0522000011', 'contact_person' => 'Admin CHU'],
            ['user_id' => $hospitalUsers[2] ?? null, 'name' => 'Hopital Cheikh Zaid', 'address' => 'Rabat', 'phone' => '0522000012', 'contact_person' => 'Admin Cheikh Zaid'],
        ];

        $hospitalIds = [];
        foreach ($extraHospitals as $h) {
            if (! $h['user_id']) {
                continue;
            }
            $existing = DB::table('hospitals')->where('user_id', $h['user_id'])->first();
            if (! $existing) {
                $hospitalIds[] = DB::table('hospitals')->insertGetId([
                    'user_id' => $h['user_id'],
                    'name' => $h['name'],
                    'address' => $h['address'],
                    'phone' => $h['phone'],
                    'contact_person' => $h['contact_person'],
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            } else {
                $hospitalIds[] = $existing->id;
            }
        }

        // Get all existing hospital IDs
        $allHospitalIds = DB::table('hospitals')->pluck('id')->toArray();
        if (empty($allHospitalIds)) {
            $this->command->error('No hospitals found. Run base seeders first.');

            return;
        }

        // ========== EXTRA DONORS ==========
        $donorBloodTypes = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
        $donorData = [
            ['user_id' => $donorUsers[0] ?? null, 'blood_type' => 'O-', 'city' => 'Casablanca', 'availability' => true],
            ['user_id' => $donorUsers[1] ?? null, 'blood_type' => 'A+', 'city' => 'Rabat', 'availability' => true],
            ['user_id' => $donorUsers[2] ?? null, 'blood_type' => 'B+', 'city' => 'Marrakech', 'availability' => true],
            ['user_id' => $donorUsers[3] ?? null, 'blood_type' => 'AB+', 'city' => 'Fes', 'availability' => false],
            ['user_id' => $donorUsers[4] ?? null, 'blood_type' => 'O+', 'city' => 'Tanger', 'availability' => true],
            ['user_id' => $donorUsers[5] ?? null, 'blood_type' => 'A-', 'city' => 'Oujda', 'availability' => true],
        ];

        $donorIds = [];
        foreach ($donorData as $d) {
            if (! $d['user_id']) {
                continue;
            }
            $existing = DB::table('donors')->where('user_id', $d['user_id'])->first();
            if (! $existing) {
                $donorIds[] = DB::table('donors')->insertGetId([
                    'user_id' => $d['user_id'],
                    'blood_type' => $d['blood_type'],
                    'city' => $d['city'],
                    'availability' => $d['availability'],
                    'last_donation_date' => $d['availability'] ? null : $now->copy()->subDays(30),
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            } else {
                $donorIds[] = $existing->id;
            }
        }

        // All existing donor IDs
        $allDonorIds = DB::table('donors')->pluck('id')->toArray();

        // ========== BLOOD REQUESTS ==========
        $bloodRequests = [
            ['hospital_id' => $allHospitalIds[0], 'blood_type' => 'O-', 'quantity' => 500, 'urgency' => 'critical', 'status' => 'open', 'location' => 'Casablanca'],
            ['hospital_id' => $allHospitalIds[0], 'blood_type' => 'A+', 'quantity' => 1000, 'urgency' => 'high', 'status' => 'open', 'location' => 'Casablanca'],
            ['hospital_id' => $allHospitalIds[1] ?? $allHospitalIds[0], 'blood_type' => 'B+', 'quantity' => 750, 'urgency' => 'medium', 'status' => 'open', 'location' => 'Rabat'],
            ['hospital_id' => $allHospitalIds[2] ?? $allHospitalIds[0], 'blood_type' => 'O+', 'quantity' => 400, 'urgency' => 'low', 'status' => 'open', 'location' => 'Marrakech'],
            ['hospital_id' => $allHospitalIds[1] ?? $allHospitalIds[0], 'blood_type' => 'AB+', 'quantity' => 300, 'urgency' => 'critical', 'status' => 'open', 'location' => 'Tanger'],
            ['hospital_id' => $allHospitalIds[0], 'blood_type' => 'A-', 'quantity' => 600, 'urgency' => 'high', 'status' => 'open', 'location' => 'Oujda'],
            ['hospital_id' => $allHospitalIds[2] ?? $allHospitalIds[0], 'blood_type' => 'O-', 'quantity' => 800, 'urgency' => 'high', 'status' => 'fulfilled', 'location' => 'Fes'],
            ['hospital_id' => $allHospitalIds[0], 'blood_type' => 'A+', 'quantity' => 450, 'urgency' => 'low', 'status' => 'cancelled', 'location' => 'Rabat'],
        ];

        $requestIds = [];
        foreach ($bloodRequests as $br) {
            DB::table('blood_requests')->insertGetId([
                'hospital_id' => $br['hospital_id'],
                'blood_type' => $br['blood_type'],
                'quantity' => $br['quantity'],
                'urgency' => $br['urgency'],
                'status' => $br['status'],
                'location' => $br['location'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $allRequestIds = DB::table('blood_requests')->pluck('id')->toArray();
        $openRequestIds = DB::table('blood_requests')->where('status', 'open')->pluck('id')->toArray();

        // ========== DONOR RESPONSES ==========
        if (! empty($allDonorIds) && ! empty($allRequestIds)) {
            foreach ($allDonorIds as $di) {
                $donor = DB::table('donors')->find($di);
                $compatibleReqs = DB::table('blood_requests')
                    ->where('blood_type', $donor->blood_type)
                    ->where('status', 'open')
                    ->get();
                foreach ($compatibleReqs as $cr) {
                    $exists = DB::table('donor_responses')
                        ->where('donor_id', $di)
                        ->where('blood_request_id', $cr->id)
                        ->exists();
                    if (! $exists) {
                        $accepted = rand(0, 3) > 0;
                        DB::table('donor_responses')->insert([
                            'donor_id' => $di,
                            'blood_request_id' => $cr->id,
                            'status' => $accepted ? 'accepted' : 'rejected',
                            'response_date' => $now,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                        // Also add pivot
                        $pivotExists = DB::table('blood_request_donor')
                            ->where('donor_id', $di)
                            ->where('blood_request_id', $cr->id)
                            ->exists();
                        if (! $pivotExists) {
                            DB::table('blood_request_donor')->insert([
                                'blood_request_id' => $cr->id,
                                'donor_id' => $di,
                                'status' => $accepted ? 'accepted' : 'rejected',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        }
                    }
                }
            }
        }

        // ========== DONATIONS ==========
        if (! empty($allDonorIds)) {
            foreach ($allDonorIds as $di) {
                $acceptedReqs = DB::table('donor_responses')
                    ->where('donor_id', $di)
                    ->where('status', 'accepted')
                    ->get();
                foreach ($acceptedReqs as $ar) {
                    $exists = DB::table('donations')
                        ->where('donor_id', $di)
                        ->where('blood_request_id', $ar->blood_request_id)
                        ->exists();
                    if (! $exists) {
                        DB::table('donations')->insert([
                            'donor_id' => $di,
                            'blood_request_id' => $ar->blood_request_id,
                            'donation_date' => $now->copy()->subDays(rand(1, 30)),
                            'quantity' => rand(300, 500),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                        // Mark request as fulfilled if applicable
                        $req = DB::table('blood_requests')->find($ar->blood_request_id);
                        if ($req && $req->status === 'open') {
                            DB::table('blood_requests')->where('id', $ar->blood_request_id)->update(['status' => 'fulfilled']);
                        }
                    }
                }
            }
        }

        // ========== MESSAGES ==========
        $donorUserIds = DB::table('users')->where('role', 'donor')->pluck('id')->toArray();
        $hospitalUserIds = DB::table('users')->where('role', 'hospital')->pluck('id')->toArray();

        $conversations = [
            ['from' => $donorUserIds[0] ?? null, 'to' => $hospitalUserIds[0] ?? null,
                'messages' => [
                    ['content' => 'Bonjour, j\'ai accepté votre demande de sang O-. Je suis disponible cette semaine.', 'read_at' => null],
                    ['content' => 'Merci beaucoup! Pouvez-vous venir demain à 10h?', 'read_at' => null],
                    ['content' => 'Oui, c\'est parfait. À demain.', 'read_at' => null],
                ]],
            ['from' => $hospitalUserIds[0] ?? null, 'to' => $donorUserIds[1] ?? null,
                'messages' => [
                    ['content' => 'Bonjour, nous avons un besoin urgent de sang A+. Êtes-vous disponible?', 'read_at' => null],
                    ['content' => 'Oui, je peux venir cet après-midi.', 'read_at' => null],
                ]],
        ];

        foreach ($conversations as $conv) {
            if (! $conv['from'] || ! $conv['to']) {
                continue;
            }
            foreach ($conv['messages'] as $msg) {
                DB::table('messages')->insert([
                    'sender_id' => $conv['from'],
                    'receiver_id' => $conv['to'],
                    'content' => $msg['content'],
                    'read_at' => $msg['read_at'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // ========== NOTIFICATIONS ==========
        $allUserIds = DB::table('users')->pluck('id')->toArray();
        foreach ($allUserIds as $uid) {
            $count = DB::table('notifications')->where('user_id', $uid)->count();
            if ($count === 0) {
                DB::table('notifications')->insert([
                    'user_id' => $uid,
                    'title' => 'Bienvenue sur BloodLink',
                    'message' => 'Merci de rejoindre BloodLink. Ensemble, sauvons des vies!',
                    'type' => 'welcome',
                    'read_status' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $this->command->info('Test data seeded successfully!');
    }
}
