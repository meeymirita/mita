<?php

namespace App\Services\User;


use App\Contracts\User\UpdateUserInterface;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;

class UpdateService implements UpdateUserInterface
{
    /**
     * @param array $userData
     * @return User|JsonResponse|Authenticatable|null
     */
    public function update(array $userData): User|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Auth\Authenticatable|null
    {
        $user = auth()->user();
        if (!$user->can('update', $user)) {
            return response()->json([
                'message' => 'У вас нет прав для обновления профиля'
            ], 403);
        }
        $user->update($userData);

        return $user;
    }
}
