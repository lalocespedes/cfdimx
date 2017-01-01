<?php

namespace lalocespedes\Elementos;

/**
 * 
 */
class Impuestos
{
    protected $impuestos;

    function __construct($xml, $comprobante)
    {
        $this->impuestos = $xml->createElement("cfdi:Impuestos");

        $comprobante->appendChild($this->impuestos);

        $attr = [
			"totalImpuestosRetenidos" => "000", // valid format decimals
			"totalImpuestosTrasladados" => "000"
       	];

        foreach ($attr as $key => $val) {
		    $val = preg_replace('/\s\s+/', ' ', $val); // Regla 5a y 5c
		    $val = trim($val); // Regla 5b
		    if (strlen($val)>0) { // Regla 6
		        $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
		        $this->impuestos->setAttribute($key,$val);
		    }
		}
    }

    public function appendChild($value)
    {
        $this->impuestos->appendChild($value);
    }
}
