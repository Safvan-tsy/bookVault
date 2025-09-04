<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BorrowRecord;
use App\Models\Category;
use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowExtendTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_extend_due_date()
    {
        // Create test data
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $member = User::factory()->create(['role' => UserRole::MEMBER]);
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);
        
        $borrowRecord = BorrowRecord::create([
            'user_id' => $member->id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(14),
        ]);

        $originalDueDate = $borrowRecord->due_date;

        // Act as admin and extend the due date
        $response = $this->actingAs($admin)
            ->patch("/borrows/{$borrowRecord->id}/extend", [
                'days' => '7' // String value to test type casting
            ]);

        // Assert the request was successful
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Due date extended by 7 days.');

        // Assert the due date was actually extended
        $borrowRecord->refresh();
        $this->assertEquals(
            $originalDueDate->addDays(7)->format('Y-m-d'),
            $borrowRecord->due_date->format('Y-m-d')
        );
    }

    public function test_member_cannot_extend_due_date()
    {
        // Create test data
        $member = User::factory()->create(['role' => UserRole::MEMBER]);
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);
        
        $borrowRecord = BorrowRecord::create([
            'user_id' => $member->id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(14),
        ]);

        // Act as member and try to extend the due date
        $response = $this->actingAs($member)
            ->patch("/borrows/{$borrowRecord->id}/extend", [
                'days' => 7
            ]);

        // Assert access is forbidden
        $response->assertStatus(403);
    }

    public function test_cannot_extend_returned_book()
    {
        // Create test data
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $member = User::factory()->create(['role' => UserRole::MEMBER]);
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);
        
        $borrowRecord = BorrowRecord::create([
            'user_id' => $member->id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(14),
            'returned_at' => now(), // Already returned
        ]);

        // Act as admin and try to extend the due date
        $response = $this->actingAs($admin)
            ->patch("/borrows/{$borrowRecord->id}/extend", [
                'days' => 7
            ]);

        // Assert the request was redirected with error
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Cannot extend due date for returned book.');
    }

    public function test_extend_validates_days_parameter()
    {
        // Create test data
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $member = User::factory()->create(['role' => UserRole::MEMBER]);
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);
        
        $borrowRecord = BorrowRecord::create([
            'user_id' => $member->id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(14),
        ]);

        // Test with invalid days value
        $response = $this->actingAs($admin)
            ->patch("/borrows/{$borrowRecord->id}/extend", [
                'days' => 0 // Invalid: less than minimum
            ]);

        $response->assertSessionHasErrors('days');

        // Test with days exceeding maximum
        $response = $this->actingAs($admin)
            ->patch("/borrows/{$borrowRecord->id}/extend", [
                'days' => 31 // Invalid: exceeds maximum of 30
            ]);

        $response->assertSessionHasErrors('days');
    }

    public function test_member_can_borrow_book_with_custom_days()
    {
        // Create test data
        $member = User::factory()->create(['role' => UserRole::MEMBER]);
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id, 'stock_count' => 1]);

        // Member borrows book for 10 days
        $response = $this->actingAs($member)
            ->post("/books/{$book->id}/borrow", [
                'days' => 10
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify borrow record was created with correct due date
        $borrowRecord = BorrowRecord::where('user_id', $member->id)
            ->where('book_id', $book->id)
            ->first();

        $this->assertNotNull($borrowRecord);
        $this->assertEquals(
            now()->addDays(10)->format('Y-m-d'),
            $borrowRecord->due_date->format('Y-m-d')
        );
    }

    public function test_borrow_validates_days_parameter()
    {
        // Create test data
        $member = User::factory()->create(['role' => UserRole::MEMBER]);
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id, 'stock_count' => 1]);

        // Test with invalid days value (too low)
        $response = $this->actingAs($member)
            ->post("/books/{$book->id}/borrow", [
                'days' => 0
            ]);

        $response->assertSessionHasErrors('days');

        // Test with invalid days value (too high)
        $response = $this->actingAs($member)
            ->post("/books/{$book->id}/borrow", [
                'days' => 15
            ]);

        $response->assertSessionHasErrors('days');

        // Test with valid days value
        $response = $this->actingAs($member)
            ->post("/books/{$book->id}/borrow", [
                'days' => 7
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }
}
