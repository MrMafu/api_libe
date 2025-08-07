<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookCopy extends Model
{
    protected $fillable = [
        'book_id',
        'copy_code',
        'condition',
        'status',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
