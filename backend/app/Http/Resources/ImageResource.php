<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
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
            'filename' => $this->filename,
            'view_url' =>
                url("/api/posts/images/{$this->id}/view")
                ?? url("/api/storage/posts/images/{$this->id}/view"),
            'download_url' =>
                url("/api/posts/images/{$this->id}/download")
                ?? url("/api/storage/posts/images/{$this->id}/view")
            ,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
        ];
    }
}
