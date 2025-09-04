<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BorrowRecord;
use App\Models\Category;
use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_book_without_active_borrows()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($admin)->delete(route('books.destroy', $book));

        $response->assertRedirect(route('books.index'));
        $response->assertSessionHas('success', 'Book deleted successfully.');
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_admin_cannot_delete_book_with_active_borrows()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $member = User::factory()->create(['role' => UserRole::MEMBER]);
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);

        // Create an active borrow record (not returned)
        BorrowRecord::create([
            'user_id' => $member->id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(14),
            'returned_at' => null,
        ]);

        $response = $this->actingAs($admin)->delete(route('books.destroy', $book));

        $response->assertRedirect(route('books.show', $book));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('books', ['id' => $book->id]);
    }

    public function test_admin_can_delete_book_with_returned_borrows()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $member = User::factory()->create(['role' => UserRole::MEMBER]);
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);

        // Create a returned borrow record
        BorrowRecord::create([
            'user_id' => $member->id,
            'book_id' => $book->id,
            'borrowed_at' => now()->subDays(20),
            'due_date' => now()->subDays(6),
            'returned_at' => now()->subDays(5),
        ]);

        $response = $this->actingAs($admin)->delete(route('books.destroy', $book));

        $response->assertRedirect(route('books.index'));
        $response->assertSessionHas('success', 'Book deleted successfully.');
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }
}
