<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use App\Models\Category;
use App\Models\BorrowRecord;
use App\UserRole;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BookBorrowingFeatureTest extends TestCase
{
    use DatabaseTransactions;

    protected $member;
    protected $admin;
    protected $book;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::where('name', 'Fiction')->first() 
            ?? Category::factory()->create(['name' => 'Test Category']);

        $this->member = User::where('email', 'member@bookvault.com')->first() 
            ?? User::factory()->create(['role' => UserRole::MEMBER]);

        $this->admin = User::where('email', 'admin@bookvault.com')->first() 
            ?? User::factory()->create(['role' => UserRole::ADMIN]);

        $this->book = Book::where('category_id', $this->category->id)
            ->where('stock_count', '>', 0)
            ->first() ?? Book::factory()->create([
                'category_id' => $this->category->id,
                'stock_count' => 3
            ]);
    }

    /**
     * Test admin can view overdue borrows page
     */
    public function test_admin_can_view_overdue_borrows(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('borrows.overdue'));

        $response->assertStatus(200);
        $response->assertViewIs('borrows.overdue');
    }

    /**
     * Test member cannot access overdue borrows page
     */
    public function test_member_cannot_view_overdue_borrows(): void
    {
        $this->actingAs($this->member);

        $response = $this->get(route('borrows.overdue'));

        $response->assertStatus(403);
    }

    /**
     * Test borrow index shows members records correctly
     */
    public function test_borrow_index_shows_member_borrows(): void
    {
        BorrowRecord::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(7),
        ]);

        $otherUser = User::factory()->create(['role' => UserRole::MEMBER]);
        BorrowRecord::create([
            'user_id' => $otherUser->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(7),
        ]);

        $this->actingAs($this->member);

        $response = $this->get(route('borrows.index'));

        $response->assertStatus(200);
        $borrowRecords = $response->viewData('borrowRecords');
        
        $this->assertCount(1, $borrowRecords);
        $this->assertEquals($this->member->id, $borrowRecords->first()->user_id);
    }

    /**
     * Test borrow index shows all records for admin
     */
    public function test_borrow_index_shows_all_records_for_admin(): void
    {
        BorrowRecord::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(7),
        ]);

        $otherUser = User::factory()->create(['role' => UserRole::MEMBER]);
        BorrowRecord::create([
            'user_id' => $otherUser->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(7),
        ]);

        $this->actingAs($this->admin);

        $response = $this->get(route('borrows.index'));

        $response->assertStatus(200);
        $borrowRecords = $response->viewData('borrowRecords');
        
        $this->assertCount(2, $borrowRecords);
    }

    /**
     * Test overdue borrows page shows correct records
     */
    public function test_overdue_page_shows_only_overdue_records(): void
    {
        BorrowRecord::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now()->subDays(10),
            'due_date' => now()->subDays(3),
        ]);

        BorrowRecord::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(7),
        ]);

        BorrowRecord::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now()->subDays(14),
            'due_date' => now()->subDays(7),
            'returned_at' => now()->subDays(1),
        ]);

        $this->actingAs($this->admin);

        $response = $this->get(route('borrows.overdue'));

        $response->assertStatus(200);
        $overdueRecords = $response->viewData('overdueRecords');
        
        // Should only show the overdue record
        $this->assertCount(1, $overdueRecords);
        $this->assertTrue($overdueRecords->first()->isOverdue());
    }

    /**
     * Test that book borrowing system integrates properly with database
     */
    public function test_borrowing_return_flow(): void
    {
        
        //1 Create borrow record
        $borrowRecord = BorrowRecord::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(14),
        ]);

        // 2 Verify borrow record exists
        $this->assertDatabaseHas('borrow_records', [
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'returned_at' => null
        ]);

        // 3 Verify book stock is affected
        $this->assertEquals(2, $this->book->fresh()->available_stock);

        //4 Return the book
        $borrowRecord->update(['returned_at' => now()]);

        //5 Verify return is recorded
        $this->assertDatabaseHas('borrow_records', [
            'id' => $borrowRecord->id,
        ]);
        $this->assertNotNull($borrowRecord->fresh()->returned_at);

        //6 Verify stock is restored
        $this->assertEquals(3, $this->book->fresh()->available_stock);
    }
}
