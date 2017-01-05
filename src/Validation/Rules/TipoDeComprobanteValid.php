<?php

namespace lalocespedes\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

class TipoDeComprobanteValid extends AbstractRule
{
    public function validate($input)
    {
        $valid = [
            "ingreso",
            "egreso",
            "traslado"
        ];

        if(!in_array($input, $valid)) {

            return false;
        }

        return true;
    }
}
