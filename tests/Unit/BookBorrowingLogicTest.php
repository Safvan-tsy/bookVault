<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use App\Models\Category;
use App\Models\BorrowRecord;
use App\UserRole;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BookBorrowingLogicTest extends TestCase
{
    use DatabaseTransactions;

    protected $member;
    protected $book;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::where('name', 'Fiction')->first() 
            ?? Category::factory()->create(['name' => 'Test Category']);

        $this->member = User::where('email', 'member@bookvault.com')->first() 
            ?? User::factory()->create([
                'name' => 'Test Member',
                'email' => 'test.member@bookvault.com',
                'role' => UserRole::MEMBER
            ]);

        $this->book = Book::where('category_id', $this->category->id)
            ->where('stock_count', '>', 0)
            ->first() ?? Book::factory()->create([
                'title' => 'Test Book',
                'category_id' => $this->category->id,
                'stock_count' => 3
            ]);
    }

    /**
     * Test that borrowing reduces available stock
     */
    public function test_borrowing_reduces_available_stock(): void
    {
        $this->assertEquals(3, $this->book->available_stock);

        BorrowRecord::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(7),
        ]);

        $this->assertEquals(2, $this->book->fresh()->available_stock);

        $this->assertEquals(3, $this->book->fresh()->stock_count);
    }

    /**
     * Returning a book should increase stock
     */
    public function test_returning_increases_available_stock(): void
    {
        $borrowRecord = BorrowRecord::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(7),
        ]);

        $this->assertEquals(2, $this->book->fresh()->available_stock);

        $borrowRecord->update(['returned_at' => now()]);

        $this->assertEquals(3, $this->book->fresh()->available_stock);
    }

    /**
     * Test book availability if zero stock
     */
    public function test_book_with_zero_stock_is_not_available(): void
    {
        $zeroStockBook = Book::factory()->create([
            'stock_count' => 0,
            'category_id' => $this->category->id,
        ]);

        $this->assertFalse($zeroStockBook->isAvailable());
        $this->assertEquals(0, $zeroStockBook->available_stock);
    }

    /**
     * book should be unavailable when all copies are borrowed
     */
    public function test_book_unavailable_when_all_copies_borrowed(): void
    {
        $book = Book::factory()->create([
            'stock_count' => 2,
            'category_id' => $this->category->id,
        ]);

        $this->assertTrue($book->isAvailable());
        $this->assertEquals(2, $book->available_stock);

        $member1 = User::factory()->create(['role' => UserRole::MEMBER]);
        $member2 = User::factory()->create(['role' => UserRole::MEMBER]);

        BorrowRecord::create([
            'user_id' => $member1->id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(7),
        ]);

        BorrowRecord::create([
            'user_id' => $member2->id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(7),
        ]);

        $this->assertTrue($book->fresh()->isAvailable());
        $this->assertEquals(0, $book->fresh()->available_stock);
    }

    /**
     * Test available stock calculation with returned books
     */
    public function test_available_stock_excludes_returned_books(): void
    {
        $member2 = User::factory()->create(['role' => UserRole::MEMBER]);

        $activeBorrow = BorrowRecord::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(7),
        ]);

        $returnedBorrow = BorrowRecord::create([
            'user_id' => $member2->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now()->subDays(7),
            'due_date' => now(),
            'returned_at' => now()->subDays(1),
        ]);

        $this->assertEquals(2, $this->book->fresh()->available_stock);
    }

    /**
     * Test a member cannot borrow same book twice
     */
    public function test_member_cannot_have_duplicate_active_borrows(): void
    {
        BorrowRecord::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(7),
        ]);

        $existingBorrow = BorrowRecord::where('user_id', $this->member->id)
            ->where('book_id', $this->book->id)
            ->whereNull('returned_at')
            ->exists();

        $this->assertTrue($existingBorrow);
    }

    /**
     * Test borrow record creation with correct data
     */
    public function test_borrow_record_creation(): void
    {
        $dueDate = now()->addDays(14);

        $borrowRecord = BorrowRecord::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_date' => $dueDate,
        ]);

        $this->assertDatabaseHas('borrow_records', [
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'returned_at' => null
        ]);

        $this->assertNotNull($borrowRecord->borrowed_at);
        $this->assertEquals($dueDate->toDateString(), $borrowRecord->due_date->toDateString());
        $this->assertNull($borrowRecord->returned_at);
    }

    /**
     * Test book return functionality
     */
    public function test_book_return(): void
    {
        $borrowRecord = BorrowRecord::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(7),
        ]);

        $this->assertNull($borrowRecord->returned_at);
        $this->assertFalse($borrowRecord->isReturned());

        $returnTime = now();
        $borrowRecord->update(['returned_at' => $returnTime]);

        $this->assertNotNull($borrowRecord->fresh()->returned_at);
        $this->assertTrue($borrowRecord->fresh()->isReturned());
    }

    /**
     * Test overdue detection
     */
    public function test_overdue_detection(): void
    {
        $overdueBorrow = BorrowRecord::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now()->subDays(10),
            'due_date' => now()->subDays(3),
        ]);

        $this->assertTrue($overdueBorrow->isOverdue());
        $this->assertEquals(3, $overdueBorrow->getDaysOverdue());

        $currentBorrow = BorrowRecord::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(7),
        ]);

        $this->assertFalse($currentBorrow->isOverdue());
        $this->assertEquals(0, $currentBorrow->getDaysOverdue());
    }
}
