<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'description',
        'publisher',
        'publication_year',
        'quantity',
        'category',
        'kelas',
        'rak',
        'location',
        'cover_image'
    ];

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    public function getAvailableQuantityAttribute(): int
    {
        $borrowed = $this->borrowings()->where('status', 'borrowed')->sum('quantity');
        return $this->quantity - $borrowed;
    }
}
