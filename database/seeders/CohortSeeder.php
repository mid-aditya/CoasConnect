<?php

namespace Database\Seeders;

use App\Models\Cohort;
use Illuminate\Database\Seeder;

class CohortSeeder extends Seeder
{
    public function run(): void
    {
        $cohorts = [
            ['name' => 'Cohort A - 2021', 'year' => 2021],
            ['name' => 'Cohort B - 2021', 'year' => 2021],
            ['name' => 'Cohort A - 2022', 'year' => 2022],
            ['name' => 'Cohort B - 2022', 'year' => 2022],
            ['name' => 'Cohort A - 2023', 'year' => 2023],
            ['name' => 'Cohort B - 2023', 'year' => 2023],
            ['name' => 'Cohort A - 2024', 'year' => 2024],
            ['name' => 'Cohort B - 2024', 'year' => 2024],
        ];

        foreach ($cohorts as $cohort) {
            Cohort::create($cohort);
        }
    }
}
