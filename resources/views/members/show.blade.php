@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ $member->name }}</h1>
                <div class="flex items-center space-x-4">
                    <p class="text-lg text-gray-600">{{ $member->email }}</p>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $member->role->value === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                        {{ ucfirst($member->role->value) }}
                    </span>
                    @if($member->id === auth()->id())
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            You
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('members.edit', $member) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Edit Member
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white shadow rounded-lg p-4">
                <div class="flex items-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['total_borrows'] }}</div>
                    <div class="ml-4">
                        <div class="text-sm text-gray-600">Total Borrows</div>
                    </div>
                </div>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <div class="flex items-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['active_borrows'] }}</div>
                    <div class="ml-4">
                        <div class="text-sm text-gray-600">Active Borrows</div>
                    </div>
                </div>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <div class="flex items-center">
                    <div class="text-2xl font-bold text-red-600">{{ $stats['overdue_borrows'] }}</div>
                    <div class="ml-4">
                        <div class="text-sm text-gray-600">Overdue</div>
                    </div>
                </div>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <div class="flex items-center">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['returned_books'] }}</div>
                    <div class="ml-4">
                        <div class="text-sm text-gray-600">Returned</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Active Borrows -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Active Borrows</h2>
                @if($member->activeBorrows->count() > 0)
                    <div class="space-y-3">
                        @foreach($member->activeBorrows as $borrow)
                            <div class="border rounded-lg p-4 {{ $borrow->isOverdue() ? 'border-red-300 bg-red-50' : 'border-gray-200' }}">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $borrow->book->title }}</h3>
                                        <p class="text-sm text-gray-600">by {{ $borrow->book->author }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Borrowed: {{ $borrow->borrowed_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium {{ $borrow->isOverdue() ? 'text-red-600' : 'text-gray-900' }}">
                                            Due: {{ $borrow->due_date->format('M d, Y') }}
                                        </p>
                                        @if($borrow->isOverdue())
                                            <p class="text-xs text-red-600 font-medium">
                                                {{ $borrow->getDaysOverdue() }} days overdue
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-3 flex space-x-2">
                                    <form action="{{ route('borrows.return', $borrow) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700"
                                                onclick="return confirm('Mark this book as returned?')">
                                            Return Book
                                        </button>
                                    </form>
                                    @if($borrow->isOverdue())
                                        <button onclick="showExtendModal({{ $borrow->id }})" 
                                                class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                            Extend Due Date
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No active borrows.</p>
                @endif
            </div>

            <!-- Recent Borrow History -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Recent Borrow History</h2>
                @if($member->borrowRecords->count() > 0)
                    <div class="space-y-3">
                        @foreach($member->borrowRecords->take(10) as $borrow)
                            <div class="border rounded-lg p-4 border-gray-200">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $borrow->book->title }}</h3>
                                        <p class="text-sm text-gray-600">by {{ $borrow->book->author }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Borrowed: {{ $borrow->borrowed_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        @if($borrow->returned_at)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Returned
                                            </span>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $borrow->returned_at->format('M d, Y') }}
                                            </p>
                                        @elseif($borrow->isOverdue())
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                Overdue
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Active
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($member->borrowRecords->count() > 10)
                        <div class="mt-4 text-center">
                            <a href="{{ route('borrows.index') }}?user={{ $member->id }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                View All Borrow History
                            </a>
                        </div>
                    @endif
                @else
                    <p class="text-gray-500">No borrow history yet.</p>
                @endif
            </div>
        </div>

        <!-- Member Details -->
        <div class="bg-white shadow rounded-lg p-6 mt-8">
            <h2 class="text-xl font-semibold mb-4">Member Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Member Since</label>
                    <p class="text-gray-900">{{ $member->created_at->format('F d, Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Email Verified</label>
                    <p class="text-gray-900">
                        @if($member->email_verified_at)
                            <span class="text-green-600">✓ Verified</span>
                        @else
                            <span class="text-red-600">✗ Not verified</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                    <p class="text-gray-900">{{ $member->updated_at->format('F d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Extend Due Date Modal -->
<div id="extendModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg p-6 w-96">
            <h3 class="text-lg font-medium mb-4">Extend Due Date</h3>
            <form id="extendForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label for="days" class="block text-sm font-medium text-gray-700">Number of days to extend:</label>
                    <input type="number" id="days" name="days" min="1" max="30" value="7" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideExtendModal()" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Extend
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showExtendModal(recordId) {
    document.getElementById('extendForm').action = `/borrows/${recordId}/extend`;
    document.getElementById('extendModal').classList.remove('hidden');
}

function hideExtendModal() {
    document.getElementById('extendModal').classList.add('hidden');
}
</script>
@endsection
