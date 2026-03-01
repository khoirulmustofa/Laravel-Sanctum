<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Siswa Index', only: ['index']),
            new Middleware('permission:Siswa Create', only: ['store']),
            new Middleware('permission:Siswa Edit', only: ['update']),
            new Middleware('permission:Siswa Delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $search = $request->input('search');
        $sortFields = $request->input('sort');
        $sortOrders = $request->input('order');

        $query = Siswa::query()
            ->select('siswa.*')
            ->leftJoin('sekolah', 'siswa.sekolah_id', '=', 'sekolah.id')
            ->leftJoin('siswa_orang_tua', 'siswa.id', '=', 'siswa_orang_tua.siswa_id')
            ->leftJoin('siswa_alamat', 'siswa.id', '=', 'siswa_alamat.siswa_id')
            ->with(['sekolah', 'alamat', 'orangTua']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('siswa.nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('siswa.nisn', 'like', "%{$search}%")
                    ->orWhere('siswa.nik', 'like', "%{$search}%")
                    ->orWhere('siswa.email_pribadi', 'like', "%{$search}%")
                    ->orWhere('sekolah.nama_sekolah', 'like', "%{$search}%")
                    ->orWhere('siswa_orang_tua.nama_ayah', 'like', "%{$search}%")
                    ->orWhere('siswa_orang_tua.nama_ibu', 'like', "%{$search}%")
                    ->orWhere('siswa_alamat.alamat_tempat_tinggal', 'like', "%{$search}%");
            });
        }

        if ($sortFields) {
            $fields = explode(',', $sortFields);
            $orders = explode(',', $sortOrders);

            foreach ($fields as $index => $field) {
                $direction = isset($orders[$index]) ? $orders[$index] : 'asc';

                if ($field === 'sekolah.nama_sekolah') {
                    $query->orderBy('sekolah.nama_sekolah', $direction);
                } else if ($field === 'alamat.alamat_tempat_tinggal') {
                    $query->orderBy('siswa_alamat.alamat_tempat_tinggal', $direction);
                } else if ($field === 'orang_tua.nama_ayah') {
                    $query->orderBy('siswa_orang_tua.nama_ayah', $direction);
                } else if ($field === 'orang_tua.nama_ibu') {
                    $query->orderBy('siswa_orang_tua.nama_ibu', $direction);
                } else {
                    $query->orderBy('siswa.' . $field, $direction);
                }
            }
        } else {
            $query->orderBy('siswa.created_at', 'desc');
        }

        $siswa = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $siswa->items(),
            'meta' => [
                'total' => $siswa->total(),
                'current_page' => $siswa->currentPage(),
                'last_page' => $siswa->lastPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sekolah_id' => 'nullable|exists:sekolah,id',
            'nama_lengkap' => 'required|string',
            'jenis_kelamin' => 'required|in:L,P',
            'nisn' => 'nullable|string|unique:siswa,nisn',
            'nik' => 'nullable|string',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string',
            'tinggi_badan' => 'nullable|integer',
            'berkebutuhan_khusus' => 'nullable|string',
            'no_telepon_rumah' => 'nullable|string',
            'jarak_ke_sekolah' => 'nullable|integer',
            'alat_transportasi' => 'nullable|string',
            'email_pribadi' => 'nullable|email',

            // Alamat validation
            'alamat' => 'nullable|array',
            'alamat.jenis_tinggal' => 'nullable|string',
            'alamat.alamat_tempat_tinggal' => 'nullable|string',
            'alamat.kelurahan' => 'nullable|string',
            'alamat.kecamatan' => 'nullable|string',
            'alamat.kabupaten_kota' => 'nullable|string',
            'alamat.provinsi' => 'nullable|string',

            // Orang Tua validation
            'orang_tua' => 'nullable|array',
            'orang_tua.nama_ayah' => 'nullable|string',
            'orang_tua.pekerjaan_ayah' => 'nullable|string',
            'orang_tua.pendidikan_ayah' => 'nullable|string',
            'orang_tua.penghasilan_ayah' => 'nullable|numeric',
            'orang_tua.nama_ibu' => 'nullable|string',
            'orang_tua.pekerjaan_ibu' => 'nullable|string',
            'orang_tua.pendidikan_ibu' => 'nullable|string',
            'orang_tua.penghasilan_ibu' => 'nullable|numeric',
            'orang_tua.nama_wali' => 'nullable|string',
            'orang_tua.pekerjaan_wali' => 'nullable|string',
            'orang_tua.pendidikan_wali' => 'nullable|string',
            'orang_tua.penghasilan_wali' => 'nullable|numeric',
        ]);

        return DB::transaction(function () use ($validated) {
            $siswa = Siswa::create(collect($validated)->except(['alamat', 'orang_tua'])->toArray());

            if (!empty($validated['alamat'])) {
                $siswa->alamat()->create($validated['alamat']);
            }

            if (!empty($validated['orang_tua'])) {
                $siswa->orangTua()->create($validated['orang_tua']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Siswa created successfully',
                'data' => $siswa->load(['alamat', 'orangTua']),
            ]);
        });
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $validated = $request->validate([
            'sekolah_id' => 'nullable|exists:sekolah,id',
            'nama_lengkap' => 'required|string',
            'jenis_kelamin' => 'required|in:L,P',
            'nisn' => 'nullable|string|unique:siswa,nisn,' . $id,
            'nik' => 'nullable|string',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string',
            'tinggi_badan' => 'nullable|integer',
            'berkebutuhan_khusus' => 'nullable|string',
            'no_telepon_rumah' => 'nullable|string',
            'jarak_ke_sekolah' => 'nullable|integer',
            'alat_transportasi' => 'nullable|string',
            'email_pribadi' => 'nullable|email',

            // Alamat validation
            'alamat' => 'nullable|array',
            'alamat.jenis_tinggal' => 'nullable|string',
            'alamat.alamat_tempat_tinggal' => 'nullable|string',
            'alamat.kelurahan' => 'nullable|string',
            'alamat.kecamatan' => 'nullable|string',
            'alamat.kabupaten_kota' => 'nullable|string',
            'alamat.provinsi' => 'nullable|string',

            // Orang Tua validation
            'orang_tua' => 'nullable|array',
            'orang_tua.nama_ayah' => 'nullable|string',
            'orang_tua.pekerjaan_ayah' => 'nullable|string',
            'orang_tua.pendidikan_ayah' => 'nullable|string',
            'orang_tua.penghasilan_ayah' => 'nullable|numeric',
            'orang_tua.nama_ibu' => 'nullable|string',
            'orang_tua.pekerjaan_ibu' => 'nullable|string',
            'orang_tua.pendidikan_ibu' => 'nullable|string',
            'orang_tua.penghasilan_ibu' => 'nullable|numeric',
            'orang_tua.nama_wali' => 'nullable|string',
            'orang_tua.pekerjaan_wali' => 'nullable|string',
            'orang_tua.pendidikan_wali' => 'nullable|string',
            'orang_tua.penghasilan_wali' => 'nullable|numeric',
        ]);

        return DB::transaction(function () use ($siswa, $validated) {
            $siswa->update(collect($validated)->except(['alamat', 'orang_tua'])->toArray());

            if (isset($validated['alamat'])) {
                $siswa->alamat()->updateOrCreate(['siswa_id' => $siswa->id], $validated['alamat']);
            }

            if (isset($validated['orang_tua'])) {
                $siswa->orangTua()->updateOrCreate(['siswa_id' => $siswa->id], $validated['orang_tua']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Siswa updated successfully',
                'data' => $siswa->load(['alamat', 'orangTua']),
            ]);
        });
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return response()->json([
            'success' => true,
            'message' => 'Siswa deleted successfully',
        ]);
    }

    // get Sekolah
    public function sekolah()
    {
        $sekolah = Sekolah::all();
        return response()->json([
            'success' => true,
            'data' => $sekolah,
        ]);
    }
}
