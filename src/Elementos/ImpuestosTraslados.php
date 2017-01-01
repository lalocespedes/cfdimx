<?php

namespace lalocespedes\Elementos;

/**
 * 
 */
class ImpuestosTraslados
{
    protected $impuestostraslados;

    function __construct($xml, $impuestos)
    {
        $this->impuestostraslados = $xml->createElement("cfdi:Traslados");

        $impuestos->appendChild($this->impuestostraslados);

        $RetencionesArray = [
            "0" => [
                "tasa" => "16.00", // valid format decimals
                "importe" => "1000", // valid format decimals
                "impuesto" => "IVA"
            ]
       	];

        foreach ($RetencionesArray as $key => $item) {

            $traslado = $xml->createElement("cfdi:Traslado");
            $this->impuestostraslados->appendChild($traslado);

            foreach ($item as $key => $val) {
		        $val = preg_replace('/\s\s+/', ' ', $val); // Regla 5a y 5c
		        $val = trim($val); // Regla 5b
		        if (strlen($val)>0) { // Regla 6
		            $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
		            $traslado->setAttribute($key,$val);
		        }
		    }
        }

    }
}
