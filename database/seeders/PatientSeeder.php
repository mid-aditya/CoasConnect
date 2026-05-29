<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $coasUsers = User::role("coas")->get();

        if ($coasUsers->isEmpty()) {
            $this->command->warn(
                "No COAS users found. Skipping patient seeding.",
            );
            return;
        }

        $patients = [
            // IPD Patients
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "A.W.",
                "age_category" => "Dewasa",
                "gender" => "L",
                "working_diagnosis" =>
                    "DM Tipe 2 dengan Ketoasidosis Diabetikum",
                "care_context" => "Rawat Inap",
            ],
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "S.R.",
                "age_category" => "Dewasa",
                "gender" => "P",
                "working_diagnosis" => "Hipertensi Esensial Grade 2",
                "care_context" => "Rawat Inap",
            ],
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "M.H.",
                "age_category" => "Dewasa",
                "gender" => "L",
                "working_diagnosis" => "Pneumonia Komunitas",
                "care_context" => "Rawat Inap",
            ],
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "D.K.",
                "age_category" => "Lansia",
                "gender" => "L",
                "working_diagnosis" => "CHF NYHA II dengan Fibrilasi Atrium",
                "care_context" => "Rawat Inap",
            ],
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "R.A.",
                "age_category" => "Dewasa",
                "gender" => "P",
                "working_diagnosis" => "Thyphoid Fever dengan Dehidrasi",
                "care_context" => "Rawat Jalan",
            ],

            // IKA Patients
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "F.Z.",
                "age_category" => "Anak",
                "gender" => "L",
                "working_diagnosis" => "Bronchopneumonia pada Anak",
                "care_context" => "Rawat Inap",
            ],
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "A.L.",
                "age_category" => "Anak",
                "gender" => "P",
                "working_diagnosis" => "DBD Grade I",
                "care_context" => "Rawat Inap",
            ],
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "P.W.",
                "age_category" => "Bayi",
                "gender" => "L",
                "working_diagnosis" => "Gastroenteritis Akut dehidrasi ringan",
                "care_context" => "Rawat Jalan",
            ],

            // Bedah Patients
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "T.S.",
                "age_category" => "Dewasa",
                "gender" => "L",
                "working_diagnosis" => "Acute Appendicitis",
                "care_context" => "Rawat Inap",
            ],
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "N.W.",
                "age_category" => "Dewasa",
                "gender" => "P",
                "working_diagnosis" => "Hernia Inguinalis Dextra",
                "care_context" => "Rawat Inap",
            ],
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "B.H.",
                "age_category" => "Lansia",
                "gender" => "L",
                "working_diagnosis" => "Carpal Tunnel Syndrome",
                "care_context" => "Rawat Jalan",
            ],

            // Obsgyn Patients
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "L.M.",
                "age_category" => "Dewasa",
                "gender" => "P",
                "working_diagnosis" => "Kehamilan Trimester 3",
                "care_context" => "Rawat Jalan",
            ],
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "S.U.",
                "age_category" => "Dewasa",
                "gender" => "P",
                "working_diagnosis" => "Myoma Uteri",
                "care_context" => "Rawat Jalan",
            ],

            // Neurologi Patients
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "H.K.",
                "age_category" => "Lansia",
                "gender" => "L",
                "working_diagnosis" => "Stroke Iskemik SINAC",
                "care_context" => "Rawat Inap",
            ],
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "Y.T.",
                "age_category" => "Dewasa",
                "gender" => "P",
                "working_diagnosis" => "Migraine with Aura",
                "care_context" => "Rawat Jalan",
            ],

            // Psikiatri Patients
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "A.P.",
                "age_category" => "Dewasa",
                "gender" => "L",
                "working_diagnosis" => "Major Depressive Disorder",
                "care_context" => "Rawat Jalan",
            ],
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "I.G.",
                "age_category" => "Dewasa",
                "gender" => "P",
                "working_diagnosis" => "Anxiety Disorder Generalized",
                "care_context" => "Rawat Jalan",
            ],

            // Mata Patients
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "W.S.",
                "age_category" => "Lansia",
                "gender" => "L",
                "working_diagnosis" => "Cataract Senilis Immatura OD",
                "care_context" => "Rawat Jalan",
            ],

            // THT Patients
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "C.L.",
                "age_category" => "Anak",
                "gender" => "P",
                "working_diagnosis" => "OMSK Dextral",
                "care_context" => "Rawat Jalan",
            ],
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "R.D.",
                "age_category" => "Dewasa",
                "gender" => "L",
                "working_diagnosis" => "Deviasi Septum Nasi Dextral",
                "care_context" => "Rawat Jalan",
            ],

            // Kulit & Kelamin Patients
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "J.M.",
                "age_category" => "Dewasa",
                "gender" => "L",
                "working_diagnosis" => "Tinea Corporis",
                "care_context" => "Rawat Jalan",
            ],
            [
                "user_id" => $coasUsers->random()->id,
                "initials" => "E.F.",
                "age_category" => "Dewasa",
                "gender" => "P",
                "working_diagnosis" => "Acne Vulgaris Grade II",
                "care_context" => "Rawat Jalan",
            ],
        ];

        foreach ($patients as $patient) {
            Patient::create($patient);
        }
    }
}
