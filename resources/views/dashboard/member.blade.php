<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Dashboard
        </h2>
    </x-slot>

    @section('content')
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex items-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $stats['active_borrows'] }}</div>
                                <div class="ml-4">
                                    <div class="text-sm text-gray-600">Currently Borrowed</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex items-center">
                                <div class="text-2xl font-bold text-green-600">{{ $stats['total_borrowed'] }}</div>
                                <div class="ml-4">
                                    <div class="text-sm text-gray-600">Total Books Borrowed</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex items-center">
                                <div
                                    class="text-2xl font-bold {{ $stats['overdue_books'] > 0 ? 'text-red-600' : 'text-gray-600' }}">
                                    {{ $stats['overdue_books'] }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm text-gray-600">Overdue Books</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($stats['overdue_books'] > 0)
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-8">
                        <div class="flex">
                            <div class="py-1">
                                <strong class="font-bold">⚠️ Overdue Books!</strong>
                                <span class="block sm:inline">You have {{ $stats['overdue_books'] }} overdue book(s). Please
                                    return them as soon as possible.</span>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold mb-4">Currently Borrowed Books</h3>
                            @if($currentBorrows->count() > 0)
                                <div class="space-y-4">
                                    @foreach($currentBorrows as $borrow)
                                        <div
                                            class="p-4 border rounded-lg {{ $borrow->isOverdue() ? 'border-red-300 bg-red-50' : 'border-gray-200' }}">
                                            <div class="font-semibold">{{ $borrow->book->title }}</div>
                                            <div class="text-sm text-gray-600">by {{ $borrow->book->author }}</div>
                                            <div class="text-sm text-gray-500">Category: {{ $borrow->book->category->name }}</div>
                                            <div
                                                class="text-sm {{ $borrow->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                                Due: {{ $borrow->due_date->format('M d, Y') }}
                                                @if($borrow->isOverdue())
                                                    ({{ $borrow->getDaysOverdue() }} days overdue)
                                                @endif
                                            </div>
                                            <div class="mt-2">
                                                <form action="{{ route('borrows.return', $borrow) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="bg-green-500 hover:bg-green-700 text-white text-xs font-bold py-1 px-3 rounded">
                                                        Return Book
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">You haven't borrowed any books yet.</p>
                                <a href="{{ route('books.index') }}" class="text-blue-600 hover:text-blue-800 underline">Browse
                                    our collection</a>
                            @endif
                        </div>
                    </div>

                    @if($recentHistory->count() > 0)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-lg font-semibold mb-4">Recent Borrowing History</h3>
                                <div class="space-y-3">
                                    @foreach($recentHistory as $record)
                                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                            <div>
                                                <div class="font-semibold">{{ $record->book->title }}</div>
                                                <div class="text-sm text-gray-600">
                                                    Borrowed: {{ $record->borrowed_at->format('M d, Y') }}
                                                    @if($record->returned_at)
                                                        • Returned: {{ $record->returned_at->format('M d, Y') }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-sm">
                                                @if($record->returned_at)
                                                    <span class="text-green-600">✓ Returned</span>
                                                @else
                                                    <span class="text-blue-600">📖 Active</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                </div>


            </div>
        </div>
    @endsection
</x-app-layout>