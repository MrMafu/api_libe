<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"               => $this->id,
            "sub_topic"        => new SubTopicResource($this->whenLoaded("subTopic")),
            "isbn"             => $this->isbn,
            "cover"            => $this->cover,
            "cover_url"        => $this->cover ? url('storage/' . $this->cover) : null,
            "title"            => $this->title,
            "num_of_pages"     => $this->num_of_pages,
            "language"         => $this->language,
            "author"           => $this->author,
            "publisher"        => $this->publisher,
            "publication_date" => $this->publication_date,
            "copies"           => BookCopyResource::collection($this->whenLoaded("copies")),
            "created_at"       => $this->created_at,
            "updated_at"       => $this->updated_at,
        ];
    }
}
