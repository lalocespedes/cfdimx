<?php

namespace lalocespedes\Validation;

use Respect\Validation\Validator as Respect;
use Respect\Validation\Exceptions\NestedValidationException;

/**
 * 
 */
class Retenciones
{
    protected $errors = [];
    protected $required;

    public function validate(array $array, array $rules)
    {
        if(!count($array)) {
            return $this->errors = ["setRetenciones esta vacio"];
        }

        $this->required($array);

        foreach ($rules as $field => $rule) {
           
           try {

               if(array_key_exists($field, $array)) {
                    $rule->setName(ucfirst($field))->assert($array[$field]);
               }
           }catch (NestedValidationException $e) {
               array_push($this->errors, [
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
            "Version",
            "FechaExp",
            "CveRetenc"
        ];

        foreach($this->required as $key => $value){

            if(!array_key_exists($value, $array)) {
                array_push($this->errors, [
                    $value => $value. " is required"
                ]);
            }
        }
    }
}
