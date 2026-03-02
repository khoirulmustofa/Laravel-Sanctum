<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use Illuminate\Database\Seeder;

class SekolahSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 50; $i++) {
            Sekolah::updateOrCreate([
                'nama_sekolah' => 'Sekolah '.$i + 1,
                'nss' => 1234567890 .$i + 1,
                'npsn' => 1234567890 .$i + 1,

            ], [
                'alamat_sekolah' => 'Jl. Sekolah No. '.$i + 1,
                'kecamatan' => 'Kecamatan '.$i + 1,
                'kabupaten_kota' => 'Kabupaten '.$i + 1,
                'provinsi' => 'Provinsi '.$i + 1,
            ]);
        }
    }
}
