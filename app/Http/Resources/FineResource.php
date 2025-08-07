<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "user" => new UserResource($this->whenLoaded("user")),
            "borrowing" => new BorrowingResource($this->whenLoaded("borrowing")),
            "book_copy" => new BookCopyResource($this->whenLoaded("bookCopy")),
            "amount" => $this->amount,
            "issued_at" => $this->issued_at,
            "paid_at" => $this->paid_at,
            "status" => $this->status,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
