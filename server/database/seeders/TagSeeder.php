<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Web Development',
            'Mobile Apps',
            'AI',
            'Machine Learning',
            'Data Science',
            'Cloud Computing',
            'Cybersecurity',
            'Programming',
            'DevOps',
            'UI/UX',
            'Frontend',
            'Backend',
            'Database',
            'API',
            'Testing'
        ];

        foreach ($tags as $tag) {
            Tag::create([
                'name' => $tag,
                'slug' => Str::slug($tag)
            ]);
        }
    }
}
