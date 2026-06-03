<?php

namespace Database\Seeders;

use App\Models\CareerCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CareerCategorySeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::query()
            ->where('role', 'admin')
            ->value('id');

        $categories = [
            'Engineering',
            'Design',
            'Infrastructure',
            'Marketing',
            'Operations',
            'Product',
            'Quality Assurance',
            'Customer Support',
            'Finance',
            'Human Resources',
        ];

        foreach ($categories as $name) {
            CareerCategory::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'created_by' => $adminId,
                ]
            );
        }
    }
}
