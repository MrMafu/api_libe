<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\BookResource;


class BorrowingDetailResource extends JsonResource
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
            "book" => new BookResource($this->whenLoaded("book")),
            "book_copy" => new BookCopyResource($this->whenLoaded("book_copy")),
            "returned_condition" => $this->returned_condition,
        ];
    }
}
