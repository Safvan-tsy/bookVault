@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Add New Member</h1>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('members.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }}">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                               class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }}">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                        <input type="password" id="password" name="password" required
                               class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }}">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div class="md:col-span-2">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                        <select id="role" name="role" required
                                class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('role') ? 'border-red-500' : 'border-gray-300' }}">
                            <option value="">Select a role</option>
                            <option value="member" {{ old('role') === 'member' ? 'selected' : '' }}>Member</option>
                        </select>
                        @error('role')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">
                            <strong>Member:</strong> Can browse and borrow books<br>
                        </p>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="history.back()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Create Member
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
