<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowingDetail extends Model
{
    protected $fillable = [
        'borrowing_id',
        'book_id',
        'book_copy_id',
        'returned_condition'
    ];

    public function borrowing()
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function bookCopy()
    {
        return $this->belongsTo(BookCopy::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
