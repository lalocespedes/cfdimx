<?php

namespace lalocespedes\Elementos\Cfdi;

/**
 * 
 */
class Receptor
{
    protected $receptor;

    function __construct($xml, $comprobante, array $data)
    {
        $this->receptor = $xml->createElement("cfdi:Receptor");

        $comprobante->appendChild($this->receptor);

        foreach ($data as $key => $val) {
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
