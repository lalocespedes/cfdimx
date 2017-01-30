<?php

namespace lalocespedes\Elementos\Retenciones;

/**
 * 
 */
class Complemento
{
    protected $complemento;
    protected $dividendos;

    function __construct($xml, $retenciones, $data)
    {
        $complemento = $xml->createElement("retenciones:Complemento");

        $dividendos = $xml->createElement("dividendos:Dividendos");

        $retenciones->appendChild($complemento);

        // Add nodo dividendos
        
        $complemento->appendChild($dividendos);

        // $this->setAttribute($data['Dividendos'], "dividendos");

        // Add nodo DividOUtil

        // $this->DividOUtil = $xml->createElement("dividendos:DividOUtil");
        // $this->complemento->appendChild($this->DividOUtil);

        // $this->setAttribute($data['DividOUtil'], "DividOUtil");
    }

    public function appendChild($value)
    {
        $this->complemento->appendChild($value);
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
