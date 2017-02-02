<?php

namespace lalocespedes\Cfdimx\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class RFCValidException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => "RFC NO VALIDO."
        ]
    ];
}
