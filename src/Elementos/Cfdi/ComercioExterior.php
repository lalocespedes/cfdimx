<?php

namespace lalocespedes\Cfdimx\Elementos\Cfdi;

/**
 * 
 */
class ComercioExterior
{
    protected $comercioexterior;

    function __construct($xml, array $data)
    {        
        $xml->setAttributeNS(
            'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation',
            'http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd http://www.sat.gob.mx/ComercioExterior http://www.sat.gob.mx/sitio_internet/cfd/ComercioExterior/ComercioExterior10.xsd'
        );

        $xml->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:cce',
            'http://www.sat.gob.mx/ComercioExterior'
        );

        foreach ($data as $key => $val) {
		    $val = preg_replace('/\s+/', ' ', $val); // Regla 5a y 5c
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
