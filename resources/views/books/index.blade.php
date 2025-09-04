@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Books</h1>
        @can('create', App\Models\Book::class)
            <a href="{{ route('books.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Book
            </a>
        @endcan
    </div>

    <form method="GET" action="{{ route('books.index') }}" class="mb-6 flex flex-wrap gap-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search books..." class="border rounded px-3 py-2" />
        <select name="category" class="border rounded px-3 py-2">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select name="availability" class="border rounded px-3 py-2">
            <option value="">All Books</option>
            <option value="available" @selected(request('availability') === 'available')>Available</option>
            <option value="unavailable" @selected(request('availability') === 'unavailable')>Unavailable</option>
        </select>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($books as $book)
            <div class="border rounded p-4 bg-white shadow">
                <h2 class="text-lg font-semibold mb-2">{{ $book->title }}</h2>
                <p class="mb-1">Author: {{ $book->author }}</p>
                <p class="mb-1">Category: {{ $book->category->name ?? '-' }}</p>
                <div class="mb-1 flex items-center">
                    <span class="mr-2">Available:</span>
                    @if($book->available_stock > 0)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $book->available_stock }}/{{ $book->stock_count }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            0/{{ $book->stock_count }}
                        </span>
                    @endif
                </div>
                <a href="{{ route('books.show', $book) }}" class="text-blue-600 hover:underline">View Details</a>
            </div>
        @empty
            <p class="col-span-3 text-gray-500">No books found.</p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $books->withQueryString()->links() }}
    </div>
</div>
@endsection
