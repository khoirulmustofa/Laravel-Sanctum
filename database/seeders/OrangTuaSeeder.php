<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrangTuaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       

        for ($i = 1; $i <= 600; $i++) {
            \App\Models\OrangTua::create([
                'user_id' => null, // bisa diisi jika ingin langsung buat akun
                'nama' => 'Nama Orang Tua ' . ($i + 1),
                'jenis_kelamin' => $i % 2 == 0 ? 'L' : 'P',
                'pekerjaan' => 'Pekerjaan ' . $i,
                'pendidikan' => 'Pendidikan ' . $i,
                'penghasilan' => 1000000 * $i,
                'no_telepon' => '08123456789' . $i,
                'email' => 'orangtua' . $i . '@example.com',
                'alamat' => 'Alamat Orang Tua ' . $i,
            ]);
        }
    }
}
