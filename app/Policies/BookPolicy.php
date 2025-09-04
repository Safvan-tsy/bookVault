<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;
use App\UserRole;

class BookPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view books
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Book $book): bool
    {
        return true; // All authenticated users can view individual books
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Book $book): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Book $book): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can borrow the book.
     */
    public function borrow(User $user, Book $book): bool
    {
        return $user->role === UserRole::MEMBER && $book->isAvailable();
    }

    /**
     * Determine whether the user can return the book.
     */
    public function return(User $user, Book $book): bool
    {
        return $user->role === UserRole::MEMBER || $user->role === UserRole::ADMIN;
    }
}
