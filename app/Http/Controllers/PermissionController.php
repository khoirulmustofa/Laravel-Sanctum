<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        // For demonstration, we manually add 'group' to the response
        // In a real database, you would add a 'group' column to the permissions table
        $permissions = Permission::all()->map(function ($p) {
            $p->group = $this->deriveGroup($p->name);
            return $p;
        });

        return response()->json([
            'success' => true,
            'data' => $permissions
        ]);
    }

    /**
     * Helper to derive group if column doesn't exist yet.
     * Use user provided sample logic/data.
     */
    private function deriveGroup($name)
    {
        if (str_contains($name, 'User')) return 'User';
        if (str_contains($name, 'Role')) return 'Role';
        if (str_contains($name, 'Product')) return 'Product';
        if (str_contains($name, 'Category')) return 'Category';

        $parts = explode(' ', $name);
        return count($parts) > 1 ? $parts[0] : 'General';
    }
}
