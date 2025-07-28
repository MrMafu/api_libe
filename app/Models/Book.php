<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        "sub_topic_id",
        "isbn",
        "cover",
        "title",
        "num_of_pages",
        "language",
        "author",
        "publisher",
        "publication_date",
    ];

    public function subTopic()
    {
        return $this->belongsTo(SubTopic::class);
    }
}
