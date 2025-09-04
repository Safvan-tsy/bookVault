<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BorrowRecord;
use App\Models\Category;
use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookAvailabilityFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_filter_available_books_only()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $category = Category::factory()->create();
        
        // Create an available book (stock_count = 2, no borrows)
        $availableBook = Book::factory()->create([
            'title' => 'Available Book',
            'category_id' => $category->id,
            'stock_count' => 2
        ]);
        
        // Create an unavailable book (stock_count = 1, 1 active borrow)
        $unavailableBook = Book::factory()->create([
            'title' => 'Unavailable Book',
            'category_id' => $category->id,
            'stock_count' => 1
        ]);
        
        $member = User::factory()->create(['role' => UserRole::MEMBER]);
        BorrowRecord::create([
            'user_id' => $member->id,
            'book_id' => $unavailableBook->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(14),
            'returned_at' => null,
        ]);

        $response = $this->actingAs($user)->get(route('books.index', ['availability' => 'available']));

        $response->assertStatus(200);
        $response->assertSee('Available Book');
        $response->assertDontSee('Unavailable Book');
    }

    public function test_can_filter_unavailable_books_only()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $category = Category::factory()->create();
        
        // Create an available book
        $availableBook = Book::factory()->create([
            'title' => 'Available Book',
            'category_id' => $category->id,
            'stock_count' => 2
        ]);
        
        // Create an unavailable book
        $unavailableBook = Book::factory()->create([
            'title' => 'Unavailable Book',
            'category_id' => $category->id,
            'stock_count' => 1
        ]);
        
        $member = User::factory()->create(['role' => UserRole::MEMBER]);
        BorrowRecord::create([
            'user_id' => $member->id,
            'book_id' => $unavailableBook->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(14),
            'returned_at' => null,
        ]);

        $response = $this->actingAs($user)->get(route('books.index', ['availability' => 'unavailable']));

        $response->assertStatus(200);
        $response->assertSee('Unavailable Book');
        $response->assertDontSee('Available Book');
    }

    public function test_shows_all_books_when_no_availability_filter()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $category = Category::factory()->create();
        
        $availableBook = Book::factory()->create([
            'title' => 'Available Book',
            'category_id' => $category->id,
            'stock_count' => 2
        ]);
        
        $unavailableBook = Book::factory()->create([
            'title' => 'Unavailable Book',
            'category_id' => $category->id,
            'stock_count' => 1
        ]);
        
        $member = User::factory()->create(['role' => UserRole::MEMBER]);
        BorrowRecord::create([
            'user_id' => $member->id,
            'book_id' => $unavailableBook->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(14),
            'returned_at' => null,
        ]);

        $response = $this->actingAs($user)->get(route('books.index'));

        $response->assertStatus(200);
        $response->assertSee('Available Book');
        $response->assertSee('Unavailable Book');
    }
}
