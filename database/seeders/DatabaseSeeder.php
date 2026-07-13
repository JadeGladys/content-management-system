<?php

namespace Database\Seeders;

use App\Models\AuditLog; 
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        AuditLog::withoutRecording(function () { 
            $this->call([
                BootstrapAdminSeeder::class,
                ArticleCategorySeeder::class,
                CareerCategorySeeder::class,
                TagSeeder::class,
            ]);
        });
    }
}
