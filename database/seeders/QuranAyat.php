<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\QuranAyat as QuranAyatModel;
use App\Models\QuranSurah;
use Illuminate\Support\Facades\Http;

class QuranAyat extends Seeder
{
    /**
     * Run the database seeds.
     * Read ayat data from storage/app/public/ayat.json
     * and seed the quran_ayat table.
     *
     * To generate ayat.json, run: php fetch_ayat.php
     */
    public function run(): void
    {
        // Ambil semua surah dari database yang sudah di-seed sebelumnya
        $quranSurah = QuranSurah::all();

        if ($quranSurah->isEmpty()) {
            $this->command->error('Data Surah kosong! Jalankan seeder Surah terlebih dahulu.');
            return;
        }

        $this->command->info('Sedang mengambil data ayat dari API...');

        // Inisialisasi progress bar agar kita tahu prosesnya sudah sampai mana
        $this->command->getOutput()->progressStart($quranSurah->count());

        foreach ($quranSurah as $surah) {
            try {
                $response = Http::timeout(20) // Naikkan timeout karena data ayat cukup besar
                    ->withoutVerifying()
                    ->get('https://quran-api.santrikoding.com/api/surah/' . $surah->nomor);

                if ($response->failed()) {
                    $this->command->warn("\nGagal mengambil surah nomor: " . $surah->nomor);
                    continue; // Lanjut ke surah berikutnya jika satu gagal
                }

                $data = $response->json();

                // Perhatikan: Data ayat ada di dalam key 'ayat'
                $ayats = $data['ayat'] ?? [];

                foreach ($ayats as $ayat) {
                    QuranAyatModel::updateOrCreate(
                        [
                            'surah' => $ayat['surah'], // nomor surah
                            'nomor' => $ayat['nomor'], // nomor ayat
                        ],
                        [
                            'ar'  => $ayat['ar'],
                            'tr'  => $ayat['tr'],
                            'idn' => $ayat['idn'],
                        ]
                    );
                }
            } catch (\Exception $e) {
                $this->command->error("\nError pada surah {$surah->nomor}: " . $e->getMessage());
            }

            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info('Berhasil menyalin seluruh ayat ke database.');
    }
}
