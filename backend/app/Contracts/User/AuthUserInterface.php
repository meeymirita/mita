<?php

namespace App\Contracts\User;

interface AuthUserInterface
{
    public function login(array $userData);
}
