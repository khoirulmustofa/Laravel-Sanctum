<?php

namespace App\Http\Controllers;

use Database\Seeders\PermissionSeeder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Role Index', only: ['index']),
            new Middleware('permission:Role Create', only: ['store']),
            new Middleware('permission:Role Edit', only: ['update']),
            new Middleware('permission:Role Delete', only: ['destroy']),
            new Middleware('permission:Role Assign Permission', only: ['permissions', 'assignPermission']),
            new Middleware('permission:Role Assign User', only: ['users', 'assignUser']),
        ];
    }

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $search = $request->input('search');
        $sortFields = $request->input('sort');
        $sortOrders = $request->input('order');

        $query = Role::withCount(['users', 'permissions']);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
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
            $query->orderBy('id', 'desc');
        }

        $roles = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $roles->items(),
            'meta' => [
                'total' => $roles->total(),
                'current_page' => $roles->currentPage(),
                'last_page' => $roles->lastPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'data' => $role,
        ]);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully',
            'data' => $role,
        ]);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully',
        ]);
    }

    public function permissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $search = $request->input('search');
        $sortFields = $request->input('sort');
        $sortOrders = $request->input('order');

        $query = Permission::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('group', 'like', "%{$search}%");
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

        $permissions = $query->paginate($limit, ['*'], 'page', $page);

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        $data = collect($permissions->items())->map(function ($permission) use ($rolePermissions) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'group' => $permission->group,
                'assigned' => in_array($permission->name, $rolePermissions),
            ];
        });

        return response()->json([
            'success' => true,
            'role_name' => $role->name,
            'data' => $data,
            'meta' => [
                'total' => $permissions->total(),
                'current_page' => $permissions->currentPage(),
                'last_page' => $permissions->lastPage(),
            ],
        ]);
    }

    public function assignPermission(Request $request, $id)
    {
        try {

            $role = Role::findOrFail($id);
            if ($request->action === 'assign') {
                $role->givePermissionTo($request->permission);

                return response()->json([
                    'success' => true,
                    'message' => "Permission assigned to role '{$role->name}' successfully.",
                ]);
            } else {
                $role->revokePermissionTo($request->permission);

                return response()->json([
                    'success' => true,
                    'message' => "Permission revoked from role '{$role->name}' successfully.",
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error assigning permission: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign permission. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function users(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $search = $request->input('search');
        $sortFields = $request->input('sort');
        $sortOrders = $request->input('order');

        $query = \App\Models\User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
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
            $query->orderBy('name', 'asc');
        }

        $users = $query->paginate($limit, ['*'], 'page', $page);

        $data = collect($users->items())->map(function ($user) use ($role) {
            $user->assigned = $user->hasRole($role->name);

            return $user;
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'total' => $users->total(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }

    public function assignUser(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $user = \App\Models\User::findOrFail($request->user_id);

        if ($request->action === 'assign') {
            $user->assignRole($role->name);

            return response()->json([
                'success' => true,
                'message' => "User '{$user->name}' assigned to role '{$role->name}' successfully.",
            ]);
        } else {
            $user->removeRole($role->name);

            return response()->json([
                'success' => true,
                'message' => "User '{$user->name}' removed from role '{$role->name}' successfully.",
            ]);
        }
    }

    public function permissionSeeder()
    {
        try {
            // Opsi 1: Memanggil Class langsung
            $seeder = new PermissionSeeder();
            $seeder->run();

            // Opsi 2: Menggunakan Artisan Command (Lebih bersih jika seeder kompleks)
            // Artisan::call('db:seed', ['--class' => 'PermissionSeeder']);

            return response()->json([
                'success' => true,
                'message' => "Permission seeder run successfully.",
                'timestamp' => now()->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Error running seeder: " . $e->getMessage(),
            ], 500);
        }
    }
}
