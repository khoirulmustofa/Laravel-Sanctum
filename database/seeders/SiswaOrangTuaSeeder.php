<?php

namespace Database\Seeders;

use App\Models\Siswa;
use Illuminate\Database\Seeder;

class SiswaOrangTuaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $siswas = Siswa::all();

        foreach ($siswas as $key => $siswa) {
            $siswa->orangTua()->updateOrCreate([
                'nama_ayah' => 'Ayah '.$key + 1,
                'pekerjaan_ayah' => 'Pekerjaan Ayah '.$key + 1,
                'pendidikan_ayah' => 'Pendidikan Ayah '.$key + 1,
                'penghasilan_ayah' => 1000000,
                'no_telepon_ayah' => '081234567890'.$key + 1,
                'email_ayah' => 'email'.$key + 1 .'@gmail.com',

                'nama_ibu' => 'Ibu '.$key + 1,
                'pekerjaan_ibu' => 'Pekerjaan Ibu '.$key + 1,
                'pendidikan_ibu' => 'Pendidikan Ibu '.$key + 1,
                'penghasilan_ibu' => 1000000,
                'no_telepon_ibu' => '081234567890'.$key + 1,
                'email_ibu' => 'email'.$key + 1 .'@gmail.com',

                'nama_wali' => 'Wali '.$key + 1,
                'pekerjaan_wali' => 'Pekerjaan Wali '.$key + 1,
                'pendidikan_wali' => 'Pendidikan Wali '.$key + 1,
                'penghasilan_wali' => 1000000,
                'no_telepon_wali' => '081234567890'.$key + 1,
                'email_wali' => 'email'.$key + 1 .'@gmail.com',
            ]);
        }
    }
}
