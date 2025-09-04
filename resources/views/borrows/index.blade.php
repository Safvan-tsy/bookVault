@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">
            @if(auth()->user()->role === \App\UserRole::ADMIN)
                All Borrow Records
            @else
                My Borrowed Books
            @endif
        </h1>
        
        @if(auth()->user()->role === \App\UserRole::ADMIN)
            <a href="{{ route('borrows.overdue') }}" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                View Overdue Books
            </a>
        @endif
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

    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @if(auth()->user()->role === \App\UserRole::ADMIN)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrowed Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($borrowRecords as $record)
                    <tr class="{{ $record->isOverdue() ? 'bg-red-50' : '' }}">
                        @if(auth()->user()->role === \App\UserRole::ADMIN)
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $record->user->name }}
                            </td>
                        @endif
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $record->book->title }}</div>
                            <div class="text-sm text-gray-500">by {{ $record->book->author }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $record->borrowed_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $record->due_date->format('M d, Y') }}
                            @if($record->isOverdue())
                                <span class="text-red-600 font-medium">({{ $record->getDaysOverdue() }} days overdue)</span>
                            @endif
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if(!$record->returned_at)
                                <form action="{{ route('borrows.return', $record) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900 mr-3"
                                            onclick="return confirm('Are you sure you want to return this book?')">
                                        Return
                                    </button>
                                </form>
                                
                                @if(auth()->user()->role === \App\UserRole::ADMIN)
                                    <button onclick="showExtendModal({{ $record->id }})" 
                                            class="text-blue-600 hover:text-blue-900">
                                        Extend
                                    </button>
                                @endif
                            @else
                                <span class="text-gray-400">Returned on {{ $record->returned_at->format('M d, Y') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->role === \App\UserRole::ADMIN ? '6' : '5' }}" class="px-6 py-4 text-center text-gray-500">
                            No borrow records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $borrowRecords->links() }}
    </div>
</div>

@if(auth()->user()->role === \App\UserRole::ADMIN)
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
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
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
@endif
@endsection
