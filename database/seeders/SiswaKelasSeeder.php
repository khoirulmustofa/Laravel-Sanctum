<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class SiswaKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $siswas = Siswa::all(); // ada 300 siswa
        $kelasList = Kelas::all(); // ada 12 kelas
        $tahunAjaran = TahunAjaran::where('aktif', true)->first(); // ada 3 tahun ajaran
        $semester = Semester::where('aktif', true)->first(); // ada 2 semester

        $siswas->chunk(25)->each(function ($chunk, $index) use ($kelasList, $tahunAjaran, $semester) {

            $kelas = $kelasList[$index] ?? null;

            if (! $kelas) {
                return;
            }

            foreach ($chunk as $siswa) {
                SiswaKelas::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'tahun_ajaran' => $tahunAjaran->nama,
                        'semester' => $semester->semester,
                    ],
                    [
                        'kelas_id' => $kelas->id,
                        'aktif' => true,
                    ]
                );
            }
        });
    }
}
