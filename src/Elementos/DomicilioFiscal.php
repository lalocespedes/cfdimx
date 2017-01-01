<?php

namespace lalocespedes\Elementos;

/**
 * 
 */
class DomicilioFiscal
{
    protected $domiciliofiscal;

    function __construct($xml, $emisor)
    {
        $this->domiciliofiscal = $xml->createElement("cfdi:DomicilioFiscal");

        $emisor->appendChild($this->domiciliofiscal);

        $attr = [
			"calle" => "calle 1",
			"noExterior" => "123",
			"noInterior" => "nada",
			"colonia" => "micol",
			"municipio" => "mpio",
			"estado" => "edo",
			"pais" => "mex",
			"codigoPostal" => "nose"
       	];

        foreach ($attr as $key => $val) {
		    $val = preg_replace('/\s\s+/', ' ', $val); // Regla 5a y 5c
		    $val = trim($val); // Regla 5b
		    if (strlen($val)>0) { // Regla 6
		        $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
		        $this->domiciliofiscal->setAttribute($key,$val);
		    }
		}
    }
}
