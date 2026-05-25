<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@coasconnect.local',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Create coordinator
        $coordinator = User::create([
            'name' => 'Dr. coordinator',
            'email' => 'koordinator@coasconnect.local',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $coordinator->assignRole('coordinator');

        // Create doctors
        $doctors = [
            ['name' => 'Dr. John Smith', 'email' => 'doctor1@coasconnect.local'],
            ['name' => 'Dr. Jane Doe', 'email' => 'doctor2@coasconnect.local'],
        ];

        foreach ($doctors as $doctorData) {
            $doctor = User::create([
                'name' => $doctorData['name'],
                'email' => $doctorData['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            $doctor->assignRole('doctor');
        }

        // Create COAS students
        $coasStudents = [
            ['name' => 'Andi Wijaya', 'email' => 'coas1@coasconnect.local'],
            ['name' => 'Budi Santoso', 'email' => 'coas2@coasconnect.local'],
            ['name' => 'Citra Dewi', 'email' => 'coas3@coasconnect.local'],
            ['name' => 'Dian Pratama', 'email' => 'coas4@coasconnect.local'],
            ['name' => 'Eko Susanto', 'email' => 'coas5@coasconnect.local'],
        ];

        foreach ($coasStudents as $index => $coasData) {
            $coas = User::create([
                'name' => $coasData['name'],
                'email' => $coasData['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            $coas->assignRole('coas');

            // Create user profile
            \App\Models\UserProfile::create([
                'user_id' => $coas->id,
                'employee_id' => '2021' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'faculty' => 'Kedokteran',
                'batch' => 2021,
                'specialization' => 'Umum',
                'supervision_quota' => 5,
            ]);
        }

        // Assign user profiles to doctors
        foreach ($doctors as $index => $doctorData) {
            $doctor = User::where('email', $doctorData['email'])->first();
            if ($doctor) {
                \App\Models\UserProfile::create([
                    'user_id' => $doctor->id,
                    'employee_id' => 'NIP-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'department' => 'Ilmu Penyakit Dalam',
                    'supervision_quota' => 10,
                ]);
            }
        }
    }
}
