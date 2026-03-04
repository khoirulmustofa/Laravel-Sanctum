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
        $orangTuas = \App\Models\OrangTua::all();

        // Schema::create('siswa_orang_tua', function (Blueprint $table) {
        //     $table->uuid('id')->primary();
        //     $table->foreignUuid('siswa_id')->constrained('siswa')->cascadeOnDelete();
        //     $table->foreignUuid('orang_tua_id')->constrained('orang_tua')->cascadeOnDelete();

        //     // Informasi relasi spesifik
        //     $table->string('hubungan'); // Contoh: Ayah, Ibu, Wali, Kakak, Tante
        //     $table->boolean('kontak_utama')->default(false); // Untuk menentukan siapa yang dihubungi jika darurat

        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        foreach ($siswas as $index => $siswa) {
            // Mengambil 2 orang tua berdasarkan urutan (0-1, 2-3, 4-5, dst)
            $chunkOrtu = $orangTuas->slice($index * 2, 2);

            if ($chunkOrtu->count() == 2) {
                // Input Ayah
                \App\Models\SiswaOrangTua::create([
                    'siswa_id' => $siswa->id,
                    'orang_tua_id' => $chunkOrtu->values()[0]->id,
                    'hubungan' => 'Ayah',
                    'kontak_utama' => true,
                ]);

                // Input Ibu
                \App\Models\SiswaOrangTua::create([
                    'siswa_id' => $siswa->id,
                    'orang_tua_id' => $chunkOrtu->values()[1]->id,
                    'hubungan' => 'Ibu',
                    'kontak_utama' => false,
                ]);
            }
        }
    }
}
