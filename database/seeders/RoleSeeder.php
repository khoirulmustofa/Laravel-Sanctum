<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'guard_name' => 'web'],
            ['name' => 'Admin', 'guard_name' => 'web'],
            ['name' => 'User', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                [
                    'name' => $role['name'],
                    'guard_name' => $role['guard_name'],
                ],
                ['guard_name' => 'web']
            );
        }

        // delete role yang tidak ada di $roles
        Role::whereNotIn('name', collect($roles)->pluck('name'))->delete();

        // assign role super admin ke user super admin
        $superAdmin = User::where('email', 'khoirulm@smpit-nfbogor.sch.id')->first();
        if ($superAdmin) {
            $superAdmin->assignRole('Super Admin');
        }

        Role::where('name', 'Super Admin')->first()->givePermissionTo(Permission::all());
    }
}
