<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fictionCategory = Category::where('name', 'Fiction')->first();
        $scienceCategory = Category::where('name', 'Science')->first();
        $technologyCategory = Category::where('name', 'Technology')->first();
        $mysteryCategory = Category::where('name', 'Mystery')->first();
        $fantasyCategory = Category::where('name', 'Fantasy')->first();

        $books = [
            [
                'title' => 'The Great Gatsby',
                'author' => 'F. Scott Fitzgerald',
                'category_id' => $fictionCategory->id,
                'published_year' => 1925,
                'stock_count' => 3,
                'isbn' => '9780743273565',
                'description' => 'A classic American novel set in the Roaring Twenties.'
            ],
            [
                'title' => 'To Kill a Mockingbird',
                'author' => 'Harper Lee',
                'category_id' => $fictionCategory->id,
                'published_year' => 1960,
                'stock_count' => 2,
                'isbn' => '9780061120084',
                'description' => 'A gripping tale of racial injustice and lost innocence.'
            ],
            [
                'title' => 'A Brief History of Time',
                'author' => 'Stephen Hawking',
                'category_id' => $scienceCategory->id,
                'published_year' => 1988,
                'stock_count' => 2,
                'isbn' => '9780553380163',
                'description' => 'A landmark volume on the universe and cosmology.'
            ],
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'category_id' => $technologyCategory->id,
                'published_year' => 2008,
                'stock_count' => 4,
                'isbn' => '9780132350884',
                'description' => 'A handbook of agile software craftsmanship.'
            ],
            [
                'title' => 'The Design of Everyday Things',
                'author' => 'Don Norman',
                'category_id' => $technologyCategory->id,
                'published_year' => 1988,
                'stock_count' => 2,
                'isbn' => '9780465050659',
                'description' => 'Essential reading for anyone interested in design and usability.'
            ],
            [
                'title' => 'The Da Vinci Code',
                'author' => 'Dan Brown',
                'category_id' => $mysteryCategory->id,
                'published_year' => 2003,
                'stock_count' => 3,
                'isbn' => '9780307474278',
                'description' => 'A mystery thriller involving art, history, and secret societies.'
            ],
            [
                'title' => 'The Lord of the Rings',
                'author' => 'J.R.R. Tolkien',
                'category_id' => $fantasyCategory->id,
                'published_year' => 1954,
                'stock_count' => 5,
                'isbn' => '9780544003415',
                'description' => 'An epic fantasy adventure in Middle-earth.'
            ],
            [
                'title' => 'Harry Potter and the Philosophers Stone',
                'author' => 'J.K. Rowling',
                'category_id' => $fantasyCategory->id,
                'published_year' => 1997,
                'stock_count' => 4,
                'isbn' => '9780439708180',
                'description' => 'The first book in the beloved Harry Potter series.'
            ],
        ];

        foreach ($books as $bookData) {
            Book::firstOrCreate(
                ['isbn' => $bookData['isbn']],
                $bookData
            );
        }
    }
}
