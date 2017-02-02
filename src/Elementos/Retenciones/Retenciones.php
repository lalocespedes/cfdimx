<?php

namespace lalocespedes\Cfdimx\Elementos\Retenciones;

/**
 * 
 */
class Retenciones
{
    protected $retenciones;

    function __construct($xml, array $data)
    {        
        $this->retenciones = $xml->appendChild(
            $xml->createElementNS("http://www.sat.gob.mx/esquemas/retencionpago/1","retenciones:Retenciones")
        );

        $this->retenciones->setAttributeNS(
            'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation',
            'http://www.sat.gob.mx/esquemas/retencionpago/1 http://www.sat.gob.mx/esquemas/retencionpago/1/retencionpagov1.xsd http://www.sat.gob.mx/esquemas/retencionpago/1/dividendos http://www.sat.gob.mx/esquemas/retencionpago/1/dividendos/dividendos.xsd'
        );

        $this->retenciones->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:dividendos',
            'http://www.sat.gob.mx/esquemas/retencionpago/1/dividendos'
        );

        foreach ($data as $key => $val) {
		    $val = preg_replace('/\s\s+/', ' ', $val); // Regla 5a y 5c
		    $val = trim($val); // Regla 5b
		    if (strlen($val)>0) { // Regla 6
		        $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
		        $this->retenciones->setAttribute($key,$val);
		    }
		}
    }

    public function appendChild($value)
    {
        $this->retenciones->appendChild($value);
    }
}
