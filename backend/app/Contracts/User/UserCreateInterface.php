<?php

namespace App\Contracts\User;

interface UserCreateInterface
{
    public function createUser(array $userData);
}
