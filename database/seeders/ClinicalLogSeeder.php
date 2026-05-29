<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\ClinicalLog;
use App\Models\Competency;
use Illuminate\Database\Seeder;

class ClinicalLogSeeder extends Seeder
{
    public function run(): void
    {
        $assignments = Assignment::with(["coas", "rotation"])->get();
        $competencies = Competency::active()->whereNotNull("parent_id")->get(); // Get leaf competencies

        if ($assignments->isEmpty()) {
            $this->command->warn(
                "No assignments found. Skipping clinical log seeding.",
            );
            return;
        }

        if ($competencies->isEmpty()) {
            $this->command->warn(
                "No competencies found. Skipping clinical log seeding.",
            );
            return;
        }

        $activityTypes = [
            "anamnesis",
            "physical_exam",
            "procedure",
            "education",
            "consultation",
            "other",
        ];

        $conditions = ["stable", "stable", "improving", "worsening"];

        $statuses = ["draft", "submitted", "submitted", "reviewed"];

        // Create 2-3 logs per assignment
        foreach ($assignments as $assignment) {
            $numLogs = rand(2, 3);

            for ($i = 0; $i < $numLogs; $i++) {
                $activityType = $activityTypes[array_rand($activityTypes)];
                $status = $statuses[array_rand($statuses)];

                $log = ClinicalLog::create([
                    "assignment_id" => $assignment->id,
                    "rotation_id" => $assignment->rotation_id,
                    "user_id" => $assignment->coas_id,
                    "activity_date" => now()->subDays(rand(1, 14)),
                    "activity_type" => $activityType,
                    "description" => $this->getDescription($activityType),
                    "patient_condition" => $conditions[array_rand($conditions)],
                    "reflection" => $this->getReflection(),
                    "status" => $status,
                    "submitted_at" =>
                        $status !== "draft" ? now()->subDays(rand(0, 7)) : null,
                ]);

                // Attach random competencies (2-4)
                $numComps = min(rand(2, 4), $competencies->count());
                $attachedCompetencies = $competencies->random($numComps);

                foreach ($attachedCompetencies as $comp) {
                    $log->competencies()->attach($comp->id, [
                        "rating" => rand(1, 5),
                        "feedback" => $this->getFeedback(),
                    ]);
                }
            }
        }
    }

    private function getDescription(string $type): string
    {
        $descriptions = [
            "anamnesis" => [
                "Melakukan anamnesis autoalloanamnesis pada pasien dengan keluhan utama poliuria dan polidipsi.",
                "Anamnesis heteroanamnesis pada keluarga pasien yang mengeluh demam tinggi sejak 3 hari.",
                "Wawancara mendalam tentang riwayat penyakit terdahulu dan kebiasaan hidup pasien.",
            ],
            "physical_exam" => [
                "Pemeriksaan fisik lengkap dengan fokus pada sistem kardiovaskular dan respirasi.",
                "Pemeriksaan abdomen dengan teknik inspeksi, palpasi, perkusi, dan auskultasi.",
                "Pemeriksaan neurologis singkat dengan GCS, pupil, dan motorik.",
            ],
            "procedure" => [
                "Membantu tindakan insersi infus perifer pada pasien geriatri.",
                "Melakukan dressing luka pada pasien post-op hari ke-2.",
                "Mengukur vital signs dan melakukan EKG 12 lead.",
            ],
            "education" => [
                "Memberikan edukasi kepada pasien tentang pentingnya kontrol diet pada diabetes.",
                "Konseling keluarga tentang tanda-tanda bahaya yang perlu diwaspadai.",
                "Edukasi tentang cara penggunaan obat dan efek sampingnya.",
            ],
            "consultation" => [
                "Membantu pembuatan surat konsultasi ke bagian ilmu penyakit dalam.",
                "Mendiskusikan kasus dengan dokter pembimbing tentang rencana pemeriksaan.",
                "Mengikuti visitasi dan menyampaikan perkembangan pasien.",
            ],
            "other" => [
                "Mengikuti seminar mingguan tentang manajemen stroke akut.",
                "Membaca dan mendiskusikan jurnal terbaru tentang terapi diabetes.",
                "Mengikuti ronde pagi dengan tim medis.",
            ],
        ];

        $items = $descriptions[$type] ?? $descriptions["other"];
        return $items[array_rand($items)];
    }

    private function getReflection(): string
    {
        $reflections = [
            "Pasien menunjukkan pemahaman yang baik setelah diberikan edukasi.",
            "Terdapat hambatan dalam komunikasi dengan pasien geriatri, namun dapat diatasi.",
            "Saya belajar bahwa anamnesis yang baik sangat penting untuk diagnosis.",
            "Pemeriksaan fisik abdomen masih perlu latihan lebih banyak.",
            "Penting untuk selalu memeriksa ulang setiap instruksi.",
            "Saya menyadari bahwa manajemen waktu sangat krusial.",
            "Keterlibatan keluarga dalam proses pengobatan sangat membantu.",
        ];

        return $reflections[array_rand($reflections)];
    }

    private function getFeedback(): string
    {
        $feedbacks = [
            "做得很好 (Lakukan dengan baik)",
            "Perlu peningkatan dalam teknik.",
            "Sudah kompeten untuk level ini.",
            "Perlu bimbingan lebih lanjut.",
            "Excellent work!",
        ];

        return $feedbacks[array_rand($feedbacks)];
    }
}
