@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Member Management</h1>
            <a href="{{ route('members.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Add New Member
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

        <div class="bg-white shadow rounded-lg p-4 mb-6">
            <form method="GET" action="{{ route('members.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-64">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by name or email..."
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <select name="role"
                        class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="member" {{ request('role') === 'member' ? 'selected' : '' }}>Member</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Search
                </button>
                <a href="{{ route('members.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    Clear
                </a>
            </form>
        </div>
        <!-- Statistics -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white shadow rounded-lg p-4">
                <div class="flex items-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $members->total() }}</div>
                    <div class="ml-4">
                        <div class="text-sm text-gray-600">Total Users</div>
                    </div>
                </div>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <div class="flex items-center">
                    <div class="text-2xl font-bold text-purple-600">
                        {{ $members->where('role', \App\UserRole::ADMIN)->count() }}
                    </div>
                    <div class="ml-4">
                        <div class="text-sm text-gray-600">Admins</div>
                    </div>
                </div>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <div class="flex items-center">
                    <div class="text-2xl font-bold text-green-600">
                        {{ $members->where('role', \App\UserRole::MEMBER)->count() }}
                    </div>
                    <div class="ml-4">
                        <div class="text-sm text-gray-600">Members</div>
                    </div>
                </div>
            </div>
        </div>


        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active
                            Borrows</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
                            Borrows</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($members as $member)
                        <tr class="{{ $member->id === auth()->id() ? 'bg-blue-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ strtoupper(substr($member->name, 0, 2)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $member->name }}
                                            @if($member->id === auth()->id())
                                                <span class="text-xs text-blue-600">(You)</span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $member->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $member->role->value === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($member->role->value) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $member->active_borrows_count ?? $member->activeBorrows->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $member->borrow_records_count ?? $member->borrowRecords->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $member->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('members.show', $member) }}"
                                        class="text-indigo-600 hover:text-indigo-900">
                                        View
                                    </a>
                                    <a href="{{ route('members.edit', $member) }}" class="text-blue-600 hover:text-blue-900">
                                        Edit
                                    </a>
                                    @if($member->id !== auth()->id())
                                        @if($member->activeBorrows->count() === 0)
                                            <form action="{{ route('members.destroy', $member) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Are you sure you want to delete this member? This action cannot be undone.')">
                                                    Delete
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 cursor-help" title="Cannot delete member with active borrows">Cannot delete</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">Self</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No members found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $members->withQueryString()->links() }}
        </div>


    </div>
@endsection