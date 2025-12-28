<?php

namespace App\Enums;

enum UserType: string
{
    case User = 'user';
    case Admin = 'admin';
    case SuperAdmin = 'superadmin';

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }

    public function label() : string
    {
        return match ($this) {
            self::User => 'Пользователь',
            self::Admin => 'Администратор',
            self::SuperAdmin => 'Супер администратор',
        };
    }

    public static function randomForFactory()
    {
        return self::cases()[array_rand(self::cases())];
    }
}
