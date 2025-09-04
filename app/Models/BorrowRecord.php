<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BorrowRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'borrowed_at',
        'due_date',
        'returned_at',
        'notes',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_date' => 'datetime',
        'returned_at' => 'datetime',
    ];

    /**
     * Get the user who borrowed the book
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book that was borrowed
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Check if the book is overdue
     */
    public function isOverdue(): bool
    {
        return $this->returned_at === null && $this->due_date->isPast();
    }

    /**
     * Check if the book is returned
     */
    public function isReturned(): bool
    {
        return $this->returned_at !== null;
    }

    /**
     * Get days overdue
     */
    public function getDaysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        
        return $this->due_date->diffInDays(Carbon::now());
    }

    /**
     * Scope for overdue records
     */
    public function scopeOverdue($query)
    {
        return $query->whereNull('returned_at')
                    ->where('due_date', '<', now());
    }

    /**
     * Scope for active borrows
     */
    public function scopeActive($query)
    {
        return $query->whereNull('returned_at');
    }

    /**
     * Scope for returned borrows
     */
    public function scopeReturned($query)
    {
        return $query->whereNotNull('returned_at');
    }
}
