<?php

namespace lalocespedes\Cfdimx\V_33;

/**
 * 
 */
class Cfdi
{
    /**
    * @var array
    */
    protected $errors = [];

    /**
    * @var bool
    */
    protected $valid = true;

    protected $xml;

    public function build()
    {
        $this->xml = new DOMdocument("1.0","UTF-8");

        $this->comprobante = $this->xml->appendChild(
            $this->xml->createElementNS("http://www.sat.gob.mx/cfd/3","cfdi:Comprobante")
        );

        // valid xml xsd

        return $this;

    }

    public function getXml()
    {
        if($this->valid) {
            
            return $this->xml;
        }
        $this->xml = null;
        return $this->errors();
    }

    public function failed()
    {
        return !empty($this->errors);
    }
    
    public function errors()
    {
        return $this->errors;
    }

}
