<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Book routes
    Route::resource('books', BookController::class);

    // Category routes
    Route::resource('categories', CategoryController::class);

    // Borrow routes
    Route::get('/borrows', [BorrowController::class, 'index'])->name('borrows.index');
    Route::post('/books/{book}/borrow', [BorrowController::class, 'store'])->name('borrows.store');
    Route::patch('/borrows/{borrowRecord}/return', [BorrowController::class, 'return'])->name('borrows.return');
    Route::get('/borrows/overdue', [BorrowController::class, 'overdue'])->name('borrows.overdue');
    Route::patch('/borrows/{borrowRecord}/extend', [BorrowController::class, 'extend'])->name('borrows.extend');

    // Member management routes (Admin only)
    Route::resource('members', MemberController::class);
});

require __DIR__.'/auth.php';
