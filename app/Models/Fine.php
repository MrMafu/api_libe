<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    protected $fillable = [
        'user_id',
        'borowing_id',
        'book_copy_id',
        'amount',
        'issued_at',
        'paid_at',
        'status',
        'reason'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function borrowing()
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function bookCopy()
    {
        return $this->belongsTo(BookCopy::class);
    }
}
