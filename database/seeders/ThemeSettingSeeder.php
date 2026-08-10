<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ThemeSetting;

class ThemeSettingSeeder extends Seeder
{
    public function run(): void
    {
        ThemeSetting::updateOrCreate(
            ['key' => 'theme'],
            ['value' => 'default']
        );
    }
}
