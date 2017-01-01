<?php

namespace lalocespedes\Elementos;

/**
 * 
 */
class Conceptos
{
    protected $conceptos;

    function __construct($xml, $comprobante)
    {
        $this->conceptos = $xml->createElement("cfdi:Conceptos");

        $comprobante->appendChild($this->conceptos);

        $conceptosArray = [
			"0" => [
                "noIdentificacion" => "222",
                "cantidad" => "50",
                "unidad" => "PZA",
                "descripcion" => "DESC",
                "valorUnitario" => number_format("1000", 2, '.',''),
                "importe" => number_format("1100", 2, '.','')
            ],
            "1" => [
                "noIdentificacion" => "111",
                "cantidad" => "50",
                "unidad" => "PZA",
                "descripcion" => "DESC2",
                "valorUnitario" => number_format("10002", 2, '.',''),
                "importe" => number_format("11002", 2, '.','')
            ]
       	];
        
        foreach ($conceptosArray as $key => $item) {

            $concepto = $xml->createElement("cfdi:Concepto");
            $this->conceptos->appendChild($concepto);

            foreach ($item as $key => $val) {
		        $val = preg_replace('/\s\s+/', ' ', $val); // Regla 5a y 5c
		        $val = trim($val); // Regla 5b
		        if (strlen($val)>0) { // Regla 6
		            $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
		            $concepto->setAttribute($key,$val);
		        }
		    }
        }
    }
}
