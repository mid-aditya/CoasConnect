<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user (skip if exists)
        $admin = User::firstOrCreate(
            ["email" => "admin@coasconnect.local"],
            [
                "name" => "Administrator",
                "password" => Hash::make("password"),
                "email_verified_at" => now(),
            ],
        );
        $admin->assignRole("admin");

        // Create coordinator (skip if exists)
        $coordinator = User::firstOrCreate(
            ["email" => "koordinator@coasconnect.local"],
            [
                "name" => "Dr. Coordinator",
                "password" => Hash::make("password"),
                "email_verified_at" => now(),
            ],
        );
        $coordinator->assignRole("coordinator");

        // Create doctors (skip if exists)
        $doctors = [
            [
                "name" => "Dr. John Smith",
                "email" => "doctor1@coasconnect.local",
            ],
            ["name" => "Dr. Jane Doe", "email" => "doctor2@coasconnect.local"],
        ];

        foreach ($doctors as $index => $doctorData) {
            $doctor = User::firstOrCreate(
                ["email" => $doctorData["email"]],
                [
                    "name" => $doctorData["name"],
                    "password" => Hash::make("password"),
                    "email_verified_at" => now(),
                ],
            );
            $doctor->assignRole("doctor");

            // Create or update user profile
            UserProfile::updateOrCreate(
                ["user_id" => $doctor->id],
                [
                    "employee_id" =>
                        "NIP-" . str_pad($index + 1, 4, "0", STR_PAD_LEFT),
                    "department" => "Ilmu Penyakit Dalam",
                    "supervision_quota" => 10,
                ],
            );
        }

        // Create COAS students (skip if exists)
        $coasStudents = [
            ["name" => "Andi Wijaya", "email" => "coas1@coasconnect.local"],
            ["name" => "Budi Santoso", "email" => "coas2@coasconnect.local"],
            ["name" => "Citra Dewi", "email" => "coas3@coasconnect.local"],
            ["name" => "Dian Pratama", "email" => "coas4@coasconnect.local"],
            ["name" => "Eko Susanto", "email" => "coas5@coasconnect.local"],
        ];

        foreach ($coasStudents as $index => $coasData) {
            $coas = User::firstOrCreate(
                ["email" => $coasData["email"]],
                [
                    "name" => $coasData["name"],
                    "password" => Hash::make("password"),
                    "email_verified_at" => now(),
                ],
            );
            $coas->assignRole("coas");

            // Create or update user profile
            UserProfile::updateOrCreate(
                ["user_id" => $coas->id],
                [
                    "employee_id" =>
                        "2021" . str_pad($index + 1, 4, "0", STR_PAD_LEFT),
                    "faculty" => "Kedokteran",
                    "batch" => 2021,
                    "specialization" => "Umum",
                    "supervision_quota" => 5,
                ],
            );
        }
    }
}
