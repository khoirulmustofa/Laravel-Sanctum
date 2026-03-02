<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'My Laravel App',
                'type' => 'string',
            ],
            [
                'key' => 'site_description',
                'value' => 'A Laravel application with Sanctum and Spatie Permissions',
                'type' => 'string',
            ],
            [
                'key' => 'site_logo',
                'value' => null,
                'type' => 'file',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrInsert($setting);
        }
    }
}
