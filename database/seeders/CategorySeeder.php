<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Fiction',
                'description' => 'Fictional novels and stories'
            ],
            [
                'name' => 'Science',
                'description' => 'Scientific books and research materials'
            ],
            [
                'name' => 'History',
                'description' => 'Historical books and documentaries'
            ],
            [
                'name' => 'Technology',
                'description' => 'Programming, IT, and technology books'
            ],
            [
                'name' => 'Biography',
                'description' => 'Biographical and autobiographical books'
            ],
            [
                'name' => 'Mystery',
                'description' => 'Mystery and thriller novels'
            ],
            [
                'name' => 'Romance',
                'description' => 'Romance novels and love stories'
            ],
            [
                'name' => 'Fantasy',
                'description' => 'Fantasy and magical fiction'
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }
    }
}
