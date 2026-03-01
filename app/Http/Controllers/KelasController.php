<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class KelasController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Kelas Index', only: ['index']),
            new Middleware('permission:Kelas Create', only: ['store']),
            new Middleware('permission:Kelas Edit', only: ['update']),
            new Middleware('permission:Kelas Delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $search = $request->input('search');
        $sortFields = $request->input('sort');
        $sortOrders = $request->input('order');

        $query = Kelas::query();

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
            $query->orderBy('created_at', 'desc');
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

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'paralel' => 'required|numeric',
            'tipe' => 'required|string',
        ]);

        $kelas = Kelas::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kelas created successfully',
            'data' => $kelas,
        ]);
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|unique:kelas,nama,' . $kelas->id,
            'paralel' => 'required|numeric',
            'tipe' => 'required|string',
        ]);

        $kelas->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kelas updated successfully',
            'data' => $kelas,
        ]);
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kelas deleted successfully',
        ]);
    }
}
