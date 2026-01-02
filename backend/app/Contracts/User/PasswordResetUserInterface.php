<?php

namespace App\Contracts\User;

interface PasswordResetUserInterface
{

    public function sendResetLink(string $userData);

    public function passwordReset(array $userData);


}
