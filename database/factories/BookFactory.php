<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'author' => fake()->name(),
            'category_id' => Category::factory(),
            'published_year' => fake()->numberBetween(1950, 2024),
            'stock_count' => fake()->numberBetween(1, 5),
            'isbn' => fake()->unique()->isbn13(),
            'description' => fake()->paragraph(),
        ];
    }
}
