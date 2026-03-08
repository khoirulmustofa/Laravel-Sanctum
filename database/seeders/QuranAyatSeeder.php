<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\QuranAyat as QuranAyatModel;
use App\Models\QuranSurah;
use Illuminate\Support\Facades\Http;

class QuranAyatSeeder extends Seeder
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
        $quranSurah = QuranSurah::all();

        if ($quranSurah->isEmpty()) {
            $this->command->error('Data Surah kosong! Jalankan seeder Surah terlebih dahulu.');
            return;
        }

        $this->command->info('Sedang mengambil data ayat dari API...');
        $this->command->getOutput()->progressStart($quranSurah->count());

        foreach ($quranSurah as $surah) {

            try {

                $response = Http::timeout(20)
                    ->withoutVerifying()
                    ->get("https://muslim-api-three.vercel.app/v1/quran/ayah/surah?id={$surah->number}");

                if ($response->failed()) {
                    $this->command->warn("\nGagal mengambil surah nomor: {$surah->number}");
                    continue;
                }

                $ayats = $response->json('data');

                foreach ($ayats as $ayat) {

                    $textArab = $ayat['arab'];
                    $basmalah = "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ";

                    // hapus bismillah jika bukan surah 1 dan 9
                    if ($ayat['ayah'] == 1 && $surah->number != 1 && $surah->number != 9) {

                        if (str_starts_with($textArab, $basmalah)) {
                            $textArab = trim(str_replace($basmalah, '', $textArab));
                        }
                    }

                    QuranAyatModel::updateOrCreate(
                        [
                            'surah' => $ayat['surah'],
                            'ayah' => $ayat['ayah'],
                        ],
                        [
                            'arab' => $textArab,
                            'latin' => $ayat['latin'],
                            'page' => $ayat['page'],
                            'juz' => $ayat['juz'],
                            'hizb' => $ayat['hizb'],
                            'asbab' => $ayat['asbab'],
                            'audio' => $ayat['audio'],
                            'theme' => $ayat['theme'],
                            'text' => $ayat['text'],
                            'notes' => $ayat['notes'],
                        ]
                    );
                }
            } catch (\Exception $e) {
                $this->command->error("\nError pada surah {$surah->number}: " . $e->getMessage());
            }

            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info('Berhasil menyalin seluruh ayat ke database.');
    }
}
