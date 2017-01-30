<?php

namespace lalocespedes\Elementos;

/**
 * 
 */
class ImpuestosRetenciones
{
    protected $impuestosretenciones;

    function __construct($xml, $impuestos, array $data)
    {
        $this->impuestosretenciones = $xml->createElement("cfdi:Retenciones");

        $impuestos->appendChild($this->impuestosretenciones);

        foreach ($data as $key => $item) {

            $retencion = $xml->createElement("cfdi:Retencion");
            $this->impuestosretenciones->appendChild($retencion);

            foreach ($item as $key => $val) {
		        $val = preg_replace('/\s\s+/', ' ', $val); // Regla 5a y 5c
		        $val = trim($val); // Regla 5b
		        if (strlen($val)>0) { // Regla 6
		            $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
		            $retencion->setAttribute($key,$val);
		        }
		    }
        }

    }
}
