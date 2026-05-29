<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UrlResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'origin_url' => $this->origin_url,
            'short_code' => $this->short_code,
            'short_url' => url($this->short_code),
            'views_count' => $this->views_count,
            'expires_at' => $this->expires_at,
        ];
    }
}
