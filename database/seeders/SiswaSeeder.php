<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sekolahs = Sekolah::all();

        // Prefix & lebar digit berurutan untuk NIS
        $nisPrefix = '202306';
        $nisWidth = 4; // hasil: 2023060001, 2023060002, ...

        for ($i = 0; $i < 300; $i++) {
            $noUrut = $i + 1;

            // Nama
            $namaLengkap = 'Siswa '.$noUrut;

            // Jenis kelamin bergantian L/P
            $jk = ($i % 2 === 0) ? 'L' : 'P';

            // NIS berformat: 202306 + 4 digit berleading zero
            $nis = $nisPrefix.str_pad((string) $noUrut, $nisWidth, '0', STR_PAD_LEFT);

            // NISN contoh 10 digit + running (pastikan tidak duplikat)
            // Misal base 1000000000, tambah no urut
            $nisn = (string) (1000000000 + $noUrut);

            // NIK contoh 16 digit (disarankan realistic/unik)
            // Misal base 3201010000000000 + no urut
            $nik = (string) (3201010000000000 + $noUrut);

            // Telepon (string agar leading zero aman)
            $noTelepon = '0812345678'.str_pad((string) $noUrut, 2, '0', STR_PAD_LEFT);

            // Email unik
            $email = 'email'.$noUrut.'@gmail.com';

            // Tempat & tanggal lahir
            $tempatLahir = 'Tempat Lahir '.$noUrut;
            // Usia kira2 12–14 th, random
            $tglLahir = Carbon::now()->subYears(12 + rand(0, 2))->subDays(rand(0, 365))->format('Y-m-d');

            // Ambil sekolah random (pastikan ada data sekolah)
            $sekolahId = $sekolahs->random()->id;

            // Gunakan nisn sebagai kunci unik (atau bisa nis sesuai kebutuhan)
            Siswa::updateOrCreate(
                [
                    'nisn' => $nisn, // key pencarian unik
                ],
                [
                    'nama_lengkap' => $namaLengkap,
                    'jenis_kelamin' => $jk,
                    'nis' => $nis,
                    'nik' => $nik,
                    'sekolah_id' => $sekolahId,
                    'tempat_lahir' => $tempatLahir,
                    'tanggal_lahir' => $tglLahir,
                    'agama' => 'Islam',
                    'tinggi_badan' => 160,
                    'berkebutuhan_khusus' => 'Tidak',
                    'no_telepon_rumah' => $noTelepon,
                    'jarak_ke_sekolah' => 10,
                    'alat_transportasi' => 'Asrama',
                    'email_pribadi' => $email,
                ]
            );
        }
    }
}
