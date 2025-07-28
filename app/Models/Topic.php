<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = ["name"];

    public function subTopics()
    {
        return $this->hasMany(SubTopic::class);
    }
}
