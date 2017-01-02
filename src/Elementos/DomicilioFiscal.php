<?php

namespace lalocespedes\Elementos;

/**
 * 
 */
class DomicilioFiscal
{
    protected $domiciliofiscal;

    function __construct($xml, $emisor, $data)
    {
        $this->domiciliofiscal = $xml->createElement("cfdi:DomicilioFiscal");

        $emisor->appendChild($this->domiciliofiscal);

        foreach ($data as $key => $val) {
		    $val = preg_replace('/\s\s+/', ' ', $val); // Regla 5a y 5c
		    $val = trim($val); // Regla 5b
		    if (strlen($val)>0) { // Regla 6
		        $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
		        $this->domiciliofiscal->setAttribute($key,$val);
		    }
		}
    }
}
