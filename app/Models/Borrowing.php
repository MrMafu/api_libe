<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    protected $fillable = [
       'user_id',
       'due',
       'borrowed_at',
       'returned_at',
       'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function borrowingDetails()
    {
        return $this->hasMany(BorrowingDetail::class);
    }
}
