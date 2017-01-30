<?php

namespace lalocespedes\Elementos\Cfdi;

/**
 * 
 */
class RegimenFiscal
{
    protected $regimenfiscal;

    function __construct($xml, $emisor, $data)
    {
        $this->regimenfiscal = $xml->createElement("cfdi:RegimenFiscal");

        $emisor->appendChild($this->regimenfiscal);

        foreach ($data as $key => $val) {
		    $val = preg_replace('/\s\s+/', ' ', $val); // Regla 5a y 5c
		    $val = trim($val); // Regla 5b
		    if (strlen($val)>0) { // Regla 6
		        $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
		        $this->regimenfiscal->setAttribute($key,$val);
		    }
		}
    }
}
