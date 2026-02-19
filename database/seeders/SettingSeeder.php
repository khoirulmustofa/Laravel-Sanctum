<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ramsey\Collection\Set;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrInsert([
            'key' => 'site_name',
        ], [
            'value' => 'My Laravel App',
            'type' => 'string'
        ]);
        Setting::updateOrInsert([
            'key' => 'site_description',
        ], [
            'value' => 'A Laravel application with Sanctum and Spatie Permissions',
            'type' => 'string'
        ]);
        Setting::updateOrInsert([
            'key' => 'site_logo',
        ], [
            'value' => '',
            'type' => 'file'
        ]);
    }
}
