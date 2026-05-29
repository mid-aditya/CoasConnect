<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Patient;
use App\Models\Rotation;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $doctors = User::role("doctor")->get();
        $rotations = Rotation::all();

        if ($doctors->isEmpty()) {
            $this->command->warn(
                "No doctors found. Skipping assignment seeding.",
            );
            return;
        }

        $statuses = [
            Assignment::STATUS_ACTIVE,
            Assignment::STATUS_ACTIVE,
            Assignment::STATUS_ACTIVE,
            Assignment::STATUS_PENDING,
            Assignment::STATUS_COMPLETED,
        ];

        foreach ($patients as $patient) {
            $doctor = $doctors->random();
            $rotation = $rotations->random();

            Assignment::create([
                "patient_id" => $patient->id,
                "coas_id" => $patient->user_id,
                "doctor_id" => $doctor->id,
                "rotation_id" => $rotation->id,
                "status" => $statuses[array_rand($statuses)],
                "assigned_at" => now()->subDays(rand(1, 30)),
                "notes" => $this->getRandomNotes(),
            ]);
        }
    }

    private function getRandomNotes(): string
    {
        $notes = [
            "Pasien sudah mendapatkan edukasi tentang penyakitnya.",
            "Keluarga pasien sudah diklarifikasi tentang rencana penanganan.",
            "Pasien memerlukan monitoring ketat.",
            "Pasien sudah merespons terapi awal.",
            "Perlu konsultasi interdisipliner.",
            "Pasien dalam masa observasi.",
        ];

        return $notes[array_rand($notes)];
    }
}
