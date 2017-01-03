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
    protected $valid = true;

    protected $datacomprobante;
    protected $dataemisor;
    protected $dataemisordomiciliofiscal;
    protected $dataregimenfiscal;
    protected $datareceptor;
    protected $datareceptordomicilio;
    protected $dataconceptos;
    protected $dataimpuestos;
    protected $dataimpuestosretenciones;
    protected $dataimpuestostrasladados;
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
	 * Sets the data Comprobante
	 * @param array $data
	 */
	public function setComprobante(array $data)
	{
        // valid data
        $valid = new \lalocespedes\Validation\Complemento;
        $valid->validate($data, [
            'version' => \Respect\Validation\Validator::notEmpty()->noWhitespace(),
            'serie' => \Respect\Validation\Validator::length(1, 25),
            'folio' => \Respect\Validation\Validator::length(1, 20),
            'fecha' => \Respect\Validation\Validator::notEmpty(),
            'sello' => \Respect\Validation\Validator::notEmpty(),
            'formaDePago' => \Respect\Validation\Validator::notEmpty(),
            'noCertificado' => \Respect\Validation\Validator::notEmpty()->length(1, 20),
            'certificado' => \Respect\Validation\Validator::notEmpty()->length(1, 20),
            'condicionesDePago' => \Respect\Validation\Validator::length(1, 100),
            'subTotal' => \Respect\Validation\Validator::notEmpty()->floatVal(),
            'descuento' => \Respect\Validation\Validator::floatVal(),
            'motivoDescuento' => \Respect\Validation\Validator::alpha(),
            'TipoCambio' => \Respect\Validation\Validator::floatVal(),
            'Moneda' => \Respect\Validation\Validator::alpha(),
            'total' => \Respect\Validation\Validator::notEmpty()->floatVal(),
            'tipoDeComprobante' => \Respect\Validation\Validator::notEmpty()->alpha(),
            'metodoDePago' => \Respect\Validation\Validator::notEmpty()->alpha(),
            'LugarExpedicion' => \Respect\Validation\Validator::notEmpty(),
            'NumCtaPago' => \Respect\Validation\Validator::noWhitespace()->min(4),
            'FolioFiscalOrig' => \Respect\Validation\Validator::noWhitespace(),
            'SerieFolioFiscalOrig' => \Respect\Validation\Validator::noWhitespace(),
            'FechaFolioFiscalOrig' => \Respect\Validation\Validator::noWhitespace(),
            'MontoFolioFiscalOrig' => \Respect\Validation\Validator::noWhitespace()->floatVal()
        ]);

        if($valid->failed()) {

            $this->valid = false;
            return $this->errors = $valid->errors();
        }

		$this->datacomprobante = $data;
	}

    /**
	 * Sets the data Emisor
	 * @param array $data
	 */
	public function setEmisor(array $data)
	{
        // Valid data
        $valid = new \lalocespedes\Validation\Emisor;
        $valid->validate($data, [
            'rfc' => \Respect\Validation\Validator::notEmpty(),
            'nombre' => \Respect\Validation\Validator::stringType()
        ]);

        if($valid->failed()) {

            $this->valid = false;
            return $this->errors = $valid->errors();
        }

        $this->dataemisor = $data;
    }

    /**
	 * Sets the data Emisor Domicilio Fiscal
	 * @param array $data
	 */
	public function setEmisorDomicilioFiscal(array $data)
	{
        $valid = new \lalocespedes\Validation\EmisorDomicilioFiscal;
        $valid->validate($data, [
            'calle' => \Respect\Validation\Validator::notEmpty(),
            'noExterior' => \Respect\Validation\Validator::stringType(),
            'noInterior' => \Respect\Validation\Validator::stringType(),
            'colonia' => \Respect\Validation\Validator::stringType(),
            'localidad' => \Respect\Validation\Validator::stringType(),
            'referencia' => \Respect\Validation\Validator::stringType(),
            'municipio' => \Respect\Validation\Validator::notEmpty()->stringType(),
            'estado' => \Respect\Validation\Validator::notEmpty()->stringType(),
            'pais' => \Respect\Validation\Validator::notEmpty()->stringType(),
            'codigoPostal' => \Respect\Validation\Validator::notEmpty()->stringType(),
        ]);

        if($valid->failed()) {

            $this->valid = false;
            return $this->errors = $valid->errors();
        }
        
        $this->dataemisordomiciliofiscal = $data;

    }

    /**
	 * Sets the data Regimen Fiscal
	 * @param array $data
	 */
    public function setRegimenFiscal(array $data)
    {
        $valid = new \lalocespedes\Validation\RegimenFiscal;
        $valid->validate($data, [
            'Regimen' => \Respect\Validation\Validator::notEmpty()
        ]);

        if($valid->failed()) {

            $this->valid = false;
            return $this->errors = $valid->errors();
        }
        
        $this->dataregimenfiscal = $data;

    }

    /**
	 * Sets the data Receptor
	 * @param array $data
	 */
    public function setReceptor(array $data)
    {
        $valid = new \lalocespedes\Validation\Receptor;
        $valid->validate($data, [
            'rfc' => \Respect\Validation\Validator::notEmpty(),
            'nombre' => \Respect\Validation\Validator::stringType()
        ]);

        if($valid->failed()) {

            $this->valid = false;
            return $this->errors = $valid->errors();
        }
        
        $this->datareceptor = $data;

    }

    /**
	 * Sets the data ReceptorDomicilio
	 * @param array $data
	 */
    public function setReceptorDomicilio(array $data)
    {
        $valid = new \lalocespedes\Validation\ReceptorDomicilio;
        $valid->validate($data, [
            'calle' => \Respect\Validation\Validator::notEmpty(),
            'noExterior' => \Respect\Validation\Validator::stringType(),
            'noInterior' => \Respect\Validation\Validator::stringType(),
            'colonia' => \Respect\Validation\Validator::stringType(),
            'localidad' => \Respect\Validation\Validator::stringType(),
            'referencia' => \Respect\Validation\Validator::stringType(),
            'municipio' => \Respect\Validation\Validator::notEmpty()->stringType(),
            'estado' => \Respect\Validation\Validator::notEmpty()->stringType(),
            'pais' => \Respect\Validation\Validator::notEmpty()->stringType(),
            'codigoPostal' => \Respect\Validation\Validator::notEmpty()->stringType(),
        ]);

        if($valid->failed()) {

            $this->valid = false;
            return $this->errors = $valid->errors();
        }
        
        $this->datareceptordomicilio = $data;

    }

    /**
	 * Sets the data Conceptos
	 * @param array $data
	 */
    public function setConceptos(array $data)
    {
        $valid = new \lalocespedes\Validation\Conceptos;

        foreach ($data as $key => $value) {

            $valid->validate($value, [
                'cantidad' => \Respect\Validation\Validator::notEmpty()->floatVal(),
                'unidad' => \Respect\Validation\Validator::notEmpty(),
                'noIdentificacion' => \Respect\Validation\Validator::stringType(),
                'descripcion' => \Respect\Validation\Validator::notEmpty(),
                'valorUnitario' => \Respect\Validation\Validator::notEmpty(),
                'importe' => \Respect\Validation\Validator::notEmpty()
            ]);
        }

        if($valid->failed()) {

            $this->valid = false;
            return $this->errors = $valid->errors();
        }
        
        $this->dataconceptos = $data;

    }

    /**
	 * Sets the data Impuestos
	 * @param array $data
	 */
    public function setImpuestos(array $data)
    {
        $valid = new \lalocespedes\Validation\Impuestos;

        $valid->validate($data, [
            'totalImpuestosRetenidos' => \Respect\Validation\Validator::floatVal(),
            'totalImpuestosTrasladados' => \Respect\Validation\Validator::floatVal()
        ]);

        if($valid->failed()) {

            $this->valid = false;
            return $this->errors = $valid->errors();
        }
        
        $this->dataimpuestos = $data;

    }

    /**
	 * Sets the data ImpuestosRetenciones
	 * @param array $data
	 */
    public function setImpuestosRetenciones(array $data)
    {
        $valid = new \lalocespedes\Validation\ImpuestosRetenciones;

        foreach ($data as $key => $value) {

            $valid->validate($value, [
                'impuesto' => \Respect\Validation\Validator::notEmpty(),
                'importe' => \Respect\Validation\Validator::notEmpty()->floatVal()
            ]);
        }

        if($valid->failed()) {

            $this->valid = false;
            return $this->errors = $valid->errors();
        }
        
        $this->dataimpuestosretenciones = $data;

    }

    /**
	 * Sets the data ImpuestosTrasladados
	 * @param array $data
	 */
    public function setImpuestosTrasladados(array $data)
    {
        $valid = new \lalocespedes\Validation\ImpuestosTrasladados;

        foreach ($data as $key => $value) {

            $valid->validate($value, [
                'impuesto' => \Respect\Validation\Validator::notEmpty(),
                'tasa' => \Respect\Validation\Validator::notEmpty()->floatVal(),
                'importe' => \Respect\Validation\Validator::notEmpty()->floatVal()
            ]);
        }

        if($valid->failed()) {

            $this->valid = false;
            return $this->errors = $valid->errors();
        }
        
        $this->dataimpuestostrasladados = $data;

    }

    public function build()
    {
        $this->xml = new DOMdocument("1.0","UTF-8");

        $this->comprobante = new Comprobante($this->xml, $this->datacomprobante);
        $this->emisor = new Emisor($this->xml, $this->comprobante, $this->dataemisor);
        $this->domiciliofiscal = new DomicilioFiscal($this->xml, $this->emisor, $this->dataemisordomiciliofiscal);
        $this->regimenfiscal = new RegimenFiscal($this->xml, $this->emisor, $this->dataregimenfiscal);
        $this->receptor = new Receptor($this->xml, $this->comprobante, $this->datareceptor);
        $this->receptordomicilio = new ReceptorDomicilio($this->xml, $this->receptor, $this->datareceptordomicilio);
        $this->conceptos = new Conceptos($this->xml, $this->comprobante, $this->dataconceptos);
        if(!is_null($this->dataimpuestos)) {
            $this->impuestos = new Impuestos($this->xml, $this->comprobante, $this->dataimpuestos);
            $this->impuestosretenciones = new ImpuestosRetenciones($this->xml, $this->impuestos, $this->dataimpuestosretenciones );
            $this->impuestostraslados = new ImpuestosTraslados($this->xml, $this->impuestos, $this->dataimpuestostrasladados);
        }
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
