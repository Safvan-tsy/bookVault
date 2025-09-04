<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Fiction', 'Science', 'Technology', 'History', 'Biography', 
                'Mystery', 'Romance', 'Fantasy', 'Horror', 'Adventure'
            ]),
            'description' => fake()->sentence(10),
        ];
    }
}
