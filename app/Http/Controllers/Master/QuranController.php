<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\QuranBookmark;
use App\Models\QuranSurah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class QuranController extends Controller implements HasMiddleware
{
     public static function middleware(): array
    {
        return [
            new Middleware('permission:Quran Reading', only: ['surah', 'surahDetail', 'listBookmarks', 'storeBookmark', 'destroyBookmark']),
        ];
    }

    /**
     * Mengambil daftar seluruh surah diurutkan berdasarkan nomor.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function surah(Request $request)
    {
        $quranSurah = QuranSurah::orderBy('number')->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil tampilkan surah',
            'data'    => $quranSurah,
        ]);
    }

    /**
     * Menampilkan detail surah berdasarkan nomor beserta daftar ayatnya.
     *
     * @param int $surah
     * @return \Illuminate\Http\JsonResponse
     */
    public function surahDetail($number)
    {
        $surah = QuranSurah::with('ayats' )->find($number);

        if (!$surah) {
            return response()->json([
                'success' => false,
                'message' => 'Surah tidak ditemukan'
            ], 404);
        }

        $suratSebelumnya = QuranSurah::find($number - 1);
        $suratSelanjutnya = QuranSurah::find($number + 1);

        return response()->json([
            'success'           => true,
            'message'           => 'Berhasil tampilkan detail surah',
            'data'              => $surah,
            'surat_sebelumnya'  => $suratSebelumnya ?: false,
            'surat_selanjutnya' => $suratSelanjutnya ?: false,
        ]);
    }

    /**
     * Menampilkan daftar bookmark milik user yang sedang login.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listBookmarks(Request $request)
    {
        $bookmarks = QuranBookmark::where('user_id', $request->user()->id)
            ->with(['surah', 'ayat'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil tampilkan bookmark',
            'data'    => $bookmarks,
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
            'surah_nomor' => 'required|integer|exists:quran_surah,number',
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
