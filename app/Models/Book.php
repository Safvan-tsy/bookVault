<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'category_id',
        'published_year',
        'stock_count',
        'isbn',
        'description',
    ];

    protected $casts = [
        'published_year' => 'integer',
        'stock_count' => 'integer',
    ];

    /**
     * Get the category that owns the book
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get borrow records for this book
     */
    public function borrowRecords(): HasMany
    {
        return $this->hasMany(BorrowRecord::class);
    }

    /**
     * Get active borrow records (not returned)
     */
    public function activeBorrows(): HasMany
    {
        return $this->hasMany(BorrowRecord::class)->whereNull('returned_at');
    }

    /**
     * Check if book is available for borrowing
     */
    public function isAvailable(): bool
    {
        return $this->stock_count > 0;
    }

    /**
     * Get available stock (total - currently borrowed)
     */
    public function getAvailableStockAttribute(): int
    {
        $borrowedCount = $this->activeBorrows()->count();
        return max(0, $this->stock_count - $borrowedCount);
    }

    /**
     * Scope for available books (has stock and available copies)
     */
    public function scopeAvailable($query)
    {
        return $query->whereRaw('stock_count > (
            SELECT COUNT(*) 
            FROM borrow_records 
            WHERE book_id = books.id 
            AND returned_at IS NULL
        )');
    }

    /**
     * Scope for unavailable books (no available copies)
     */
    public function scopeUnavailable($query)
    {
        return $query->whereRaw('stock_count <= (
            SELECT COUNT(*) 
            FROM borrow_records 
            WHERE book_id = books.id 
            AND returned_at IS NULL
        )');
    }

    /**
     * Scope for searching books
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }
        return $query;
    }
}
