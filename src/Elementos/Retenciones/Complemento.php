<?php

namespace lalocespedes\Cfdimx\Elementos\Retenciones;

/**
 * 
 */
class Complemento
{
    protected $complemento;
    protected $dividendos;

    function __construct($xml, $retenciones, $data)
    {
        // Create Element retenciones:Complemento
        $complemento = $xml->createElement("retenciones:Complemento");
        $retenciones->appendChild($complemento);

        if(array_key_exists("Dividendos", $data)) {

            // Create Element dividendos:Dividendos
            $dividendos = $xml->createElement("dividendos:Dividendos");
            $this->dividendos = $complemento->appendChild($dividendos);

            // Set Attibutes
            $this->setAttribute($data['Dividendos'], "dividendos");

            // Create Element dividendos:DividOUtil
            $dividOUtil = $xml->createElement("dividendos:DividOUtil");
            $this->DividOUtil = $dividendos->appendChild($dividOUtil);

            $this->setAttribute($data['DividOUtil'], "DividOUtil");
        }

    }

    public function appendChild($value)
    {
        $this->complemento->appendChild($value);
    }
    
    private function setAttribute(array $data, $nodo)
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
