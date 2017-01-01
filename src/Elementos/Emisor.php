<?php

namespace lalocespedes\Elementos;

/**
 * 
 */
class Emisor
{
    protected $emisor;

    function __construct($xml, $comprobante)
    {
        $this->emisor = $xml->createElement("cfdi:Emisor");

        $comprobante->appendChild($this->emisor);

        $attr = [
			"rfc" => "YOOO",
			"nombre" => "TUUU"
       	];

        foreach ($attr as $key => $val) {
		    $val = preg_replace('/\s\s+/', ' ', $val); // Regla 5a y 5c
		    $val = trim($val); // Regla 5b
		    if (strlen($val)>0) { // Regla 6
		        $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
		        $this->emisor->setAttribute($key,$val);
		    }
		}
    }

    public function appendChild($value)
    {
        $this->emisor->appendChild($value);
    }
}
