<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\QuranSurah as QuranSurahModel;
use Illuminate\Support\Facades\Storage;

class QuranSurah extends Seeder
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
                ->get('https://quran-api.santrikoding.com/api/surah');

            if ($response->failed()) {
                $this->command->error('Gagal mengambil data. Status: ' . $response->status());
                return;
            }

            $surahs = $response->json();

            // Seed data surah
            $this->command->info('Menyimpan data surah...');
            $this->command->getOutput()->progressStart(count($surahs));

            foreach ($surahs as $surah) {
                QuranSurahModel::updateOrCreate(
                    ['nomor' => $surah['nomor']],
                    [
                        'nama'         => $surah['nama'],
                        'nama_latin'   => $surah['nama_latin'],
                        'jumlah_ayat'  => $surah['jumlah_ayat'],
                        'tempat_turun' => $surah['tempat_turun'],
                        'arti'         => $surah['arti'],
                        'deskripsi'    => $surah['deskripsi'],
                        'audio'        => $surah['audio'],
                    ]
                );
                $this->command->getOutput()->progressAdvance();
            }

            $this->command->getOutput()->progressFinish();
            $this->command->info('Berhasil menyalin ' . count($surahs) . ' surah.');

            // Download audio files
            $this->command->info('Mengunduh file audio...');
            $downloaded = 0;
            $skipped = 0;

            $this->command->getOutput()->progressStart(count($surahs));

            foreach ($surahs as $surah) {
                $audioUrl = $surah['audio'] ?? null;

                if (!$audioUrl) {
                    $this->command->getOutput()->progressAdvance();
                    continue;
                }

                // Generate local filename: audio/001.mp3, audio/002.mp3, etc.
                $filename = 'audio_quran/' . str_pad($surah['nomor'], 3, '0', STR_PAD_LEFT) . '.mp3';

                // Skip if audio file already exists
                if (Storage::disk('public')->exists($filename)) {
                    $skipped++;
                    $this->command->getOutput()->progressAdvance();
                    continue;
                }

                try {
                    $audioResponse = Http::timeout(30)
                        ->withoutVerifying()
                        ->get($audioUrl);

                    if ($audioResponse->successful()) {
                        Storage::disk('public')->put($filename, $audioResponse->body());
                        $downloaded++;
                    } else {
                        $this->command->warn("Gagal download audio surah {$surah['nomor']}: HTTP {$audioResponse->status()}");
                    }
                } catch (\Exception $e) {
                    $this->command->warn("Error download audio surah {$surah['nomor']}: " . $e->getMessage());
                }

                $this->command->getOutput()->progressAdvance();
            }

            $this->command->getOutput()->progressFinish();
            $this->command->info("Audio: {$downloaded} downloaded, {$skipped} skipped (sudah ada).");
        } catch (\Exception $e) {
            $this->command->error('Terjadi kesalahan: ' . $e->getMessage());
            $this->command->warn('Tips: Pastikan koneksi internet stabil.');
        }
    }
}
