<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //  nama       tanggal_mulai  tanggal_selesai   aktif
        $tahunAjarans = [
            [
                'nama' => '2024/2025',
                'tanggal_mulai' => '2024-07-01',
                'tanggal_selesai' => '2025-06-30',
                'aktif' => true,
            ],
            [
                'nama' => '2025/2026',
                'tanggal_mulai' => '2025-07-01',
                'tanggal_selesai' => '2026-06-30',
                'aktif' => false,
            ],
        ];

        foreach ($tahunAjarans as $tahunAjaran) {
            TahunAjaran::updateOrCreate($tahunAjaran);
        }
    }
}
