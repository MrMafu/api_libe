<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\BorrowingDetailResource;

class BorrowingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   public function toArray($request)
{
    return [
        'id' => $this->id,
        'user_name' => $this->user->name,
        'due' => $this->due,
        'borrowed_at' => $this->borrowed_at,
        'returned_at' => $this->returned_at,
        'status' => $this->status,
        'books' => $this->borrowingDetails->map(function ($detail) {
            return [
                'book_title' => $detail->book->title,
                'copy_code' => $detail->bookCopy->copy_code,
                'returned_condition' => $detail->returned_condition,
            ];
        }),
    ];
}

}
