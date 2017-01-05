<?php

namespace lalocespedes\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class TipoDeComprobanteValidException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => "Tipo de comprobante NO VALIDO. {'ingreso', 'egreso', 'traslado'}"
        ]
    ];
}
