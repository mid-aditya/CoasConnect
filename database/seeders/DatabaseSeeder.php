<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Roles & Permissions (required for users)
            RolePermissionSeeder::class,

            // 2. Academic Periods (required for rotation assignments)
            AcademicPeriodSeeder::class,

            // 3. Cohorts
            CohortSeeder::class,

            // 4. Rotations & Procedures
            RotationSeeder::class,

            // 5. Demo Users (admin, coordinator, doctors, coas)
            DemoUsersSeeder::class,

            // 6. Competency Tree (required for clinical logs)
            CompetencyTreeSeeder::class,

            // 7. Rotation Assignments (koas to rotations)
            RotationAssignmentSeeder::class,

            // 8. Patients
            PatientSeeder::class,

            // 9. Patient Assignments (koas to patients)
            AssignmentSeeder::class,

            // 10. Clinical Logs
            ClinicalLogSeeder::class,

            // 11. Patient Logs
            PatientLogSeeder::class,

            // 12. Evaluations
            EvaluationSeeder::class,

            // 13. WhatsApp Templates
            WhatsAppTemplateSeeder::class,
        ]);
    }
}
