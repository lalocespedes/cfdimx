<?php

namespace lalocespedes\Elementos;

/**
 * 
 */
class Receptor
{
    protected $receptor;

    function __construct($xml, $comprobante)
    {
        $this->receptor = $xml->createElement("cfdi:Receptor");

        $comprobante->appendChild($this->receptor);

        $attr = [
			"rfc" => "YOOORECEPTOR",
			"nombre" => "TUUURECEPTOR"
       	];

        foreach ($attr as $key => $val) {
		    $val = preg_replace('/\s\s+/', ' ', $val); // Regla 5a y 5c
		    $val = trim($val); // Regla 5b
		    if (strlen($val)>0) { // Regla 6
		        $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
		        $this->receptor->setAttribute($key,$val);
		    }
		}
    }

    public function appendChild($value)
    {
        $this->receptor->appendChild($value);
    }
}
