<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LinkResource extends JsonResource
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
            "original_url" => $this->original_url,
            "slug" => $this->slug,
            "title" => $this->title,
            "expires_at" => $this->expires_at,
            "clicks_limit" => $this->click_limit,
            "clicks_count" => $this->clicks_count
        ];
    }
}
