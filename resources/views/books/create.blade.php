@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Add New Book</h1>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('books.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                               class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('title') ? 'border-red-500' : 'border-gray-300' }}">
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="author" class="block text-sm font-medium text-gray-700 mb-2">Author *</label>
                        <input type="text" id="author" name="author" value="{{ old('author') }}" required
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
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                        <input type="text" id="isbn" name="isbn" value="{{ old('isbn') }}"
                               class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('isbn') ? 'border-red-500' : 'border-gray-300' }}">
                        @error('isbn')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="published_year" class="block text-sm font-medium text-gray-700 mb-2">Published Year *</label>
                        <input type="number" id="published_year" required name="published_year" value="{{ old('published_year') }}" min="1901" max="{{ date('Y') }}" placeholder="e.g., {{ date('Y') }}"
                               class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('published_year') ? 'border-red-500' : 'border-gray-300' }}">
                        <!-- <p class="text-xs text-gray-500 mt-1">Year must be between 1901 and {{ date('Y') }}</p> -->
                        @error('published_year')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stock_count" class="block text-sm font-medium text-gray-700 mb-2">Stock Count *</label>
                        <input type="number" id="stock_count" name="stock_count" value="{{ old('stock_count', 1) }}" min="1" required
                               class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('stock_count') ? 'border-red-500' : 'border-gray-300' }}">
                        @error('stock_count')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('description') ? 'border-red-500' : 'border-gray-300' }}">{{ old('description') }}</textarea>
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
                        Create Book
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
