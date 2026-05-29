<?php

namespace Database\Seeders;

use App\Models\ClinicalLog;
use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Database\Seeder;

class EvaluationSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = User::role('doctor')->get();

        if ($doctors->isEmpty()) {
            $this->command->warn('No doctors found. Skipping evaluation seeding.');
            return;
        }

        // Evaluate submitted/reviewed logs
        $logsToEvaluate = ClinicalLog::whereIn('status', [
            ClinicalLog::STATUS_SUBMITTED,
            ClinicalLog::STATUS_REVIEWED,
        ])->get();

        foreach ($logsToEvaluate as $log) {
            $status = $log->status === ClinicalLog::STATUS_REVIEWED
                ? Evaluation::STATUS_APPROVED
                : [Evaluation::STATUS_APPROVED, Evaluation::STATUS_REVISION_REQUESTED][array_rand([0, 1])];

            $evaluation = Evaluation::create([
                'clinical_log_id' => $log->id,
                'evaluator_id' => $doctors->random()->id,
                'status' => $status,
                'feedback' => $this->getFeedback($status),
                'ratings' => $this->getRatings(),
                'evaluated_at' => now()->subDays(rand(1, 5)),
            ]);

            // Update log status
            $log->update(['status' => ClinicalLog::STATUS_REVIEWED]);
        }
    }

    private function getFeedback(string $status): string
    {
        if ($status === Evaluation::STATUS_APPROVED) {
            $feedbacks = [
                'Log写得很好，已掌握基本临床技能。继续加油！',
                'Good clinical documentation. Keep up the good work.',
                'Sudah kompeten untuk level koas. Pertahankan.',
                'Excellent reflection. Shows good insight into patient care.',
                'Pemeriksaan sudah baik. Perlu lebih fokus pada follow-up.',
            ];
        } else {
            $feedbacks = [
                'Mohon lengkapi deskripsi prosedur yang dilakukan.',
                'Perlu perbaikan pada refleksi klinis.',
                'Silakan tambahkan informasi tentangEdukasi yang diberikan kepada pasien.',
                'Deskripsi kondisi pasien kurang lengkap. Mohon diperbaiki.',
                'Harap cantumkan hasil pemeriksaan penunjang yang relevan.',
            ];
        }

        return $feedbacks[array_rand($feedbacks)];
    }

    private function getRatings(): array
    {
        $ratings = [
            'professionalism' => rand(3, 5),
            'clinical_skills' => rand(2, 5),
            'communication' => rand(3, 5),
            'documentation' => rand(2, 5),
            'clinical_reasoning' => rand(3, 5),
        ];

        return $ratings;
    }
}
