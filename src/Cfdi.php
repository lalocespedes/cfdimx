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
    protected $errors;
    protected $validate;

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

    function __construct(array $invoice)
    {
        // Valid array invoice
        $this->validate = $this->validate($invoice);
        
        if($this->validate) {

            $this->xml = new DOMdocument("1.0","UTF-8");

            $this->comprobante = new Comprobante($this->xml, $invoice['comprobante']);
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
    }

    public function build()
    {
        return $this;
    }

    public function getXml()
    {
        if(!$this->validate) {

            return json_encode($this->errors);
        }

		$this->xml->formatOutput = true;
		return $this->xml->saveXML();
	}

    public function validate(array $data)
    {
        $valid = new \lalocespedes\Validation\Validator;
        $valid->validate($data['comprobante'], [
            'formaDePago' => \Respect\Validation\Validator::noWhitespace()->length(1, 5)
        ]);

        if($valid->failed()) {

            $this->errors = $valid->errors();
            return false;
        }

        return true;
    }
}
