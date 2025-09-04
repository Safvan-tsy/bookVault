@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ $book->title }}</h1>
                <p class="text-lg text-gray-600">by {{ $book->author }}</p>
            </div>
            <div class="flex space-x-3">
                <button type="button" onclick="history.back()" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    ← Go Back
                </button>
                @if(auth()->user()->role === \App\UserRole::ADMIN)
                    <a href="{{ route('books.edit', $book) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Edit Book
                    </a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Book Details -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Book Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Category</label>
                            <p class="text-gray-900">{{ $book->category->name ?? 'No category' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">ISBN</label>
                            <p class="text-gray-900">{{ $book->isbn ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Published Year</label>
                            <p class="text-gray-900">{{ $book->published_year ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Stock</label>
                            <p class="text-gray-900">{{ $book->available_stock }}/{{ $book->stock_count }} available</p>
                        </div>
                    </div>
                    @if($book->description)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-500">Description</label>
                            <p class="text-gray-900 mt-1">{{ $book->description }}</p>
                        </div>
                    @endif
                </div>

                <!-- Borrow History -->
                @if(auth()->user()->role === \App\UserRole::ADMIN && $book->borrowRecords->count() > 0)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-xl font-semibold mb-4">Borrow History</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Borrowed</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($book->borrowRecords->take(10) as $record)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $record->user->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $record->borrowed_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $record->due_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($record->returned_at)
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                        Returned
                                                    </span>
                                                @elseif($record->isOverdue())
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                        Overdue
                                                    </span>
                                                @else
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Active
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Actions Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Actions</h3>
                    
                    <!-- Availability Status -->
                    <div class="mb-4 p-3 rounded {{ $book->available_stock > 0 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                        <div class="flex items-center">
                            @if($book->available_stock > 0)
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-800">Available for borrowing</p>
                                    <p class="text-xs text-green-600">{{ $book->available_stock }} of {{ $book->stock_count }} copies</p>
                                </div>
                            @else
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-800">Not available</p>
                                    <p class="text-xs text-red-600">All copies are borrowed</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Borrow Button -->
                    @if(auth()->user()->role === \App\UserRole::MEMBER && $book->available_stock > 0)
                        @php
                            $userHasBorrowed = auth()->user()->borrowRecords()
                                ->where('book_id', $book->id)
                                ->whereNull('returned_at')
                                ->exists();
                        @endphp
                        
                        @if(!$userHasBorrowed)
                            <button onclick="showBorrowModal()" class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 mb-3">
                                Borrow This Book
                            </button>
                        @else
                            <p class="text-sm text-gray-600 mb-3">You have already borrowed this book.</p>
                        @endif
                    @endif

                    @if(auth()->user()->role === \App\UserRole::ADMIN)
                        @php
                            $activeBorrows = $book->borrowRecords()->whereNull('returned_at')->count();
                        @endphp
                        
                        <div class="space-y-2">
                            <a href="{{ route('books.edit', $book) }}" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 block text-center">
                                Edit Book
                            </a>
                            
                            @if($activeBorrows > 0)
                                <div class="w-full bg-gray-400 text-white px-4 py-2 rounded text-center cursor-not-allowed opacity-50">
                                    Delete Book
                                </div>
                                <p class="text-xs text-red-600 mt-1">
                                    Cannot delete: {{ $activeBorrows }} active borrow(s)
                                </p>
                            @else
                                <form action="{{ route('books.destroy', $book) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
                                            onclick="return confirm('Are you sure you want to delete this book? This action cannot be undone.')">
                                        Delete Book
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->role === \App\UserRole::MEMBER)
<!-- Borrow Book Modal -->
<div id="borrowModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg p-6 w-96">
            <h3 class="text-lg font-medium mb-4">Borrow Book</h3>
            <form action="{{ route('borrows.store', $book) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="days" class="block text-sm font-medium text-gray-700">Number of days to borrow:</label>
                    <input type="number" id="days" name="days" min="1" max="14" value="7" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">You can borrow this book for 1 to 14 days</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideBorrowModal()" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Borrow Book
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showBorrowModal() {
    document.getElementById('borrowModal').classList.remove('hidden');
}

function hideBorrowModal() {
    document.getElementById('borrowModal').classList.add('hidden');
}
</script>
@endif
@endsection
