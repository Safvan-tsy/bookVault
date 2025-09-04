@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-red-600">Overdue Books</h1>
        <a href="{{ route('borrows.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            Back to All Records
        </a>
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

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-red-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Overdue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($overdueRecords as $record)
                    <tr class="bg-red-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $record->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $record->user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $record->book->title }}</div>
                            <div class="text-sm text-gray-500">by {{ $record->book->author }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $record->due_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-red-600 font-bold text-sm">{{ $record->getDaysOverdue() }} days</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <form action="{{ route('borrows.return', $record) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900"
                                            onclick="return confirm('Mark this book as returned?')">
                                        Return
                                    </button>
                                </form>
                                
                                <button onclick="showExtendModal({{ $record->id }})" 
                                        class="text-blue-600 hover:text-blue-900">
                                    Extend
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            <div class="text-green-600">
                                🎉 No overdue books! All books are returned or within due date.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $overdueRecords->links() }}
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
