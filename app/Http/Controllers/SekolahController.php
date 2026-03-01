<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class SekolahController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Sekolah Index', only: ['index']),
            new Middleware('permission:Sekolah Create', only: ['store']),
            new Middleware('permission:Sekolah Edit', only: ['update']),
            new Middleware('permission:Sekolah Delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $search = $request->input('search');
        $sortFields = $request->input('sort');
        $sortOrders = $request->input('order');

        $query = Sekolah::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_sekolah', 'like', "%{$search}%")
                    ->orWhere('nss', 'like', "%{$search}%")
                    ->orWhere('npsn', 'like', "%{$search}%")
                    ->orWhere('alamat_sekolah', 'like', "%{$search}%")
                    ->orWhere('kecamatan', 'like', "%{$search}%")
                    ->orWhere('kabupaten_kota', 'like', "%{$search}%")
                    ->orWhere('provinsi', 'like', "%{$search}%");
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

        $sekolah = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $sekolah->items(),
            'meta' => [
                'total' => $sekolah->total(),
                'current_page' => $sekolah->currentPage(),
                'last_page' => $sekolah->lastPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string',
            'nss' => 'nullable|string',
            'npsn' => 'nullable|string|unique:sekolah,npsn',
            'alamat_sekolah' => 'nullable|string',
            'kecamatan' => 'nullable|string',
            'kabupaten_kota' => 'nullable|string',
            'provinsi' => 'nullable|string',
        ]);

        $sekolah = Sekolah::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Sekolah created successfully',
            'data' => $sekolah,
        ]);
    }

    public function update(Request $request, $id)
    {
        $sekolah = Sekolah::findOrFail($id);

        $request->validate([
            'nama_sekolah' => 'required|string',
            'nss' => 'nullable|string',
            'npsn' => 'nullable|string|unique:sekolah,npsn,' . $id,
            'alamat_sekolah' => 'nullable|string',
            'kecamatan' => 'nullable|string',
            'kabupaten_kota' => 'nullable|string',
            'provinsi' => 'nullable|string',
        ]);

        $sekolah->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Sekolah updated successfully',
            'data' => $sekolah,
        ]);
    }

    public function destroy($id)
    {
        $sekolah = Sekolah::findOrFail($id);
        $sekolah->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sekolah deleted successfully',
        ]);
    }
}
