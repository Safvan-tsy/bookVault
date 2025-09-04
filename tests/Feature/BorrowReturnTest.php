<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BorrowRecord;
use App\Models\Category;
use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowReturnTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->category = Category::factory()->create(['name' => 'Test Category']);
        $this->book = Book::factory()->create([
            'category_id' => $this->category->id,
            'stock_count' => 2,
        ]);
        $this->member = User::factory()->create(['role' => UserRole::MEMBER]);
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
    }

    public function test_member_can_borrow_available_book()
    {
        $this->actingAs($this->member);

        $response = $this->post(route('borrows.store', $this->book), [
            'days' => 7
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('borrow_records', [
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'returned_at' => null,
        ]);
    }

    public function test_member_cannot_borrow_same_book_twice()
    {
        $this->actingAs($this->member);

        // First borrow
        $this->post(route('borrows.store', $this->book), [
            'days' => 7
        ]);

        // Second borrow attempt
        $response = $this->post(route('borrows.store', $this->book), [
            'days' => 7
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_member_can_return_borrowed_book()
    {
        $this->actingAs($this->member);

        // Create a borrow record
        $borrowRecord = BorrowRecord::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(14),
        ]);

        $response = $this->patch(route('borrows.return', $borrowRecord));

        $response->assertRedirect();
        $this->assertDatabaseHas('borrow_records', [
            'id' => $borrowRecord->id,
            'returned_at' => now(),
        ]);
    }

    public function test_member_cannot_return_book_they_did_not_borrow()
    {
        $otherMember = User::factory()->create(['role' => UserRole::MEMBER]);
        
        $borrowRecord = BorrowRecord::create([
            'user_id' => $otherMember->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(14),
        ]);

        $this->actingAs($this->member);

        $response = $this->patch(route('borrows.return', $borrowRecord));

        $response->assertStatus(403);
    }

    public function test_admin_can_return_any_book()
    {
        $borrowRecord = BorrowRecord::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(14),
        ]);

        $this->actingAs($this->admin);

        $response = $this->patch(route('borrows.return', $borrowRecord));

        $response->assertRedirect();
        $this->assertDatabaseHas('borrow_records', [
            'id' => $borrowRecord->id,
            'returned_at' => now(),
        ]);
    }

    public function test_borrow_record_detects_overdue_status()
    {
        $borrowRecord = BorrowRecord::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now()->subDays(20),
            'due_date' => now()->subDays(5), // 5 days overdue
        ]);

        $this->assertTrue($borrowRecord->isOverdue());
        $this->assertEquals(5, $borrowRecord->getDaysOverdue());
    }

    public function test_book_availability_calculation()
    {
        // Initially available
        $this->assertTrue($this->book->isAvailable());
        $this->assertEquals(2, $this->book->available_stock);

        // Borrow one copy
        BorrowRecord::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(14),
        ]);

        // Refresh the model
        $this->book->refresh();
        
        $this->assertTrue($this->book->isAvailable());
        $this->assertEquals(1, $this->book->available_stock);
    }

    public function test_guest_cannot_borrow_books()
    {
        $response = $this->post(route('borrows.store', $this->book), [
            'days' => 7
        ]);

        $response->assertRedirect('/login');
    }
}
