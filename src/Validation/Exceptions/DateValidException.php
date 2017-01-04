<?php

namespace lalocespedes\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class DateValidException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => "FECHA NO VALIDA."
        ]
    ];
}
