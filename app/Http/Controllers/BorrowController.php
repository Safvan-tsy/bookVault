<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BorrowRecord;
use App\UserRole;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Notifications\BookBorrowedNotification;

class BorrowController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === UserRole::ADMIN) {
            $borrowRecords = BorrowRecord::with(['user', 'book'])
                ->orderBy('borrowed_at', 'desc')
                ->paginate(20);
        } else {
            $borrowRecords = $user->borrowRecords()
                ->with('book')
                ->orderBy('borrowed_at', 'desc')
                ->paginate(20);
        }

        return view('borrows.index', compact('borrowRecords'));
    }

    /**
     * Borrow a book
     */
    public function store(Request $request, Book $book)
    {
        Gate::authorize('borrow', $book);

        $request->validate([
            'days' => 'required|integer|min:1|max:14'
        ]);

        // Check if user already has this book borrowed
        $existingBorrow = auth()->user()->borrowRecords()
            ->where('book_id', $book->id)
            ->whereNull('returned_at')
            ->first();

        if ($existingBorrow) {
            return redirect()->back()
                ->with('error', 'You have already borrowed this book.');
        }

        // Check if book is available
        if (!$book->isAvailable()) {
            return redirect()->back()
                ->with('error', 'This book is not available for borrowing.');
        }

        $days = (int) $request->days;
        $dueDate = now()->addDays($days);

        // Create borrow record
        BorrowRecord::create([
            'user_id' => auth()->id(),
            'book_id' => $book->id,
            'borrowed_at' => now(),
            'due_date' => $dueDate,
        ]);

        $user = auth()->user();

        $user->notify(new BookBorrowedNotification($book, $dueDate));

        return redirect()->back()
            ->with('success', "Book borrowed successfully for {$days} days. Due date: " . $dueDate->format('M d, Y'));
    }

    /**
     * Return a book
     */
    public function return(BorrowRecord $borrowRecord)
    {
        Gate::authorize('return', $borrowRecord->book);

        // Check if the borrow record belongs to the authenticated user (unless admin)
        if (auth()->user()->role !== UserRole::ADMIN && $borrowRecord->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if already returned
        if ($borrowRecord->returned_at) {
            return redirect()->back()
                ->with('error', 'This book has already been returned.');
        }

        // Mark as returned
        $borrowRecord->update([
            'returned_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Book returned successfully.');
    }

    public function overdue()
    {
        abort_unless(auth()->user()->role === UserRole::ADMIN, 403);

        $overdueRecords = BorrowRecord::overdue()
            ->with(['user', 'book'])
            ->orderBy('due_date')
            ->paginate(20);

        return view('borrows.overdue', compact('overdueRecords'));
    }

    /**
     * Extend due date
     */
    public function extend(Request $request, BorrowRecord $borrowRecord)
    {
        abort_unless(auth()->user()->role === UserRole::ADMIN, 403);

        $request->validate([
            'days' => 'required|integer|min:1|max:30'
        ]);

        if ($borrowRecord->returned_at) {
            return redirect()->back()
                ->with('error', 'Cannot extend due date for returned book.');
        }

        $days = (int) $request->days;

        $borrowRecord->update([
            'due_date' => $borrowRecord->due_date->addDays($days),
        ]);

        return redirect()->back()
            ->with('success', "Due date extended by {$days} days.");
    }
}
