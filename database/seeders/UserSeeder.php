<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Khoirul Mustofa',
            'email' => 'khoirulm@smpit-nfbogor.sch.id',
            'password' => 'K1r0l1m4570f4',
            'email_verified_at' => now(),
        ]);
    }
}
