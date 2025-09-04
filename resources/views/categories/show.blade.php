@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ $category->name }}</h1>
            @if($category->description)
                <p class="text-gray-600 mt-1">{{ $category->description }}</p>
            @endif
        </div>
        <div class="flex space-x-3">
            @if(auth()->user()->role === \App\UserRole::ADMIN)
                <a href="{{ route('categories.edit', $category) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Edit Category
                </a>
            @endif
            <a href="{{ route('categories.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                Back to Categories
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Books in this Category ({{ $books->total() }})</h2>
        
        @if($books->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($books as $book)
                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                        <h3 class="font-semibold text-lg mb-2">{{ $book->title }}</h3>
                        <p class="text-gray-600 mb-2">by {{ $book->author }}</p>
                        @if($book->published_year)
                            <p class="text-sm text-gray-500 mb-2">Published: {{ $book->published_year }}</p>
                        @endif
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm {{ $book->available_stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $book->available_stock > 0 ? 'Available' : 'Not Available' }}
                                ({{ $book->available_stock }}/{{ $book->stock_count }})
                            </span>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('books.show', $book) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                View Details
                            </a>
                            @if(auth()->user()->role === \App\UserRole::ADMIN)
                                <a href="{{ route('books.edit', $book) }}" class="text-green-600 hover:text-green-900 text-sm">
                                    Edit
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $books->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500 mb-4">No books in this category yet.</p>
                @if(auth()->user()->role === \App\UserRole::ADMIN)
                    <a href="{{ route('books.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Add First Book
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
