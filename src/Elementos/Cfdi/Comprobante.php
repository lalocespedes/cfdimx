<?php

namespace lalocespedes\Cfdimx\Elementos\Cfdi;

/**
 * 
 */
class Comprobante
{
    protected $comprobante;
    protected $validate;

    function __construct($xml, array $data)
    {        
        $this->comprobante = $xml->appendChild(
            $xml->createElementNS("http://www.sat.gob.mx/cfd/3","cfdi:Comprobante")
        );

        $this->comprobante->setAttributeNS(
            'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation',
            'http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd'
        );

        foreach ($data as $key => $val) {
		    $val = preg_replace('/\s+/', ' ', $val); // Regla 5a y 5c
		    $val = trim($val); // Regla 5b
		    if (strlen($val)>0) { // Regla 6
		        $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
		        $this->comprobante->setAttribute($key,$val);
		    }
		}

        return $this->comprobante;

    }

    public function appendChild($value)
    {
        $this->comprobante->appendChild($value);
    }

    public function setAttributeNS($namespaceURI, $qualifiedName, $value)
    {
        $this->comprobante->setAttributeNS(
            $namespaceURI,
            $qualifiedName,
            $value
        );
    }
}
