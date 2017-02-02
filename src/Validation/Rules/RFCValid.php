<?php

namespace lalocespedes\Cfdimx\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

class RFCValid extends AbstractRule
{
    public function validate($input)
    {
        $regex = "/^[A-ZÑ&]{3,4}[0-9]{2}[0-1][0-9][0-3][0-9][A-Z,0-9][A-Z,0-9][0-9A]$/";

        if (!preg_match($regex, $input, $result)) {
            return false;
        }
        
        return true;
    }
}
