<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles (skip if exists)
        $roles = [
            "admin" => "Administrator",
            "coordinator" => "Koordinator Program",
            "doctor" => "Dosen Pembimbing",
            "coas" => "Koas",
        ];

        foreach ($roles as $name => $label) {
            Role::firstOrCreate(["name" => $name, "guard_name" => "web"]);
        }

        // Also create legacy roles for backward compatibility
        $legacyRoles = [
            "Administrator Faisal",
            "Administrator Fakultas",
            "Koordinator Program",
            "Dosen Pembimbing",
            "Koas",
        ];

        foreach ($legacyRoles as $name) {
            Role::firstOrCreate(["name" => $name, "guard_name" => "web"]);
        }

        // Create permissions (skip if exists)
        $permissions = [
            // User management
            "users.view",
            "users.create",
            "users.edit",
            "users.delete",

            // Patient management
            "patients.view",
            "patients.create",
            "patients.edit",
            "patients.delete",

            // Assignment management
            "assignments.view",
            "assignments.create",
            "assignments.edit",
            "assignments.delete",

            // Clinical log management
            "logs.view",
            "logs.create",
            "logs.edit",
            "logs.delete",
            "logs.submit",
            "logs.review",

            // Competency management
            "competencies.view",
            "competencies.manage",

            // Report generation
            "reports.view",
            "reports.export",

            // System settings
            "settings.manage",

            // WhatsApp management
            "whatsapp.manage",
            "whatsapp.templates",
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                "name" => $permission,
                "guard_name" => "web",
            ]);
        }

        // Assign permissions to roles
        // Admin - all permissions
        $admin = Role::findByName("admin");
        $admin->syncPermissions(Permission::all());

        // Coordinator - view and manage curriculum
        $coordinator = Role::findByName("coordinator");
        $coordinator->syncPermissions([
            "users.view",
            "patients.view",
            "assignments.view",
            "logs.view",
            "logs.review",
            "competencies.view",
            "competencies.manage",
            "reports.view",
            "reports.export",
        ]);

        // Doctor - supervise and review
        $doctor = Role::findByName("doctor");
        $doctor->syncPermissions([
            "patients.view",
            "assignments.view",
            "logs.view",
            "logs.review",
            "reports.view",
        ]);

        // COAS - basic operations
        $coas = Role::findByName("coas");
        $coas->syncPermissions([
            "patients.view",
            "logs.view",
            "logs.create",
            "logs.edit",
            "logs.submit",
            "reports.view",
        ]);
    }
}
