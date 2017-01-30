<?php

namespace lalocespedes\Elementos\Cfdi;

/**
 * 
 */
class Complemento
{
    protected $complemento;

    function __construct($xml, $comprobante)
    {
        $this->complemento = $xml->createElement("cfdi:Complemento");

        $comprobante->appendChild($this->complemento);

        $attr = [];

        foreach ($attr as $key => $val) {
		    $val = preg_replace('/\s\s+/', ' ', $val); // Regla 5a y 5c
		    $val = trim($val); // Regla 5b
		    if (strlen($val)>0) { // Regla 6
		        $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
		        $this->complemento->setAttribute($key,$val);
		    }
		}
    }

    public function appendChild($value)
    {
        $this->complemento->appendChild($value);
    }
}
