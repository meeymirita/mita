<?php

namespace App\Enums;

enum UserStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Inactive = 'inactive';
    case Rejected = 'rejected';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Ожидает активации',
            self::Active => 'Активный',
            self::Inactive => 'Неактивный',
            self::Rejected => 'Отклонен',
        };
    }

    public static function randomForFactory()
    {
        return self::cases()[array_rand(self::cases())];
    }
}
