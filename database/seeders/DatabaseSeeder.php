<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);

        $user = new User;
        $user->name = 'Admin';
        $user->email = 'admin@admin.com';
        $user->password = 'password';
        $user->email_verified_at = now();
        $user->save();

        $user->assignRole('Admin');

        $this->call(SettingSeeder::class);
    }
}
