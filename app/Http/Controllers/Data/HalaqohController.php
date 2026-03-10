<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Models\Halaqoh;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class HalaqohController extends Controller implements HasMiddleware
{
    /**
     * Definisi Middleware untuk hak akses (Spatie Permission)
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Data Halaqoh View', only: ['index', 'options']),
            new Middleware('permission:Data Halaqoh Create', only: ['store']),
            new Middleware('permission:Data Halaqoh Edit', only: ['update']),
            new Middleware('permission:Data Halaqoh Delete', only: ['destroy']),
        ];
    }

    /**
     * Menampilkan daftar Halaqoh dengan fitur Search, Sort, dan Pagination
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $search = $request->input('search');
        $sortFields = $request->input('sort');
        $sortOrders = $request->input('order');

        // Gunakan eager loading 'user' agar tidak terjadi N+1 problem
        $query = Halaqoh::with('user')
            ->withCount(['siswas' => function ($query) use ($request) {
                $query->where('tahun_ajaran', $request->tahun_ajaran)
                    ->where('semester', $request->semester);
            }])
            ->where('tahun_ajaran', $request->tahun_ajaran)
            ->where('semester', $request->semester);

        // Pencarian berdasarkan nama halaqoh atau tahun ajaran
        // Pencarian berdasarkan nama halaqoh, tahun ajaran, atau nama guru
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Logika pengurutan (Sorting)
        if ($sortFields) {
            $fields = explode(',', $sortFields);
            $orders = explode(',', $sortOrders);

            foreach ($fields as $index => $field) {
                $direction = isset($orders[$index]) ? $orders[$index] : 'asc';
                $query->orderBy($field, $direction);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $halaqoh = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil daftar halaqoh',
            'data' => $halaqoh->items(),
            'meta' => [
                'total' => $halaqoh->total(),
                'current_page' => $halaqoh->currentPage(),
                'last_page' => $halaqoh->lastPage(),
            ],
        ]);
    }

    /**
     * Menyimpan data Halaqoh baru
     */
    public function store(Request $request)
    {
        $validated =  $request->validate([
            'nama'         => 'required|string|max:255',
            'user_id'      => [
                'nullable',
                'exists:users,id',
                // Cek apakah guru ini sudah punya halaqoh di periode yang sama
                Rule::unique('halaqoh')->where(function ($query) use ($request) {
                    return $query->where('tahun_ajaran', $request->tahun_ajaran)
                        ->where('semester', $request->semester);
                })
            ],
            'tahun_ajaran' => 'nullable|string|max:255',
            'semester'     => 'nullable|integer',
            'aktif'        => 'boolean'
        ]);

        // HasUuids pada model akan menangani pembuatan ID secara otomatis
        $halaqoh = Halaqoh::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Halaqoh berhasil dibuat',
            'data' => $halaqoh,
        ]);
    }

    /**
     * Memperbarui data Halaqoh
     */
    public function update(Request $request, $id)
    {
        $halaqoh = Halaqoh::findOrFail($id);

        $validated =  $request->validate([
            'nama'         => 'required|string|max:255',
            'user_id'      => [
                'nullable',
                'exists:users,id',
                // Abaikan ID halaqoh saat ini agar tidak terkena validasi unique diri sendiri
                Rule::unique('halaqoh')
                    ->ignore($id)
                    ->where(function ($query) use ($request) {
                        return $query->where('tahun_ajaran', $request->tahun_ajaran)
                            ->where('semester', $request->semester);
                    })
            ],
            'tahun_ajaran' => 'nullable|string|max:255',
            'semester'     => 'nullable|integer',
            'aktif'        => 'boolean'
        ]);

        $halaqoh->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Halaqoh berhasil diperbarui',
            'data' => $halaqoh,
        ]);
    }

    /**
     * Menghapus data Halaqoh (Soft Delete)
     */
    public function destroy($id)
    {
        $halaqoh = Halaqoh::findOrFail($id);
        $halaqoh->delete();

        return response()->json([
            'success' => true,
            'message' => 'Halaqoh berhasil dihapus',
        ]);
    }

    /**
     * Mengambil data opsi untuk dropdown (Guru, Tahun Ajaran, Semester)
     */
    public function options()
    {
        $halaqohs = \App\Models\Halaqoh::orderBy('nama')->get(['id', 'nama']);
        $tahunAjaran = \App\Models\TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $semesters = \App\Models\Semester::orderBy('semester', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'halaqohs' => $halaqohs,
                'tahun_ajaran' => $tahunAjaran,
                'semesters' => $semesters,
            ],
        ]);
    }


    /**
     * Menampilkan daftar siswa yang terdaftar dalam satu Halaqoh (Plotting)
     * Berdasarkan tahun ajaran dan semester tertentu.
     */
    public function indexPlotting(Request $request)
    {
        $halaqohId = $request->input('halaqoh_id');
        $tahunAjaran = $request->input('tahun_ajaran');
        $semester = $request->input('semester');
        $search = $request->input('search');

        if ($halaqohId) {
            // Jalur 1: Mengambil siswa yang SUDAH diplot di halaqoh tertentu
            $query = \App\Models\HalaqohSiswa::with(['siswa'])
                ->where('halaqoh_id', $halaqohId)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester);

            if ($search) {
                $query->whereHas('siswa', function ($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                });
            }

            $data = $query->get()->map(function ($hs) {
                return [
                    'id' => $hs->siswa->id,
                    'nis' => $hs->siswa->nis,
                    'nama_lengkap' => $hs->siswa->nama_lengkap,
                    'jenis_kelamin' => $hs->siswa->jenis_kelamin,
                    'aktif' => $hs->aktif,
                ];
            });
        } else {
            // Jalur 2: Mengambil SEMUA SISWA yang BELUM diplot di halaqoh manapun pada periode ini
            $query = \App\Models\Siswa::query();

            // Filter siswa yang belum punya record di halaqoh_siswa untuk periode ini
            $query->whereDoesntHave('halaqohs', function ($q) use ($tahunAjaran, $semester) {
                $q->where('halaqoh_siswa.tahun_ajaran', $tahunAjaran)
                    ->where('halaqoh_siswa.semester', $semester);
            });

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                });
            }

            $data = $query->limit(500)->get()->map(function ($siswa) {
                return [
                    'id' => $siswa->id,
                    'nis' => $siswa->nis,
                    'nama_lengkap' => $siswa->nama_lengkap,
                    'jenis_kelamin' => $siswa->jenis_kelamin,
                    'aktif' => false,
                ];
            });
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Memindahkan atau mendaftarkan siswa ke Halaqoh target (Transfer)
     */
    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'target_halaqoh_id' => 'required|exists:halaqoh,id',
            'tahun_ajaran' => 'required|string',
            'semester' => 'required|integer',
        ]);

        foreach ($validated['student_ids'] as $siswaId) {
            \App\Models\HalaqohSiswa::updateOrCreate(
                [
                    'siswa_id' => $siswaId,
                    'tahun_ajaran' => $validated['tahun_ajaran'],
                    'semester' => $validated['semester'],
                ],
                [
                    'halaqoh_id' => $validated['target_halaqoh_id'],
                    'aktif' => true,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Siswa berhasil diplot ke kelompok halaqoh',
        ]);
    }
}
