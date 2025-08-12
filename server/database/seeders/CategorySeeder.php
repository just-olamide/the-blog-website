<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Technology',
            'Travel',
            'Food',
            'Lifestyle',
            'Business',
            'Health',
            'Education',
            'Entertainment'
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
                'slug' => Str::slug($category),
                'description' => "Articles about " . strtolower($category)
            ]);
        }
    }
}
