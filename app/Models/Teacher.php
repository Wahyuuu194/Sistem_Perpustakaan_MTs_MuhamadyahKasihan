<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    protected $fillable = [
        'teacher_id',
        'name',
        'phone',
        'address',
        'birth_date',
        'registration_date',
        'status',
        'subject'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'registration_date' => 'date'
    ];

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class, 'teacher_id');
    }

    public function getActiveBorrowingsAttribute()
    {
        return $this->borrowings()->where('status', 'borrowed')->get();
    }
}
