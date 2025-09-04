<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->role === UserRole::ADMIN, 403);

        $query = User::with(['borrowRecords', 'activeBorrows']);

        // Search functionality
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        // Role filter
        if ($request->filled('role') && in_array($request->role, ['admin', 'member'])) {
            $query->where('role', $request->role);
        }

        $members = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('members.index', compact('members'));
    }

    public function create()
    {
        abort_unless(auth()->user()->role === UserRole::ADMIN, 403);

        return view('members.create');
    }


    public function store(Request $request)
    {
        abort_unless(auth()->user()->role === UserRole::ADMIN, 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,member',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('members.index')
            ->with('success', 'Member created successfully.');
    }


    public function show(User $member)
    {
        abort_unless(auth()->user()->role === UserRole::ADMIN, 403);

        $member->load(['borrowRecords.book', 'activeBorrows.book']);
        
        // Get borrowing statistics
        $stats = [
            'total_borrows' => $member->borrowRecords()->count(),
            'active_borrows' => $member->activeBorrows()->count(),
            'overdue_borrows' => $member->borrowRecords()->overdue()->count(),
            'returned_books' => $member->borrowRecords()->returned()->count(),
        ];

        return view('members.show', compact('member', 'stats'));
    }

    public function edit(User $member)
    {
        abort_unless(auth()->user()->role === UserRole::ADMIN, 403);

        return view('members.edit', compact('member'));
    }

    /**
     * Update member
     */
    public function update(Request $request, User $member)
    {
        abort_unless(auth()->user()->role === UserRole::ADMIN, 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $member->id,
            'role' => 'required|in:admin,member',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $member->update($updateData);

        return redirect()->route('members.index')
            ->with('success', 'Member updated successfully.');
    }

    /**
     * Remove member
     */
    public function destroy(User $member)
    {
        abort_unless(auth()->user()->role === UserRole::ADMIN, 403);

        if ($member->id === auth()->id()) {
            return redirect()->route('members.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Check if member has active borrows
        if ($member->activeBorrows()->count() > 0) {
            return redirect()->route('members.index')
                ->with('error', 'Cannot delete member with active book borrows.');
        }

        $member->delete();

        return redirect()->route('members.index')
            ->with('success', 'Member deleted successfully.');
    }

}
