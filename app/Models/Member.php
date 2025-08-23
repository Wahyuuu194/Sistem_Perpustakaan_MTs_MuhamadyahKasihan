<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'birth_date',
        'member_id',
        'registration_date',
        'status',
        'kelas'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'registration_date' => 'date'
    ];

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    public function getActiveBorrowingsAttribute()
    {
        return $this->borrowings()->where('status', 'borrowed')->get();
    }
}
