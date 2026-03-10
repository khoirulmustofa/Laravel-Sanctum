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
            ['name' => 'Dashboard View', 'group' => 'Dashboard'],

            // ========== MASTER ==========
            ['name' => 'Master View', 'group' => 'Master'],

            // ========== USER ==========
            ['name' => 'User View', 'group' => 'User'],
            ['name' => 'User Create', 'group' => 'User'],
            ['name' => 'User Edit', 'group' => 'User'],
            ['name' => 'User Delete', 'group' => 'User'],
            ['name' => 'User Assign Role', 'group' => 'User'],
            ['name' => 'User Assign Permission', 'group' => 'User'],

            // ========== ROLE ==========
            ['name' => 'Role View', 'group' => 'Role'],
            ['name' => 'Role Create', 'group' => 'Role'],
            ['name' => 'Role Edit', 'group' => 'Role'],
            ['name' => 'Role Delete', 'group' => 'Role'],
            ['name' => 'Role Assign User', 'group' => 'Role'],
            ['name' => 'Role Assign Permission', 'group' => 'Role'],

            // ========== SETTING ==========
            ['name' => 'Setting View', 'group' => 'Setting'],
            ['name' => 'Setting Edit', 'group' => 'Setting'],

            // ========== SEKOLAH ==========
            ['name' => 'Sekolah View', 'group' => 'Sekolah'],
            ['name' => 'Sekolah Create', 'group' => 'Sekolah'],
            ['name' => 'Sekolah Edit', 'group' => 'Sekolah'],
            ['name' => 'Sekolah Delete', 'group' => 'Sekolah'],

            // ========== TAHUN AJARAN ==========
            ['name' => 'Tahun Ajaran View', 'group' => 'Tahun Ajaran'],
            ['name' => 'Tahun Ajaran Create', 'group' => 'Tahun Ajaran'],
            ['name' => 'Tahun Ajaran Edit', 'group' => 'Tahun Ajaran'],
            ['name' => 'Tahun Ajaran Delete', 'group' => 'Tahun Ajaran'],

            // ========== KELAS ==========
            ['name' => 'Kelas View', 'group' => 'Kelas'],
            ['name' => 'Kelas Create', 'group' => 'Kelas'],
            ['name' => 'Kelas Edit', 'group' => 'Kelas'],
            ['name' => 'Kelas Delete', 'group' => 'Kelas'],

            // ========== SISWA ==========
            ['name' => 'Siswa View', 'group' => 'Siswa'],
            ['name' => 'Siswa Create', 'group' => 'Siswa'],
            ['name' => 'Siswa Edit', 'group' => 'Siswa'],
            ['name' => 'Siswa Delete', 'group' => 'Siswa'],

            // ========== DATA KELAS ==========
            ['name' => 'Data Kelas View', 'group' => 'Data Kelas'],

            // ========== ORANG TUA ==========
            ['name' => 'Orang Tua View', 'group' => 'Orang Tua'],
            ['name' => 'Orang Tua Create', 'group' => 'Orang Tua'],
            ['name' => 'Orang Tua Edit', 'group' => 'Orang Tua'],
            ['name' => 'Orang Tua Delete', 'group' => 'Orang Tua'],

            // ========== QURAN ==========
            ['name' => 'Quran Reading', 'group' => 'Quran'],

            // ========== HALAQOH ==========
            ['name' => 'Data Halaqoh View', 'group' => 'Data Halaqoh'],
            ['name' => 'Data Halaqoh Create', 'group' => 'Data Halaqoh'],
            ['name' => 'Data Halaqoh Edit', 'group' => 'Data Halaqoh'],
            ['name' => 'Data Halaqoh Delete', 'group' => 'Data Halaqoh'],
        ];

        // 2. Lakukan Update atau Create
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                [
                    'name' => $permission['name'],
                ],
                [
                    'guard_name' => 'web',
                    'group' => $permission['group']
                ]
            );
        }

        // 3. Hapus permission yang tidak ada di dalam daftar $permissions di atas
        $validNames = collect($permissions)->pluck('name')->toArray();

        Permission::whereNotIn('name', $validNames)->delete();
    }
}
