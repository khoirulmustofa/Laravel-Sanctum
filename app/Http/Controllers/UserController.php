<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:User Index', only: ['index']),
            new Middleware('permission:User Create', only: ['store']),
            new Middleware('permission:User Edit', only: ['update']),
            new Middleware('permission:User Delete', only: ['destroy']),
            new Middleware('permission:User Assign Permission', only: ['permissions', 'assignPermission']),
            new Middleware('permission:User Assign Role', only: ['roles', 'assignRole']),
        ];
    }

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $search = $request->input('search');
        $sortFields = $request->input('sort');
        $sortOrders = $request->input('order');

        // 2. Query dasar
        $query = User::withCount(['roles', 'permissions']);

        // 3. Logika Pencarian (Global Filter)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 4. Logika Sorting (Single atau Multi Sort)
        if ($sortFields) {
            $fields = explode(',', $sortFields);
            $orders = explode(',', $sortOrders);

            foreach ($fields as $index => $field) {
                // Pastikan order tersedia, jika tidak default ke 'asc'
                $direction = isset($orders[$index]) ? $orders[$index] : 'asc';
                $query->orderBy($field, $direction);
            }
        } else {
            // Default sort jika tidak ada kiriman dari client
            $query->orderBy('id', 'desc');
        }

        // 5. Eksekusi dengan Pagination
        $users = $query->paginate($limit, ['*'], 'page', $page);
        $users->getCollection()->transform(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles_count' => $user->roles_count,
                // Menghitung total unik permission (Direct + Role)
                'permissions_count' => $user->getAllPermissions()->count(),
            ];
        });

        // 6. Return Response (Sesuaikan dengan format Nuxt kamu)
        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'meta' => [
                'total' => $users->total(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }

    public function update(Request $request, $key)
    {
        // 1. Validation (Allow string or file)
        $request->validate([
            'value' => 'required',
        ]);

        // 2. Find or create the setting
        $setting = Setting::firstOrNew(['key' => $key]);

        // 3. Handle File vs String
        if ($request->hasFile('value')) {
            $file = $request->file('value');

            // 1. Buat nama file baru: setting_1708310000.jpg
            $extension = $file->getClientOriginalExtension();
            $fileName = 'setting_'.time().'.'.$extension;

            // 2. Simpan ke folder 'uploads/settings' di dalam disk 'public'
            // Ini akan tersimpan di: storage/app/public/uploads/settings
            $path = $file->storeAs('uploads/settings', $fileName, 'public');

            $setting->value = $path;
        } else {
            // Save as normal string
            $setting->value = $request->value;
        }

        $setting->save();

        // 4. Kirim respon balik ke Nuxt
        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully',
            'data' => $setting,
        ]);
    }

    public function roles(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $search = $request->input('search');
        $sortFields = $request->input('sort');
        $sortOrders = $request->input('order');

        $query = Role::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($sortFields) {
            $fields = explode(',', $sortFields);
            $orders = explode(',', $sortOrders);

            foreach ($fields as $index => $field) {
                $direction = isset($orders[$index]) ? $orders[$index] : 'asc';
                $query->orderBy($field, $direction);
            }
        } else {
            $query->orderBy('name', 'asc');
        }

        $roles = $query->paginate($limit, ['*'], 'page', $page);

        $userRoleNames = $user->roles->pluck('name')->toArray();

        $data = collect($roles->items())->map(function ($role) use ($userRoleNames) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'assigned' => in_array($role->name, $userRoleNames),
            ];
        });

        return response()->json([
            'success' => true,
            'user_name' => $user->name,
            'data' => $data,
            'meta' => [
                'total' => $roles->total(),
                'current_page' => $roles->currentPage(),
                'last_page' => $roles->lastPage(),
            ],
        ]);
    }

    public function assignRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->action === 'assign') {
            $user->assignRole($request->role);

            return response()->json([
                'success' => true,
                'message' => "Role '{$request->role}' assigned to user '{$user->name}' successfully.",
            ]);
        } else {
            $user->removeRole($request->role);

            return response()->json([
                'success' => true,
                'message' => "Role '{$request->role}' removed from user '{$user->name}' successfully.",
            ]);
        }
    }

    public function permissions(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $search = $request->input('search');
        $sortFields = $request->input('sort');
        $sortOrders = $request->input('order');

        $query = Permission::query();

        // 1. Logic Search (Gunakan Parameter Grouping agar tidak merusak query lain)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('group', 'like', "%{$search}%");
            });
        }

        // 2. Logic Sorting
        if ($sortFields) {
            $fields = explode(',', $sortFields);
            $orders = explode(',', $sortOrders);

            foreach ($fields as $index => $field) {
                // Pastikan field yang dikirim valid untuk menghindari SQL Injection manual
                $direction = (isset($orders[$index]) && strtolower($orders[$index]) === 'desc') ? 'desc' : 'asc';
                $query->orderBy($field, $direction);
            }
        } else {
            // Default sorting jika tidak ada request sort
            $query->orderBy('name', 'asc');
        }

        // 3. Eksekusi Pagination
        $permissions = $query->paginate($limit, ['*'], 'page', $page);

        // Kelompokkan ID berdasarkan sumbernya
        $directPermissionIds = $user->permissions->pluck('id')->toArray();
        $viaRolePermissionIds = $user->getPermissionsViaRoles()->pluck('id')->toArray();

        $data = collect($permissions->items())->map(function ($permission) use ($directPermissionIds, $viaRolePermissionIds) {
            $isDirect = in_array($permission->id, $directPermissionIds);
            $isViaRole = in_array($permission->id, $viaRolePermissionIds);

            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'group' => $permission->group,
                'assigned' => ($isDirect || $isViaRole),
                'source' => [
                    'is_direct' => $isDirect,
                    'is_via_role' => $isViaRole,
                    'label' => $this->getPermissionLabel($isDirect, $isViaRole),
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'user_name' => $user->name,
            'data' => $data,
            'meta' => [
                'total' => $permissions->total(),
                'current_page' => $permissions->currentPage(),
                'last_page' => $permissions->lastPage(),
            ],
        ]);
    }

    /**
     * Helper untuk memberikan label string yang user-friendly
     */
    private function getPermissionLabel($isDirect, $isViaRole)
    {
        if ($isDirect && $isViaRole) {
            return 'Direct & Role';
        }
        if ($isDirect) {
            return 'Direct';
        }
        if ($isViaRole) {
            return 'Via Role';
        }

        return 'None';
    }

    public function assignPermission(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->action === 'assign') {
            $user->givePermissionTo($request->permission);

            return response()->json([
                'success' => true,
                'message' => "Permission '{$request->permission}' assigned to user '{$user->name}' successfully.",
            ]);
        } else {
            $user->revokePermissionTo($request->permission);

            return response()->json([
                'success' => true,
                'message' => "Permission '{$request->permission}' removed from user '{$user->name}' successfully.",
            ]);
        }
    }
}
