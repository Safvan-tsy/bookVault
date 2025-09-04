<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>
    @section('content')
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex items-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $stats['total_books'] }}</div>
                                <div class="ml-4">
                                    <div class="text-sm text-gray-600">Total Books</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex items-center">
                                <div class="text-2xl font-bold text-green-600">{{ $stats['books_borrowed'] }}</div>
                                <div class="ml-4">
                                    <div class="text-sm text-gray-600">Currently Borrowed</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex items-center">
                                <div class="text-2xl font-bold text-red-600">{{ $stats['books_overdue'] }}</div>
                                <div class="ml-4">
                                    <div class="text-sm text-gray-600">Overdue Books</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex items-center">
                                <div class="text-2xl font-bold text-purple-600">{{ $stats['total_members'] }}</div>
                                <div class="ml-4">
                                    <div class="text-sm text-gray-600">Total Members</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Recent Borrows -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold mb-4">Recent Borrows</h3>
                            @if($recentBorrows->count() > 0)
                                <div class="space-y-3">
                                    @foreach($recentBorrows as $borrow)
                                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                            <div>
                                                <div class="font-semibold">{{ $borrow->book->title }}</div>
                                                <div class="text-sm text-gray-600">by {{ $borrow->user->name }}</div>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $borrow->borrowed_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">No recent borrows</p>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white ">
                            <h3 class="text-lg font-semibold mb-4">Overdue Books</h3>
                            @if($overdueRecords->count() > 0)
                                <div class="space-y-3">
                                    @foreach($overdueRecords as $record)
                                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                            <div>
                                                <div class="font-semibold">{{ $record->book->title }}</div>
                                                <div class="text-sm text-gray-600">by {{ $record->user->name }}</div>
                                            </div>
                                            <div class="text-sm text-red-500">
                                                {{ $record->due_date->format('M d, Y') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">No overdue data yet</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
</x-app-layout>