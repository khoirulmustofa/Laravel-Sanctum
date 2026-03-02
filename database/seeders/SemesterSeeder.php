<?php

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $semesters = [
            [
                'semester' => 1,
                'aktif' => true,
            ],
            [
                'semester' => 2,
                'aktif' => false,
            ],
        ];

        foreach ($semesters as $semester) {
            Semester::updateOrCreate($semester);
        }
    }
}
