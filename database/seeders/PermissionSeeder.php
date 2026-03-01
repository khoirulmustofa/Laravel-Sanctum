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

            // ========== MASTER ==========
            ['name' => 'Master Index', 'group' => 'Master'],

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

            // ========== SEKOLAH ==========
            ['name' => 'Sekolah Index', 'group' => 'Sekolah'],
            ['name' => 'Sekolah Create', 'group' => 'Sekolah'],
            ['name' => 'Sekolah Edit', 'group' => 'Sekolah'],
            ['name' => 'Sekolah Delete', 'group' => 'Sekolah'],

            // ========== TAHUN AJARAN ==========
            ['name' => 'Tahun Ajaran Index', 'group' => 'Tahun Ajaran'],
            ['name' => 'Tahun Ajaran Create', 'group' => 'Tahun Ajaran'],
            ['name' => 'Tahun Ajaran Edit', 'group' => 'Tahun Ajaran'],
            ['name' => 'Tahun Ajaran Delete', 'group' => 'Tahun Ajaran'],

            // ========== KELAS ==========
            ['name' => 'Kelas Index', 'group' => 'Kelas'],
            ['name' => 'Kelas Create', 'group' => 'Kelas'],
            ['name' => 'Kelas Edit', 'group' => 'Kelas'],
            ['name' => 'Kelas Delete', 'group' => 'Kelas'],

            // ========== SISWA ==========
            ['name' => 'Siswa Index', 'group' => 'Siswa'],
            ['name' => 'Siswa Create', 'group' => 'Siswa'],
            ['name' => 'Siswa Edit', 'group' => 'Siswa'],
            ['name' => 'Siswa Delete', 'group' => 'Siswa'],
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
