<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\ImageResource;
use App\Http\Resources\TagResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'post_id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'likes' => $this->likes,
            'views' => $this->views,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'images' => ImageResource::collection($this->whenLoaded('images')),
            'date' => [
                'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
            ]
        ];
    }
}
