<?php

namespace Database\Seeders;

use App\Models\AcademicPeriod;
use App\Models\Rotation;
use App\Models\RotationAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;

class RotationAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $coasUsers = User::role('coas')->get();
        $doctors = User::role('doctor')->get();
        $rotations = Rotation::all();
        $academicPeriods = AcademicPeriod::all();

        if ($coasUsers->isEmpty() || $doctors->isEmpty()) {
            $this->command->warn('No COAS or doctors found. Skipping rotation assignment seeding.');
            return;
        }

        $activePeriod = $academicPeriods->where('is_active', true)->first();
        if (!$activePeriod) {
            $activePeriod = $academicPeriods->first();
        }

        // Assign each COAS to 2-3 rotations
        foreach ($coasUsers as $coas) {
            $assignedRotations = $rotations->random(rand(2, 3));

            foreach ($assignedRotations as $rotation) {
                $startDate = $activePeriod->start_date->copy()->addDays(rand(0, 30));
                $endDate = $startDate->copy()->addDays(rand(4, 8) * 7); // 4-8 weeks

                RotationAssignment::create([
                    'user_id' => $coas->id,
                    'rotation_id' => $rotation->id,
                    'academic_period_id' => $activePeriod->id,
                    'supervisor_id' => $doctors->random()->id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]);
            }
        }
    }
}
