<?php

namespace lalocespedes\Validation;

use Respect\Validation\Validator as Respect;
use Respect\Validation\Exceptions\NestedValidationException;

/**
 * 
 */
class Complemento
{
    protected $errors = [];
    protected $required;

    public function validate(array $array, array $rules)
    {
        $this->errors['comprobante'] = [];

        if(!count($array)) {
            return $this->errors['comprobante'] = ["setComprobante esta vacio"];
        }

        $required = $this->required($array);

        foreach ($rules as $field => $rule) {
           
           try {
               $rule->setName(ucfirst($field))->assert($array[$field]);
           }catch (NestedValidationException $e) {
               array_push($this->errors['comprobante'], [
                   $field => $e->getFullMessage()
                ]);
           }
        }
        
        return $this;
    }

    public function failed()
    {
        return !empty($this->errors);
    }

    public function errors()
    {
        return $this->errors;
    }

    public function required(array $array)
    {
         $this->required = [
            "version",
            "fecha",
            "formaDePago",
            "subTotal",
            "descuento",
            "total",
            "tipoDeComprobante",
            "metodoDePago",
            "NumCtaPago",
            "Moneda",
            "TipoCambio",
            "LugarExpedicion"
        ];

        $this->errors['comprobante'] = [];

        foreach($this->required as $key => $value){

            if(!array_key_exists($value, $array)) {
                array_push($this->errors['comprobante'], [
                    $value => $value. " is required"
                ]);
            }
        }
    }
}
