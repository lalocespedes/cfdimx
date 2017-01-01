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
use Respect\Validation\Validator as v;

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
    protected $valid;

    protected $datacomprobante;
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

    /**
	 * Sets the data 
	 * @param array $data
	 */
	public function setComprobante(array $data)
	{
        $this->valid = false;

        $valid = new \lalocespedes\Validation\Complemento;
        $valid->validate($data, [
            'formaDePago' => \Respect\Validation\Validator::noWhitespace()->length(1, 50),
            'tipoDeComprobante' => \Respect\Validation\Validator::notEmpty()->noWhitespace(),
            'NumCtaPago' => \Respect\Validation\Validator::notEmpty()->noWhitespace()
        ]);

        if($valid->failed()) {

            return $this->errors = $valid->errors();
        }

		$this->datacomprobante = $data;

        return $this->valid = true;
	}

    public function build()
    {
        $this->xml = new DOMdocument("1.0","UTF-8");

        $this->comprobante = new Comprobante($this->xml, $this->datacomprobante);
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

        return $this;
    }

    public function getXml()
    {
        if($this->valid) {
            
            $this->xml->formatOutput = true;
            return $this->xml->saveXML();
        }

        return $this->xml = null;
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
