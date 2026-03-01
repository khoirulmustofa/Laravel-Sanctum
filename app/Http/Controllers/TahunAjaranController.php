<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class TahunAjaranController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Tahun Ajaran Index', only: ['index']),
            new Middleware('permission:Tahun Ajaran Create', only: ['store']),
            new Middleware('permission:Tahun Ajaran Edit', only: ['update']),
            new Middleware('permission:Tahun Ajaran Delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $search = $request->input('search');
        $sortFields = $request->input('sort');
        $sortOrders = $request->input('order');

        $query = TahunAjaran::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
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
            $query->orderBy('created_at', 'desc');
        }

        $tahunAjaran = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $tahunAjaran->items(),
            'meta' => [
                'total' => $tahunAjaran->total(),
                'current_page' => $tahunAjaran->currentPage(),
                'last_page' => $tahunAjaran->lastPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string',
            'tanggal_mulai' => 'required', // Hapus 'date' rule sementara jika masih konflik dengan format ISO
            'tanggal_selesai' => 'required',
            'aktif' => 'nullable|boolean',
        ]);

        // Bersihkan data tanggal sebelum masuk ke database
        $validated['tanggal_mulai'] = Carbon::parse($request->tanggal_mulai)->format('Y-m-d');
        $validated['tanggal_selesai'] = Carbon::parse($request->tanggal_selesai)->format('Y-m-d');

        // Pastikan 'aktif' memiliki default value jika kosong
        $validated['aktif'] = $request->aktif ?? false;

        $tahunAjaran = TahunAjaran::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tahun Ajaran created successfully',
            'data' => $tahunAjaran,
        ]);
    }

    public function update(Request $request, $id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|unique:tahun_ajaran,nama,' . $tahunAjaran->id,
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
            'aktif' => 'nullable|boolean',
        ]);

        // Bersihkan data tanggal sebelum masuk ke database
        $validated['tanggal_mulai'] = Carbon::parse($request->tanggal_mulai)->format('Y-m-d');
        $validated['tanggal_selesai'] = Carbon::parse($request->tanggal_selesai)->format('Y-m-d');

        // Pastikan 'aktif' memiliki default value jika kosong
        $validated['aktif'] = $request->aktif ?? false;

        $tahunAjaran->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tahun Ajaran updated successfully',
            'data' => $tahunAjaran,
        ]);
    }

    public function destroy($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tahun Ajaran deleted successfully',
        ]);
    }
}
