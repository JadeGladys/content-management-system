<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::query()
            ->where('role', 'admin')
            ->value('id');

        $tags = [
            'AI',
            'Automation',
            'Business',
            'Business Risk',
            'Cloud',
            'Customer Support',
            'Cybersecurity',
            'Data',
            'Digital Transformation',
            'Engineering',
            'Innovation',
            'Leadership',
            'Operations',
            'Productivity',
            'Retail',
            'SaaS',
            'Strategy',
            'Technology',
            'User Experience',
            'Workflows',
        ];

        foreach ($tags as $name) {
            Tag::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'created_by' => $adminId,
                ]
            );
        }
    }
}
