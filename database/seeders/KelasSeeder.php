<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // nama paralel  tipe
        // mulai dari 7A ~7D, 8A ~8D, 9A ~9D
        $kelas = [
            [
                'nama' => '7A',
                'paralel' => '7',
                'tipe' => 'Utama',
            ],
            [
                'nama' => '7B',
                'paralel' => '7',
                'tipe' => 'Utama',
            ],
            [
                'nama' => '7C',
                'paralel' => '7',
                'tipe' => 'Utama',
            ],
            [
                'nama' => '7D',
                'paralel' => '7',
                'tipe' => 'Utama',
            ],
            [
                'nama' => '8A',
                'paralel' => '8',
                'tipe' => 'Utama',
            ],
            [
                'nama' => '8B',
                'paralel' => '8',
                'tipe' => 'Utama',
            ],
            [
                'nama' => '8C',
                'paralel' => '8',
                'tipe' => 'Utama',
            ],
            [
                'nama' => '8D',
                'paralel' => '8',
                'tipe' => 'Utama',
            ],
            [
                'nama' => '9A',
                'paralel' => '9',
                'tipe' => 'Utama',
            ],
            [
                'nama' => '9B',
                'paralel' => '9',
                'tipe' => 'Utama',
            ],
            [
                'nama' => '9C',
                'paralel' => '9',
                'tipe' => 'Utama',
            ],
            [
                'nama' => '9D',
                'paralel' => '9',
                'tipe' => 'Utama',
            ],
        ];

        foreach ($kelas as $key => $kelas) {
            Kelas::updateOrCreate([
                'nama' => $kelas['nama'],
                'paralel' => $kelas['paralel'],
                'tipe' => $kelas['tipe'],
            ]);
        }
    }
}
