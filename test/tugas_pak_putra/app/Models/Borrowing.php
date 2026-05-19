<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Borrowing extends Model
{
    /** @use HasFactory */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'book_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'fine',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'borrow_date' => 'datetime',
        'due_date' => 'datetime',
        'return_date' => 'datetime',
    ];

    /**
     * Get the user who borrowed this book.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book that was borrowed.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Check if borrowing is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->status === 'borrowed' && now() > $this->due_date;
    }

    /**
     * Calculate fine if overdue (Rp 5000 per hari).
     */
    public function calculateFine(): int
    {
        if ($this->status === 'returned') {
            return $this->fine;
        }

        $now = now();

        if ($now->lte($this->due_date)) {
            return 0;
        }

        $daysOverdue = (int) $this->due_date->diffInDays($now);

        return $daysOverdue * 5000;
    }

    /**
     * Mark as returned.
     */
    public function markAsReturned(int $calculatedFine = 0): bool
    {
        $this->update([
            'return_date' => now(),
            'status' => 'returned',
            'fine' => $calculatedFine,
        ]);

        // Update book availability
        $this->book->increment('available');

        return true;
    }
}
