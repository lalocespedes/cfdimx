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
    protected $cerfile;
    protected $keypemfile;

    public function __construct()
    {
        v::with('lalocespedes\\Validation\\Rules\\');
    }

    /**
	 * Sets the data Comprobante
	 * @param array $data
	 */
	public function setComprobante(array $data)
	{
        // valid data
        $valid = new \lalocespedes\Validation\Comprobante;
        $valid->validate($data, [
            'version' => v::notEmpty()->noWhitespace(),
            'serie' => v::length(1, 25),
            'folio' => v::length(1, 20),
            'fecha' => v::notEmpty()->date('Y-m-d\TH:i:s')->dateValid(),
            'sello' => v::notEmpty(),
            'formaDePago' => v::notEmpty(),
            'noCertificado' => v::notEmpty()->length(1, 20),
            'certificado' => v::notEmpty()->length(1, 20),
            'condicionesDePago' => v::length(1, 100),
            'subTotal' => v::notEmpty()->floatVal(),
            'descuento' => v::floatVal(),
            'motivoDescuento' => v::alpha(),
            'TipoCambio' => v::floatVal(),
            'Moneda' => v::alpha(),
            'total' => v::notEmpty()->floatVal(),
            'tipoDeComprobante' => v::notEmpty()->alpha(),
            'metodoDePago' => v::notEmpty()->alpha(),
            'LugarExpedicion' => v::notEmpty(),
            'NumCtaPago' => v::noWhitespace()->min(4),
            'FolioFiscalOrig' => v::noWhitespace(),
            'SerieFolioFiscalOrig' => v::noWhitespace(),
            'FechaFolioFiscalOrig' => v::noWhitespace(),
            'MontoFolioFiscalOrig' => v::noWhitespace()->floatVal()
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
            'rfc' => v::noWhitespace()->notEmpty()->RFCValid(),
            'nombre' => v::stringType()
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
            'calle' => v::notEmpty(),
            'noExterior' => v::stringType(),
            'noInterior' => v::stringType(),
            'colonia' => v::stringType(),
            'localidad' => v::stringType(),
            'referencia' => v::stringType(),
            'municipio' => v::notEmpty()->stringType(),
            'estado' => v::notEmpty()->stringType(),
            'pais' => v::notEmpty()->stringType(),
            'codigoPostal' => v::notEmpty()->stringType(),
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
            'Regimen' => v::notEmpty()
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
            'rfc' => v::noWhitespace()->notEmpty()->RFCValid(),
            'nombre' => v::stringType()
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
            'calle' => v::notEmpty(),
            'noExterior' => v::stringType(),
            'noInterior' => v::stringType(),
            'colonia' => v::stringType(),
            'localidad' => v::stringType(),
            'referencia' => v::stringType(),
            'municipio' => v::notEmpty()->stringType(),
            'estado' => v::notEmpty()->stringType(),
            'pais' => v::notEmpty()->stringType(),
            'codigoPostal' => v::notEmpty()->stringType(),
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
                'cantidad' => v::notEmpty()->floatVal(),
                'unidad' => v::notEmpty(),
                'noIdentificacion' => v::stringType(),
                'descripcion' => v::notEmpty(),
                'valorUnitario' => v::notEmpty(),
                'importe' => v::notEmpty()
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
            'totalImpuestosRetenidos' => v::floatVal(),
            'totalImpuestosTrasladados' => v::floatVal()
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

        if(is_null($this->dataimpuestos)) {

            $this->errors = [
                "please setImpuestos node"
            ]; 
            $this->valid = false;
            return $this;

        }

        $valid = new \lalocespedes\Validation\ImpuestosRetenciones;

        foreach ($data as $key => $value) {

            $valid->validate($value, [
                'impuesto' => v::notEmpty(),
                'importe' => v::notEmpty()->floatVal()
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
        
        if(is_null($this->dataimpuestos)) {

            $this->errors = [
                "please setImpuestos node"
            ]; 
            $this->valid = false;
            return $this;

        }

        $valid = new \lalocespedes\Validation\ImpuestosTrasladados;

        foreach ($data as $key => $value) {

            $valid->validate($value, [
                'impuesto' => v::notEmpty(),
                'tasa' => v::notEmpty()->floatVal(),
                'importe' => v::notEmpty()->floatVal()
            ]);
        }

        if($valid->failed()) {

            $this->valid = false;
            return $this->errors = $valid->errors();
        }
        
        $this->dataimpuestostrasladados = $data;

    }

    /**
	* Sets the files Certificado
	* @param file $cer
    * @param file $key
	*/
    public function setCertificado($cer, $key)
    {
        $this->cerfile = $cer;
        $this->keypemfile = $key;
    }

    public function build()
    {
        $this->xml = new DOMdocument("1.0","UTF-8");

        if(is_null($this->datacomprobante)) {

            $this->errors = [
                "please setComprobante node"
            ]; 
            $this->valid = false;
            return $this;

        }

        $this->comprobante = new Comprobante($this->xml, $this->datacomprobante);

        if(is_null($this->dataemisor)) {

            $this->errors = [
                "please setEmisor node"
            ]; 
            $this->valid = false;
            return $this;

        }

        $this->emisor = new Emisor($this->xml, $this->comprobante, $this->dataemisor);

        if(!is_null($this->dataemisordomiciliofiscal)) {
            $this->domiciliofiscal = new DomicilioFiscal($this->xml, $this->emisor, $this->dataemisordomiciliofiscal);
        }

        if(is_null($this->dataregimenfiscal)) {

            $this->errors = [
                "please setRegimenFiscal node"
            ]; 
            $this->valid = false;
            return $this;

        }

        $this->regimenfiscal = new RegimenFiscal($this->xml, $this->emisor, $this->dataregimenfiscal);

        if(is_null($this->datareceptor)) {

            $this->errors = [
                "please setReceptor node"
            ]; 
            $this->valid = false;
            return $this;

        }

        $this->receptor = new Receptor($this->xml, $this->comprobante, $this->datareceptor);

        if(!is_null($this->datareceptordomicilio)) {
            $this->receptordomicilio = new ReceptorDomicilio($this->xml, $this->receptor, $this->datareceptordomicilio);
        }

        if(is_null($this->dataconceptos)) {

            $this->errors = [
                "please setConceptos node"
            ]; 
            $this->valid = false;
            return $this;

        }

        $this->conceptos = new Conceptos($this->xml, $this->comprobante, $this->dataconceptos);

        if(!is_null($this->dataimpuestos)) {

            $this->impuestos = new Impuestos($this->xml, $this->comprobante, $this->dataimpuestos);

            if(is_null($this->dataimpuestosretenciones)) {

                $this->errors = [
                    "please setImpuestosRetenciones node"
                ]; 
                $this->valid = false;
                return $this;
            }

            $this->impuestosretenciones = new ImpuestosRetenciones($this->xml, $this->impuestos, $this->dataimpuestosretenciones);

            if(is_null($this->dataimpuestostrasladados)) {

                $this->errors = [
                    "please setImpuestosTrasladados node"
                ]; 
                $this->valid = false;
                return $this;
            }

            $this->impuestostraslados = new ImpuestosTraslados($this->xml, $this->impuestos, $this->dataimpuestostrasladados);

        }
        
        $this->complemento = new Complemento($this->xml, $this->comprobante);

        $this->xml->formatOutput = true;

        // sellar xml
        if(is_null($this->cerfile) || is_null($this->keypemfile)) {

            $this->errors = [
                "please set setCertificado"
            ]; 
            $this->valid = false;
            return $this;

        }

        //Get CSD
        try {
            
            $csd = new \lalocespedes\Csd(dirname($this->cerfile));
            $this->cerfilecontent = $csd->getCer(basename($this->cerfile));
            $this->keypemfilecontent = $csd->getKeyPem(basename($this->keypemfile));
            $this->noCertificado = $csd->getnoCertificado($this->cerfile);

        } catch ( \League\Flysystem\FileNotFoundException $e) {

            $this->errors = [
                $e->getMessage()
            ]; 
            $this->valid = false;
            return $this;

        }

        $xml = new \lalocespedes\Sello();

        $this->xml = $xml->getSello($this->xml->saveXML(), $this->cerfilecontent, $this->keypemfilecontent, $this->noCertificado);

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