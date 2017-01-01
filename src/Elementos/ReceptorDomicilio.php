<?php

namespace lalocespedes\Elementos;

/**
 * 
 */
class ReceptorDomicilio
{
    protected $receptordomicilio;

    function __construct($xml, $receptor)
    {
        $this->receptordomicilio = $xml->createElement("cfdi:Domicilio");

        $receptor->appendChild($this->receptordomicilio);

        $attr = [
			"calle" => "calle 1",
			"noExterior" => "123",
			"noInterior" => "nada",
			"colonia" => "micol",
            "localidad" => "local",
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
		        $this->receptordomicilio->setAttribute($key,$val);
		    }
		}
    }
}
