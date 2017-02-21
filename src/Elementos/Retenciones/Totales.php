<?php

namespace lalocespedes\Cfdimx\Elementos\Retenciones;

/**
 * 
 */
class Totales
{
    protected $totales;
    protected $impretenidos;

    function __construct($xml, $retenciones, array $data, array $impretenidos=null)
    {
        $this->totales = $xml->createElement("retenciones:Totales");

        $retenciones->appendChild($this->totales);

        $this->setAttribute($data, "totales");

        // add nodos impretenidos

        if(count($impretenidos)) {
            
            foreach ($impretenidos as $key => $value) {

                $this->impretenidos = $xml->createElement("retenciones:ImpRetenidos");
                $this->totales->appendChild($this->impretenidos);
            
                $this->setAttribute([
                    "TipoPagoRet" => $value['TipoPagoRet'],
                    "montoRet" => $value['montoRet'],
                    "Impuesto" => $value['Impuesto'],
                    "BaseRet" => $value['BaseRet']
                ], 'impretenidos');
            }
        }
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
