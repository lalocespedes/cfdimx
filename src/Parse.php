<?php

namespace lalocespedes\Cfdimx;

use SimpleXMLElement;

class Parse
{

    public $xml;
    public $readxml;
    public $namespaces = [];

    public function __construct($xml) {

        $this->xml = $xml;

        $this->readxml = new SimpleXMLElement($this->xml);
        $this->namespaces = $this->readxml->getNamespaces(true);

        $this->readxml->registerXPathNamespace("c", $this->namespaces['cfdi']);
        $this->readxml->registerXPathNamespace("t", $this->namespaces['tfd']);

    }

    public function getTimbreFiscalDigital() {
        
        if(!array_key_exists('tfd', $this->namespaces)) {

            throw new Exception("No existe nodo Timbre Fiscal", 1);
        }

        return $this->readxml->xpath("//t:TimbreFiscalDigital")[0];
    }

    public function getComprobante() {

        if(!array_key_exists('cfdi', $this->namespaces)) {

            throw new Exception("No existe nodo Comprobante", 1);
        }

        return $this->readxml->xpath('//c:Comprobante')[0];
    }

}
