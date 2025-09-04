<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BorrowRecord;
use App\Models\Category;
use App\Models\User;
use App\UserRole;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === UserRole::ADMIN) {
            return $this->adminDashboard();
        } else {
            return $this->memberDashboard();
        }
    }

    /**
     * Admin dashboard with statistics
     */
    private function adminDashboard()
    {
        $stats = [
            'total_books' => Book::count(),
            'total_categories' => Category::count(),
            'total_members' => User::where('role', UserRole::MEMBER)->count(),
            'books_borrowed' => BorrowRecord::active()->count(),
            'books_overdue' => BorrowRecord::overdue()->count(),
            'books_returned_today' => BorrowRecord::whereDate('returned_at', today())->count(),
        ];

        $recentBorrows = BorrowRecord::with(['user', 'book'])
            ->orderBy('borrowed_at', 'desc')
            ->limit(5)
            ->get();

        $popularBooks = Book::withCount(['borrowRecords'])
            ->orderBy('borrow_records_count', 'desc')
            ->limit(5)
            ->get();

        $overdueRecords = BorrowRecord::overdue()
            ->with(['user', 'book'])
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        return view('dashboard.admin', compact(
            'stats', 
            'recentBorrows', 
            'popularBooks', 
            'overdueRecords'
        ));
    }

    /**
     * Member dashboard with borrowing info
     */
    private function memberDashboard()
    {
        $user = auth()->user();

        $stats = [
            'active_borrows' => $user->activeBorrows()->count(),
            'total_borrowed' => $user->borrowRecords()->count(),
            'overdue_books' => $user->borrowRecords()->overdue()->count(),
        ];

        $currentBorrows = $user->activeBorrows()
            ->with('book.category')
            ->orderBy('due_date')
            ->get();

        $recentHistory = $user->borrowRecords()
            ->with('book.category')
            ->orderBy('borrowed_at', 'desc')
            ->limit(10)
            ->get();


        return view('dashboard.member', compact(
            'stats', 
            'currentBorrows', 
            'recentHistory'
        ));
    }
}
