<?php

namespace lalocespedes\Cfdimx\Elementos\Retenciones;

/**
 * 
 */
class Periodo
{
    protected $periodo;

    function __construct($xml, $retenciones, array $data)
    {
        $this->periodo = $xml->createElement("retenciones:Periodo");

        $retenciones->appendChild($this->periodo);

        $this->setAttribute($data, "periodo");

    }

    public function appendChild($value)
    {
        $this->receptor->appendChild($value);
    }

    private function setAttribute($data, $nodo)
    {
        foreach ($data as $key => $val) {
		    $val = preg_replace('/\s\s+/', ' ', $val); // Regla 5a y 5c
		    $val = trim($val); // Regla 5b
		    if (strlen($val)>0) { // Regla 6
		        $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
                $this->{$nodo}->setAttribute($key,$val);
		    }
		}

    }
}
