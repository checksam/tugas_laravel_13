<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    /** @use HasFactory */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'description',
        'stock',
        'available',
        'publisher',
        'publication_year',
        'category',
        'price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'publication_year' => 'integer',
        'price' => 'decimal:2',
    ];

    /**
     * Get all borrowing records for this book.
     */
    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    /**
     * Check if book is available for borrowing.
     */
    public function isAvailable(): bool
    {
        return $this->available > 0;
    }

    /**
     * Get count of currently borrowed copies.
     */
    public function getBorrowedCount(): int
    {
        return $this->stock - $this->available;
    }
}
