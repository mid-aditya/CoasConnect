<?php

namespace Database\Seeders;

use App\Models\AcademicPeriod;
use Illuminate\Database\Seeder;

class AcademicPeriodSeeder extends Seeder
{
    public function run(): void
    {
        $periods = [
            [
                'name' => 'Semester Ganjil 2024-2025',
                'start_date' => '2024-08-01',
                'end_date' => '2024-12-31',
                'is_active' => true,
            ],
            [
                'name' => 'Semester Genap 2023-2024',
                'start_date' => '2024-02-01',
                'end_date' => '2024-07-31',
                'is_active' => false,
            ],
            [
                'name' => 'Semester Ganjil 2023-2024',
                'start_date' => '2023-08-01',
                'end_date' => '2024-01-31',
                'is_active' => false,
            ],
            [
                'name' => 'Semester Pendek 2024',
                'start_date' => '2025-01-15',
                'end_date' => '2025-03-15',
                'is_active' => false,
            ],
        ];

        foreach ($periods as $period) {
            AcademicPeriod::create($period);
        }
    }
}
