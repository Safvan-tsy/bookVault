@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Edit Book</h1>
            <div class="flex space-x-3">
                <a href="{{ route('books.show', $book) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    View Book
                </a>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('books.update', $book) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                        <input type="text" id="title" name="title" value="{{ old('title', $book->title) }}" required
                               class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('title') ? 'border-red-500' : 'border-gray-300' }}">
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="author" class="block text-sm font-medium text-gray-700 mb-2">Author *</label>
                        <input type="text" id="author" name="author" value="{{ old('author', $book->author) }}" required
                               class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('author') ? 'border-red-500' : 'border-gray-300' }}">
                        @error('author')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <select id="category_id" name="category_id" required
                                class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('category_id') ? 'border-red-500' : 'border-gray-300' }}">
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="isbn" class="block text-sm font-medium text-gray-700 mb-2">ISBN</label>
                        <input type="text" id="isbn" name="isbn" value="{{ old('isbn', $book->isbn) }}"
                               class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('isbn') ? 'border-red-500' : 'border-gray-300' }}">
                        @error('isbn')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="published_year" class="block text-sm font-medium text-gray-700 mb-2">Published Year *</label>
                        <input type="number" id="published_year" name="published_year" required value="{{ old('published_year', $book->published_year) }}" min="1901" max="{{ date('Y') }}" placeholder="e.g., {{ date('Y') }}"
                               class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('published_year') ? 'border-red-500' : 'border-gray-300' }}">
                        <!-- <p class="text-xs text-gray-500 mt-1">Year must be between 1901 and {{ date('Y') }}</p> -->
                        @error('published_year')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stock_count" class="block text-sm font-medium text-gray-700 mb-2">Stock Count *</label>
                        <input type="number" id="stock_count" name="stock_count" value="{{ old('stock_count', $book->stock_count) }}" min="1" required
                               class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('stock_count') ? 'border-red-500' : 'border-gray-300' }}">
                        @error('stock_count')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">
                            Currently borrowed: {{ $book->activeBorrows->count() }} copies
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('description') ? 'border-red-500' : 'border-gray-300' }}">{{ old('description', $book->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="history.back()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Update Book
                    </button>
                </div>
            </form>
        </div>

        @if($book->activeBorrows->count() > 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            Warning: Book Currently Borrowed
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>This book has {{ $book->activeBorrows->count() }} active borrow(s). Be careful when reducing the stock count below the number of currently borrowed copies.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
