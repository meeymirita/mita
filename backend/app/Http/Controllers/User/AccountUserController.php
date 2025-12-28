<?php

namespace App\Http\Controllers\User;

use App\Http\Resources\Post\UserPostResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\JsonResponse;

class AccountUserController
{

    /**
     * Вернёт пользователя и его посты
     * @return JsonResponse
     */
    public function profile()
    {
        return response()->json([
            'user' => new UserResource(auth()->user()),
            'data' => UserPostResource::collection(auth()->user()->posts()
                ->with(['images', 'tags'])
                ->latest()
                ->paginate(10)
            )
        ]);
    }
}
