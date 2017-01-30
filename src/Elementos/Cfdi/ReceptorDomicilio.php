<?php

namespace lalocespedes\Elementos\Cfdi;

/**
 * 
 */
class ReceptorDomicilio
{
    protected $receptordomicilio;

    function __construct($xml, $receptor, array $data)
    {
        $this->receptordomicilio = $xml->createElement("cfdi:Domicilio");

        $receptor->appendChild($this->receptordomicilio);

        foreach ($data as $key => $val) {
		    $val = preg_replace('/\s\s+/', ' ', $val); // Regla 5a y 5c
		    $val = trim($val); // Regla 5b
		    if (strlen($val)>0) { // Regla 6
		        $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
		        $this->receptordomicilio->setAttribute($key,$val);
		    }
		}
    }
}
