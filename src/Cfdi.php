<?php

namespace lalocespedes;

use DOMDocument;

/**
 * 
 */
class Cfdi
{
    protected $xml;
	protected $root;

    function __construct()
    {
        $this->xml = new DOMdocument("1.0","UTF-8");
        $this->root = $this->xml->appendChild($this->xml->createElementNS("http://www.sat.gob.mx/cfd/3","cfdi:Comprobante"));
    }

    public function create()
    {
        return $this;
    }

    public function getXml() {

		$this->xml->formatOutput = true;
		return $this->xml->saveXML();
	}
}
