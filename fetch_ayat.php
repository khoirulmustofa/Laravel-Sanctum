<?php

/**
 * Script to fetch all ayat data from Quran API and save to storage/app/public/ayat.json
 * 
 * Run: php fetch_ayat.php
 * 
 * This script needs internet access to reach https://quran-api.santrikoding.com
 */

$allAyat = [];

for ($nomor = 1; $nomor <= 114; $nomor++) {
    $url = "https://quran-api.santrikoding.com/api/surah/{$nomor}";

    $json = @file_get_contents($url);

    if ($json === false) {
        echo "ERROR: Failed to fetch surah {$nomor}\n";
        continue;
    }

    $data = json_decode($json, true);
    $ayats = $data['ayat'] ?? [];

    foreach ($ayats as $ayat) {
        $allAyat[] = [
            'surah' => $ayat['surah'],
            'nomor' => $ayat['nomor'],
            'ar'    => $ayat['ar'],
            'tr'    => $ayat['tr'],
            'idn'   => $ayat['idn'],
        ];
    }

    echo "Surah {$nomor}: " . count($ayats) . " ayat fetched.\n";
}

$outputPath = __DIR__ . '/storage/app/public/ayat.json';
file_put_contents($outputPath, json_encode($allAyat, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

echo "\nDone! Total " . count($allAyat) . " ayat saved to {$outputPath}\n";
