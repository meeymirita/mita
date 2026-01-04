<?php

namespace App\Http\Resources\Article;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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
//            'user_id' => $this->user_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'author' => $this->whenLoaded(
                relationship: 'user',
                value:  function () {
                    return [
                        'id' => $this->user->id,
                        'login' => $this->user->login,
                        'type' => $this->user->type,
                    ];
                },
                default: null
            ),

        ];
    }
}
