<?php

namespace lalocespedes;

use DOMDocument;

use lalocespedes\Elementos\Comprobante;
use lalocespedes\Elementos\Emisor;
use lalocespedes\Elementos\DomicilioFiscal;
use lalocespedes\Elementos\RegimenFiscal;
use lalocespedes\Elementos\Receptor;
use lalocespedes\Elementos\ReceptorDomicilio;
use lalocespedes\Elementos\Conceptos;
use lalocespedes\Elementos\Impuestos;
use lalocespedes\Elementos\ImpuestosRetenciones;
use lalocespedes\Elementos\ImpuestosTraslados;
use lalocespedes\Elementos\Complemento;

use lalocespedes\Exceptions\CfdiException;

/**
 * 
 */
class Cfdi
{
    protected $xml;
    protected $comprobante;
    protected $emisor;
    protected $domiciliofiscal;
    protected $receptor;
    protected $receptordomicilio;
    protected $conceptos;
    protected $impuestos;
    protected $impuestosretenciones;
    protected $impuestostraslados;
    protected $complemento;

    function __construct()
    {
        $this->xml = new DOMdocument("1.0","UTF-8");

        $this->comprobante = new Comprobante($this->xml);
        $this->emisor = new Emisor($this->xml, $this->comprobante);
        $this->domiciliofiscal = new DomicilioFiscal($this->xml, $this->emisor);
        $this->regimenfiscal = new RegimenFiscal($this->xml, $this->emisor);
        $this->receptor = new Receptor($this->xml, $this->comprobante);
        $this->receptordomicilio = new ReceptorDomicilio($this->xml, $this->receptor);
        $this->conceptos = new Conceptos($this->xml, $this->comprobante);
        $this->impuestos = new Impuestos($this->xml, $this->comprobante);
        $this->impuestosretenciones = new ImpuestosRetenciones($this->xml, $this->impuestos);
        $this->impuestostraslados = new ImpuestosTraslados($this->xml, $this->impuestos);
        $this->complemento = new Complemento($this->xml, $this->comprobante);
    }

    public function build()
    {
        return $this;
    }

    public function getXml()
    {
		$this->xml->formatOutput = true;
		return $this->xml->saveXML();
	}
}
