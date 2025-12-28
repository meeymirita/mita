<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class AccountUserPolicy
{
    /**
     * @param User $currentUser
     * @param User $userToUpdate
     * @return bool
     */
    public function update(User $currentUser, User $userToUpdate): bool
    {
        return $currentUser->id === $userToUpdate->id;
    }
}
