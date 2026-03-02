<?php

namespace Database\Seeders;

use App\Models\Siswa;
use Illuminate\Database\Seeder;

class SiswaAlamatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $siswas = Siswa::all();

        foreach ($siswas as $key => $siswa) {
            $siswa->alamat()->updateOrCreate([
                'jenis_tinggal' => 'Asrama',
                'alamat_tempat_tinggal' => 'Jl. Asrama '.$key + 1,
                'kelurahan' => 'Kelurahan '.$key + 1,
                'kecamatan' => 'Kecamatan '.$key + 1,
                'kabupaten_kota' => 'Kabupaten '.$key + 1,
                'provinsi' => 'Provinsi '.$key + 1,
            ]);
        }
    }
}
