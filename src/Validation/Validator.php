<?php

namespace lalocespedes\Cfdimx\Validation;

use Respect\Validation\Validator as Respect;
use Respect\Validation\Exceptions\NestedValidationException;

class Validator
{
    protected $errors = [];

    public function validate(array $array, array $rules)
    {
        if(!count($array)) {
            
            $this->errors = ["No hay datos q validar"];
            return $this;
        }
        
        foreach ($rules as $field => $rule) {
            
            try {
                
                $rule->setName(ucfirst($field))->assert($array[$field]);
                
            } catch (NestedValidationException $e) {

                $this->errors[$field] = $e->getMessages();        
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
}
