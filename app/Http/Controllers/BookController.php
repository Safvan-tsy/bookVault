<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Book::class);

        $query = Book::with('category');

        //Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        //Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        //Availability filter
        if ($request->filled('availability')) {
            if ($request->availability === 'available') {
                $query->available();
            } elseif ($request->availability === 'unavailable') {
                $query->unavailable();
            }

        }

        $books = $query->paginate(12);
        $categories = Category::orderBy('name')->get();

        return view('books.index', compact('books', 'categories'));
    }

    public function create()
    {
        Gate::authorize('create', Book::class);

        $categories = Category::orderBy('name')->get();
        return view('books.create', compact('categories'));
    }

    public function store(BookRequest $request)
    {
        Gate::authorize('create', Book::class);

        $book = Book::create($request->validated());

        return redirect()->route('books.show', $book)
            ->with('success', 'Book created successfully.');
    }

    public function show(Book $book)
    {
        Gate::authorize('view', $book);

        $book->load('category', 'borrowRecords.user');

        return view('books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        Gate::authorize('update', $book);

        $categories = Category::orderBy('name')->get();
        return view('books.edit', compact('book', 'categories'));
    }

    public function update(BookRequest $request, Book $book)
    {
        Gate::authorize('update', $book);

        $book->update($request->validated());

        return redirect()->route('books.show', $book)
            ->with('success', 'Book updated successfully.');
    }


    public function destroy(Book $book)
    {
        Gate::authorize('delete', $book);

        // Check if the book has any active borrows (not yet returned)
        $activeBorrows = $book->borrowRecords()->whereNull('returned_at')->count();

        if ($activeBorrows > 0) {
            return redirect()->route('books.show', $book)
                ->with('error', 'Cannot delete this book. It has ' . $activeBorrows . ' active borrow(s) that have not been returned yet.');
        }

        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Book deleted successfully.');
    }
}
