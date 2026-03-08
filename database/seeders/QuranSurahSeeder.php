<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\QuranSurah as QuranSurahModel;
use Illuminate\Support\Facades\Storage;

class QuranSurahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Fetch data from https://quran-api.santrikoding.com/api/surah
     * and seed the quran_surah table, including downloading audio files.
     */
    public function run(): void
    {
        $this->command->info('Sedang mengambil data dari API SantriKoding...');

        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->get('https://muslim-api-three.vercel.app/v1/quran/surah');

            if ($response->failed()) {
                $this->command->error('Gagal mengambil data. Status: ' . $response->status());
                return;
            }

            $surahs = $response->json('data');

            $this->command->info('Menyimpan data surah...');
            $this->command->getOutput()->progressStart(count($surahs));

            foreach ($surahs as $surah) {

                QuranSurahModel::updateOrCreate(
                    ['number' => $surah['number']],
                    [
                        'sequence' => $surah['sequence'],
                        'number_of_verses' => $surah['number_of_verses'],

                        'name_short' => $surah['name_short'],
                        'name_long' => $surah['name_long'],

                        'name_en' => $surah['name_en'],
                        'name_id' => $surah['name_id'],

                        'translation_en' => $surah['translation_en'],
                        'translation_id' => $surah['translation_id'],

                        'revelation' => $surah['revelation'],
                        'revelation_en' => $surah['revelation_en'],
                        'revelation_id' => $surah['revelation_id'],

                        'tafsir' => $surah['tafsir'],
                        'audio_url' => $surah['audio_url'],
                    ]
                );

                $this->command->getOutput()->progressAdvance();
            }

            $this->command->getOutput()->progressFinish();
            $this->command->info('Berhasil menyalin ' . count($surahs) . ' surah.');
        } catch (\Exception $e) {
            $this->command->error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
