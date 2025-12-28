<?php

namespace App\Enums;

enum UserCheckLoginField : string
{
    case Login = 'login';
    case Email = 'email';

    public function label(): string
    {
        return $this->value;
    }
}
