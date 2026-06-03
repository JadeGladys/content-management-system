<?php

namespace Database\Seeders;

use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleCategorySeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::query()
            ->where('role', 'admin')
            ->value('id');

        $categories = [
            'Artificial Intelligence',
            'Cybersecurity',
            'Digital Transformation',
            'Technology & Innovation',
            'Leadership',
            'Operations',
            'Product',
            'Engineering',
            'Customer Experience',
            'Data & Analytics',
        ];

        foreach ($categories as $name) {
            ArticleCategory::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'created_by' => $adminId,
                ]
            );
        }
    }
}
