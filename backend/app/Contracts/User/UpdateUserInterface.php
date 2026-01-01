<?php

namespace App\Contracts\User;

interface UpdateUserInterface
{
    /**
     * @param array $userData
     * @return mixed
     */
    public function update(array $userData);
}
