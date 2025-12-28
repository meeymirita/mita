<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResponseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource($this->resource['user']),
            'token' => $this->resource['token'],
            'token_type' => $this->resource['token_type'],
            'expires_at' => $this->resource['expires_at'],
        ];
    }

}
