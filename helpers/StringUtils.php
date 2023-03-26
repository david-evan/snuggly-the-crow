<?php

namespace Helpers;

use Illuminate\Support\Str;

abstract class StringUtils extends Str
{
    private const CHARSET = 'UTF-8';

    /**
     * Indique si une chaîne de caractère contient le nombre de caractères
     * min/max indiqués (0 par défaut).
     * @param string $value
     * @param int $min
     * @param int $max
     * @return bool
     */
    public static function isLenBetween(string $value, int $min = 0, int $max = 0) : bool
    {
        return mb_strlen($value, self::CHARSET) >= $min
            && mb_strlen($value, self::CHARSET) <= $max;
    }
}
