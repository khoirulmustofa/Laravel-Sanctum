<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 51; $i++) {
            $user = new \App\Models\User;
            $user->name = "User $i";
            $user->email = "user{$i}@example.com";
            $user->password = 'password';
            $user->save();
        }
    }
}
