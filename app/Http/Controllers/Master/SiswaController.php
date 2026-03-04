<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\OrangTua;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
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
            ->leftJoin('siswa_alamat', 'siswa.id', '=', 'siswa_alamat.siswa_id')
            ->with(['sekolah', 'alamat', 'parents']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('siswa.nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('siswa.nis', 'like', "%{$search}%")
                    ->orWhere('siswa.nisn', 'like', "%{$search}%")
                    ->orWhere('siswa.nik', 'like', "%{$search}%")
                    ->orWhere('siswa.email_pribadi', 'like', "%{$search}%")
                    ->orWhere('sekolah.nama_sekolah', 'like', "%{$search}%")
                    ->orWhere('siswa_alamat.alamat_tempat_tinggal', 'like', "%{$search}%")
                    ->orWhereHas('parents', function ($pq) use ($search) {
                        $pq->where('nama', 'like', "%{$search}%")
                            ->orWhere('no_telepon', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($sortFields) {
            $fields = explode(',', $sortFields);
            $orders = explode(',', $sortOrders);

            foreach ($fields as $index => $field) {
                $direction = isset($orders[$index]) ? $orders[$index] : 'asc';

                if ($field === 'sekolah.nama_sekolah') {
                    $query->orderBy('sekolah.nama_sekolah', $direction);
                } elseif ($field === 'alamat.alamat_tempat_tinggal') {
                    $query->orderBy('siswa_alamat.alamat_tempat_tinggal', $direction);
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

            // Parents validation
            'parents' => 'nullable|array',
            'parents.*.id' => 'nullable|uuid',
            'parents.*.nama' => 'required|string',
            'parents.*.jenis_kelamin' => 'required|in:L,P',
            'parents.*.pekerjaan' => 'nullable|string',
            'parents.*.pendidikan' => 'nullable|string',
            'parents.*.penghasilan' => 'nullable|numeric',
            'parents.*.no_telepon' => 'nullable|string',
            'parents.*.email' => 'nullable|email',
            'parents.*.alamat' => 'nullable|string',
            'parents.*.hubungan' => 'required|string',
            'parents.*.kontak_utama' => 'nullable|boolean',
        ]);

        return DB::transaction(function () use ($validated) {
            $siswa = Siswa::create(collect($validated)->except(['alamat', 'parents'])->toArray());

            if (! empty($validated['alamat'])) {
                $siswa->alamat()->create($validated['alamat']);
            }

            if (! empty($validated['parents'])) {
                $syncData = [];
                foreach ($validated['parents'] as $parentData) {
                    $parent = OrangTua::updateOrCreate(
                        ['email' => $parentData['email'] ?? null, 'nama' => $parentData['nama']],
                        collect($parentData)->except(['hubungan', 'kontak_utama', 'id'])->toArray()
                    );
                    $syncData[$parent->id] = [
                        'id' => \Illuminate\Support\Str::uuid(),
                        'hubungan' => $parentData['hubungan'],
                        'kontak_utama' => $parentData['kontak_utama'] ?? false,
                    ];
                }
                $siswa->parents()->sync($syncData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Siswa created successfully',
                'data' => $siswa->load(['alamat', 'parents']),
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

            // Parents validation
            'parents' => 'nullable|array',
            'parents.*.id' => 'nullable|uuid',
            'parents.*.nama' => 'required|string',
            'parents.*.jenis_kelamin' => 'required|in:L,P',
            'parents.*.pekerjaan' => 'nullable|string',
            'parents.*.pendidikan' => 'nullable|string',
            'parents.*.penghasilan' => 'nullable|numeric',
            'parents.*.no_telepon' => 'nullable|string',
            'parents.*.email' => 'nullable|email',
            'parents.*.alamat' => 'nullable|string',
            'parents.*.hubungan' => 'required|string',
            'parents.*.kontak_utama' => 'nullable|boolean',
        ]);

        return DB::transaction(function () use ($siswa, $validated) {
            $siswa->update(collect($validated)->except(['alamat', 'parents'])->toArray());

            if (isset($validated['alamat'])) {
                $siswa->alamat()->updateOrCreate(['siswa_id' => $siswa->id], $validated['alamat']);
            }

            if (isset($validated['parents'])) {
                $syncData = [];
                foreach ($validated['parents'] as $parentData) {
                    $parent = OrangTua::updateOrCreate(
                        ['email' => $parentData['email'] ?? null, 'nama' => $parentData['nama']],
                        collect($parentData)->except(['hubungan', 'kontak_utama', 'id'])->toArray()
                    );
                    $syncData[$parent->id] = [
                        'id' => \Illuminate\Support\Str::uuid(),
                        'hubungan' => $parentData['hubungan'],
                        'kontak_utama' => $parentData['kontak_utama'] ?? false,
                    ];
                }
                $siswa->parents()->sync($syncData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Siswa updated successfully',
                'data' => $siswa->load(['alamat', 'parents']),
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
