<?php

namespace App\Http\Resources\Article;

use App\Http\Resources\User\UserResource;
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
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'likes_count' => $this->likes_count,
            'liked_by' => $this->whenLoaded(
                relationship: 'likes',
                value: fn() => $this->likes->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'login' => $user->login,
                        'liked_at' => $user->pivot->created_at?->format('Y-m-d H:i:s')
                    ];
                }),
                default: []
            ),
            'author' => $this->whenLoaded(
                relationship: 'user',
                value:  function () {
                    return [
                        'id' => $this->user->id,
                        'login' => $this->user->login,
                        'type' => $this->user->type,
//                        'avatar' => $this->user->getUserAvatar($this->user->avatar->path)
                    ];
                },
                default: []
            ),

        ];
    }
}
