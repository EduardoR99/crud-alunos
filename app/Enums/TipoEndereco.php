<?php

namespace App\Enums;

enum TipoEndereco: string
{
    case RESIDENCIAL = 'residencial';
    case COMERCIAL = 'comercial';
    case OUTRO = 'outro';

    public static function tryFromNullable(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }

        return self::tryFrom($value);
    }
}
