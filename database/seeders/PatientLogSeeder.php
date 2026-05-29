<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\PatientLog;
use App\Models\Rotation;
use Illuminate\Database\Seeder;

class PatientLogSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::with("user")->get();
        $rotations = Rotation::all();

        if ($patients->isEmpty()) {
            $this->command->warn(
                "No patients found. Skipping patient log seeding.",
            );
            return;
        }

        $statuses = ["draft", "submitted", "revised", "approved"];

        // Create 2-4 logs per patient
        foreach ($patients as $patient) {
            $numLogs = rand(2, 4);

            for ($i = 0; $i < $numLogs; $i++) {
                PatientLog::create([
                    "patient_id" => $patient->id,
                    "user_id" => $patient->user_id,
                    "rotation_id" => $rotations->random()->id,
                    "log_date" => now()->subDays(rand(1, 20)),
                    "condition_summary" => $this->getConditionSummary(),
                    "key_examination" => $this->getKeyExamination(),
                    "treatment_plan" => $this->getTreatmentPlan(),
                    "procedures_performed" => $this->getProcedures(),
                    "clinical_reflection" => $this->getReflection(),
                    "status" => $statuses[array_rand($statuses)],
                    "supervisor_comment" => $this->getSupervisorComment(),
                ]);
            }
        }
    }

    private function getConditionSummary(): string
    {
        $summaries = [
            "Pasien compos mentis, tampak sakit sedang, tidak ikterik, tidak sesak.",
            "Keadaan umum tampak lemah, kesadaran compos mentis, TTV stabil.",
            "Pasien masih mengeluh nyeri, namun sudah membaik dari hari sebelumnya.",
            "Keadaan umum baik, pasien sudah bisa mobilize dengan bantuan.",
            "Pasien masih dalam observasi, belum ada perubahan signifikan.",
        ];

        return $summaries[array_rand($summaries)];
    }

    private function getKeyExamination(): string
    {
        $examinations = [
            "TD: 130/80 mmHg, Nadi: 80x/menit, Suhu: 36.5°C, SpO2: 98%",
            "Abdomen: Soepel, NT, BU (+) normal, Nyeri tekan epigastrik (+)",
            "Cor: BJ I-II reg, Murmur (-), Pulmo: Ves + rhonki (-)",
            "Pemeriksaan neurologis: GCS 15, Pupil isokor 3mm, Motorik 5/5",
            "Loc, Dec, Cmac: E4V5M6, deficit neurologis fokus (-)",
        ];

        return $examinations[array_rand($examinations)];
    }

    private function getTreatmentPlan(): string
    {
        $plans = [
            "Lanjutan terapi sesuai standar, monitoring ketat, lab kontrol 2x24 jam.",
            "Tambah analgesik, Infus RL 20 tpm, edukasi mobilisasi bertahap.",
            "Persiapan untuk prosedur besok pagi, informed consent sudah ditandatangani.",
            "Konsul interna untuk evaluasi lebih lanjut, puasa malam ini untuk pemeriksaan lanjutan.",
            "Pasien boleh pulang jika kondisi membaik, kontrol poliklinik 1 minggu.",
        ];

        return $plans[array_rand($plans)];
    }

    private function getProcedures(): string
    {
        $procedures = [
            "Insersi infus perifer, Pengambilan sample darah lab, Monitoring TTV q4jam",
            "Dressing luka operasi, Medication Administration, Wound assessment",
            "Nebulizer therapy, O2 nasal canul 3L/menit, Vital signs monitoring",
            "EKG 12 lead, Interpretasi hasil lab, Patient education",
            "Wound care, Medication administration, Dietary counseling",
        ];

        return $procedures[array_rand($procedures)];
    }

    private function getReflection(): string
    {
        $reflections = [
            "Belajar banyak tentang komunikasi dengan pasien geriatri hari ini.",
            "Pemeriksaan fisik masih perlu latihan, terutama auskultasi jantung.",
            "Penting untuk selalu memverifikasi setiap instruksi sebelum bertindak.",
            "Pasien menunjukkan perbaikan yang signifikan dibanding kemarin.",
            "Kesalahan kemarin sudah diperbaiki, progress patient care membaik.",
        ];

        return $reflections[array_rand($reflections)];
    }

    private function getSupervisorComment(): string
    {
        $comments = [
            "Good progress. Pertahankan.",
            "Perlu lebih teliti dalam mendokumentasikan.",
            "Sudah bagus, lanjutkan monitoring.",
            "Diskusi kasus besok pagi.",
            "Approved.",
        ];

        return $comments[array_rand($comments)];
    }
}
