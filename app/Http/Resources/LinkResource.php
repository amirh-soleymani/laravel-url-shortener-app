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
            'id' => $this->id,
            'original_url' => $this->original_url,
            'shortener_url' => $this->shortener_url,
            'user' => [
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'count' => $this->count,
        ];
    }
}
