<?php

namespace App\Enums;

enum ColorTag: string
{
    case Red = '#E74C3C';
    case Green = '#2ECC71';
    case Orange = '#F39C12';
    case Purple = '#9B59B6';
    case Turquoise = '#4ECDC4';

    public function label(): string
    {
        return match ($this) {
            self::Red => 'Красный',
            self::Green => 'Зеленый',
            self::Orange => 'Оранжевый',
            self::Purple => 'Фиолетовый',
            self::Turquoise => 'Бирюзовый',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function random(): string
    {
        return self::cases()[array_rand(self::cases())]->value;
    }
}
