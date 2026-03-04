<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\OrangTua;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class OrangTuaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Orang Tua Index', only: ['index']),
            new Middleware('permission:Orang Tua Create', only: ['store']),
            new Middleware('permission:Orang Tua Edit', only: ['update']),
            new Middleware('permission:Orang Tua Delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $search = $request->input('search');
        $sortFields = $request->input('sort');
        $sortOrders = $request->input('order');

        $query = OrangTua::with('siswas');

        // Pencarian berdasarkan nama, email, atau no telepon
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('no_telepon', 'like', "%{$search}%")
                    ->orWhere('pekerjaan', 'like', "%{$search}%");
            });
        }

        if ($sortFields) {
            $fields = explode(',', $sortFields);
            $orders = explode(',', $sortOrders);
            foreach ($fields as $index => $field) {
                $direction = $orders[$index] ?? 'asc';
                $query->orderBy($field, $direction);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $orangTua = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $orangTua->items(),
            'meta' => [
                'total' => $orangTua->total(),
                'current_page' => $orangTua->currentPage(),
                'last_page' => $orangTua->lastPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'email' => 'nullable|email|unique:orang_tua,email',
            'no_telepon' => 'nullable|string',
            'penghasilan' => 'nullable|numeric',
        ]);

        $orangTua = OrangTua::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data Orang Tua berhasil ditambahkan',
            'data' => $orangTua,
        ]);
    }

    public function update(Request $request, $id)
    {
        $orangTua = OrangTua::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'email' => 'nullable|email|unique:orang_tua,email,' . $id,
            'no_telepon' => 'nullable|string',
            'penghasilan' => 'nullable|numeric',
        ]);

        $orangTua->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data Orang Tua berhasil diperbarui',
            'data' => $orangTua,
        ]);
    }

    public function destroy($id)
    {
        $orangTua = OrangTua::findOrFail($id);
        $orangTua->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Orang Tua berhasil dihapus',
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $query = OrangTua::query();

        if ($search) {
            $query->where('nama', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('no_telepon', 'like', "%{$search}%");
        }

        return response()->json([
            'success' => true,
            'data' => $query->limit(20)->get(),
        ]);
    }
}
