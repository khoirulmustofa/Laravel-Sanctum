<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\QuranBookmark;
use App\Models\QuranSurah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuranController extends Controller
{
    /**
     * Mengambil daftar seluruh surah diurutkan berdasarkan nomor.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function surah(Request $request)
    {
        $quranSurah = QuranSurah::orderBy('nomor')->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil tampilkan surah',
            'data'    => $quranSurah,
        ]);
    }

    /**
     * Menampilkan detail surah berdasarkan nomor beserta daftar ayatnya.
     *
     * @param int $nomor
     * @return \Illuminate\Http\JsonResponse
     */
    public function surahDetail($nomor)
    {
        $surah = QuranSurah::with('ayats')->find($nomor);

        if (!$surah) {
            return response()->json([
                'success' => false,
                'message' => 'Surah tidak ditemukan'
            ], 404);
        }

        $suratSebelumnya = QuranSurah::find($nomor - 1);
        $suratSelanjutnya = QuranSurah::find($nomor + 1);

        return response()->json([
            'success'           => true,
            'message'           => 'Berhasil tampilkan detail surah',
            'data'              => $surah,
            'surat_sebelumnya'  => $suratSebelumnya ?: false,
            'surat_selanjutnya' => $suratSelanjutnya ?: false,
        ]);
    }

    /**
     * Menyimpan bookmark ayat Al-Quran.
     * Menggunakan firstOrCreate agar tidak terjadi duplikasi bookmark.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeBookmark(Request $request)
    {
        $request->validate([
            'surah_nomor' => 'required|integer|exists:quran_surah,nomor',
            'ayat_id'  => 'required|integer',
        ]);

        $bookmark = QuranBookmark::firstOrCreate([
            'user_id'     => $request->user()->id,
            'surah_nomor' => $request->surah_nomor,
            'ayat_id'  => $request->ayat_id,
        ]);

        $bookmark->load(['surah', 'ayat']);

        return response()->json([
            'success' => true,
            'message' => 'Bookmark berhasil disimpan',
            'data'    => $bookmark,
        ], 201);
    }

    /**
     * Menghapus bookmark ayat Al-Quran.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyBookmark($id)
    {
        $bookmark = QuranBookmark::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->first();

        if (!$bookmark) {
            return response()->json([
                'success' => false,
                'message' => 'Bookmark tidak ditemukan',
            ], 404);
        }

        $bookmark->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bookmark berhasil dihapus',
        ]);
    }
}
