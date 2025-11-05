<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'yearCreated' => $this->year_created,
            'artist' => $this->whenLoaded('artist', function () {
                return ArtistResource::make($this->artist)->resolve();
            }),
        ];
    }
}
