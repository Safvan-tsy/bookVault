@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Edit Member</h1>
            <div class="flex space-x-3">
                <a href="{{ route('members.show', $member) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    View Member
                </a>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('members.update', $member) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $member->name) }}" required
                               class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }}">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $member->email) }}" required
                               class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }}">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                        <select id="role" name="role" required {{ $member->id === auth()->id() ? 'disabled' : '' }}
                                class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('role') ? 'border-red-500' : 'border-gray-300' }} {{ $member->id === auth()->id() ? 'bg-gray-100' : '' }}">
                            <option value="member" {{ old('role', $member->role->value) === 'member' ? 'selected' : '' }}>Member</option>
                            <option value="admin" {{ old('role', $member->role->value) === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @if($member->id === auth()->id())
                            <input type="hidden" name="role" value="{{ $member->role->value }}">
                            <p class="text-xs text-gray-500 mt-1">You cannot change your own role.</p>
                        @else
                            <p class="text-xs text-gray-500 mt-1">
                                <strong>Member:</strong> Can browse and borrow books<br>
                                <strong>Admin:</strong> Full access to manage books, categories, members, and borrows
                            </p>
                        @endif
                        @error('role')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2 border-t pt-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Change Password (Optional)</h3>
                        <p class="text-sm text-gray-600 mb-4">Leave blank to keep current password</p>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <input type="password" id="password" name="password"
                               class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }}">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="history.back()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Update Member
                    </button>
                </div>
            </form>
        </div>

        <!-- Member Info -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mt-6">
            <h3 class="text-sm font-medium text-gray-900 mb-2">Member Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Member since:</span>
                    <span class="text-gray-900">{{ $member->created_at->format('F d, Y') }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Active borrows:</span>
                    <span class="text-gray-900">{{ $member->activeBorrows->count() }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Total borrows:</span>
                    <span class="text-gray-900">{{ $member->borrowRecords->count() }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Email verified:</span>
                    <span class="text-gray-900">
                        @if($member->email_verified_at)
                            <span class="text-green-600">✓ Yes</span>
                        @else
                            <span class="text-red-600">✗ No</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
