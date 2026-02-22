<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = ['Administrator Fakultas', 'Koordinator Program', 'Dosen Pembimbing', 'Koas'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Create Default Admin
        $adminRole = Role::where('name', 'Administrator Fakultas')->first();
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin Fakultas',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
            ]
        );

        // Optional: Create mock users for other roles
        $coordRole = Role::where('name', 'Koordinator Program')->first();
        User::firstOrCreate(
            ['email' => 'koordinator@admin.com'],
            [
                'name' => 'Koordinator Satu',
                'password' => Hash::make('password'),
                'role_id' => $coordRole->id,
            ]
        );

        $dosenRole = Role::where('name', 'Dosen Pembimbing')->first();
        User::firstOrCreate(
            ['email' => 'dosen@admin.com'],
            [
                'name' => 'Dr. Budi',
                'password' => Hash::make('password'),
                'role_id' => $dosenRole->id,
            ]
        );

        $koasRole = Role::where('name', 'Koas')->first();
        User::firstOrCreate(
            ['email' => 'koas@admin.com'],
            [
                'name' => 'Andi Koas',
                'password' => Hash::make('password'),
                'role_id' => $koasRole->id,
            ]
        );
    }
}
