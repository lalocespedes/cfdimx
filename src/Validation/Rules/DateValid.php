<?php

namespace lalocespedes\Cfdimx\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

class DateValid extends AbstractRule
{
    public function validate($input)
    {

        // Que el rango de la fecha de generacion no sea mayor a 72 horas para la emision del timbre
        if(new \DateTime($input) <= \Carbon\Carbon::now()->subDays(3)) {
            
            return false;

        }

        // no date future
        if(new \DateTime($input) > \Carbon\Carbon::now()) {

            return false;
        }

        return true;
    }
}
