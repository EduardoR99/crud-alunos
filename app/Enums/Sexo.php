<?php

namespace App\Enums;

enum Sexo: string
{
    case MASCULINO = 'M';
    case FEMININO = 'F';

    public static function tryFromNullable(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }

        return self::tryFrom($value);
    }
}
