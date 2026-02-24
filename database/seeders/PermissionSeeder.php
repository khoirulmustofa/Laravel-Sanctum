<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=PermissionSeeder
     */
    public function run(): void
    {

        $permissions = [
            // ========== DASHBOARD ==========
            ['name' => 'Dashboard Index', 'group' => 'Dashboard'],

            // ========== USER ==========
            ['name' => 'User Index', 'group' => 'User'],
            ['name' => 'User Create', 'group' => 'User'],
            ['name' => 'User Edit', 'group' => 'User'],
            ['name' => 'User Delete', 'group' => 'User'],
            ['name' => 'User Assign Role', 'group' => 'User'],
            ['name' => 'User Assign Permission', 'group' => 'User'],

            // ========== ROLE ==========
            ['name' => 'Role Index', 'group' => 'Role'],
            ['name' => 'Role Create', 'group' => 'Role'],
            ['name' => 'Role Edit', 'group' => 'Role'],
            ['name' => 'Role Delete', 'group' => 'Role'],
            ['name' => 'Role Assign User', 'group' => 'Role'],
            ['name' => 'Role Assign Permission', 'group' => 'Role'],

            // ========== SETTING ==========
            ['name' => 'Setting Index', 'group' => 'Setting'],
            ['name' => 'Setting Edit', 'group' => 'Setting'],
        ];

        // 2. Lakukan Update atau Create
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                ['group' => $permission['group']]
            );
        }

        // 3. Hapus permission yang tidak ada di dalam daftar $permissions di atas
        $validNames = collect($permissions)->pluck('name')->toArray();

        Permission::whereNotIn('name', $validNames)->delete();
    }
}
