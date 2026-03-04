<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class KelasController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Data Kelas Index', only: ['index']),
        ];
    }

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $search = $request->input('search');
        $sortFields = $request->input('sort');
        $sortOrders = $request->input('order');


        $query = Kelas::withCount('siswa_kelas');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('paralel', 'like', "%{$search}%")
                    ->orWhere('tipe', 'like', "%{$search}%");
            });
        }

        if ($sortFields) {
            $fields = explode(',', $sortFields);
            $orders = explode(',', $sortOrders);

            foreach ($fields as $index => $field) {
                $direction = isset($orders[$index]) ? $orders[$index] : 'asc';
                $query->orderBy($field, $direction);
            }
        } else {
            $query->orderBy('nama', 'asc');
        }

        $kelas = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $kelas->items(),
            'meta' => [
                'total' => $kelas->total(),
                'current_page' => $kelas->currentPage(),
                'last_page' => $kelas->lastPage(),
            ],
        ]);
    }


    public function siswa(Request $request, $id)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $search = $request->input('search');
        $sortFields = $request->input('sort');
        $sortOrders = $request->input('order');

        $query = \App\Models\SiswaKelas::where('kelas_id', $id)

            ->with(['siswa']);

        if ($search) {
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        if ($sortFields) {
            $fields = explode(',', $sortFields);
            $orders = explode(',', $sortOrders);

            foreach ($fields as $index => $field) {
                $direction = isset($orders[$index]) ? $orders[$index] : 'asc';
                if ($field === 'nama' || $field === 'nisn') {
                    // Note: This join might need to be careful with column names
                    $query->join('siswa', 'siswa_kelas.siswa_id', '=', 'siswa.id')
                        ->orderBy("siswa.{$field}", $direction)
                        ->select('siswa_kelas.*');
                } else {
                    $query->orderBy($field, $direction);
                }
            }
        }

        $siswaKelas = $query->paginate($limit, ['*'], 'page', $page);

        $data = collect($siswaKelas->items())->map(function ($sk) {
            return [
                'id' => $sk->siswa->id,
                'nis' => $sk->siswa->nis,
                'nama_lengkap' => $sk->siswa->nama_lengkap,
                'jenis_kelamin' => $sk->siswa->jenis_kelamin,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'total' => $siswaKelas->total(),
                'current_page' => $siswaKelas->currentPage(),
                'last_page' => $siswaKelas->lastPage(),
            ],
        ]);
    }

    public function options()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'kelas' => Kelas::orderBy('nama')->get(),
                'tahun_ajaran' => \App\Models\TahunAjaran::orderBy('tahun_ajaran', 'desc')->get(),
                'semesters' => \App\Models\Semester::orderBy('semester')->get(),
            ]
        ]);
    }

    public function indexPlotting(Request $request)
    {
        $kelasId = $request->input('kelas_id');
        $tahunAjaran = $request->input('tahun_ajaran');
        $semester = $request->input('semester');
        $search = $request->input('search');

        $query = \App\Models\SiswaKelas::with(['siswa'])
            ->where('kelas_id', $kelasId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester);

        if ($search) {
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $data = $query->get()->map(function ($sk) {
            return [
                'id' => $sk->siswa->id,
                'nis' => $sk->siswa->nis,
                'nama_lengkap' => $sk->siswa->nama_lengkap,
                'jenis_kelamin' => $sk->siswa->jenis_kelamin,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'siswa_ids' => 'required|array',
            'target_kelas_id' => 'required|exists:kelas,id',
            'target_tahun_ajaran' => 'required|string',
            'target_semester' => 'required|integer',
        ]);

        foreach ($validated['siswa_ids'] as $siswaId) {
            \App\Models\SiswaKelas::updateOrCreate(
                [
                    'siswa_id' => $siswaId,
                    'tahun_ajaran' => $validated['target_tahun_ajaran'],
                    'semester' => $validated['target_semester'],
                ],
                [
                    'kelas_id' => $validated['target_kelas_id'],
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Siswa berhasil ditransfer',
        ]);
    }
}
