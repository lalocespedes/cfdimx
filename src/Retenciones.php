<?php

namespace lalocespedes;

use DOMDocument;

/**
 * 
 */
class Retenciones
{
    /**
    * @var array
    */
    protected $errors = [];

    /**
    * @var bool
    */
    protected $valid = true;

    protected $cerfile;
    protected $keypemfile;
    protected $retenciones;
    protected $emisor;
    protected $receptor;
    protected $periodo;
    protected $totales;
    protected $complemento;

    // Data
    protected $dataretenciones;
    protected $dataemisor;
    protected $datareceptor;
    protected $dataperiodo;
    protected $datatotales;
    protected $dataimpretenidos;
    protected $datacomplemento;

    /**
	* Sets the data Retenciones
	* @param array $data
	*/
	public function setRetenciones(array $data = [])
	{
        $this->dataretenciones = $data;
    }

    /**
	* Sets the data Emisor
	* @param array $data
	*/
	public function setEmisor(array $data)
	{
        // // Valid data
        // $valid = new \lalocespedes\Validation\Emisor;
        // $valid->validate($data, [
        //     'rfc' => v::noWhitespace()->notEmpty()->RFCValid(),
        //     'nombre' => v::stringType()
        // ]);

        // if($valid->failed()) {

        //     $this->valid = false;
        //     return $this->errors = $valid->errors();
        // }

        $this->dataemisor = $data;
    }

    /**
	* Sets the data Receptor
	* @param array $data
	*/
    public function setReceptor(array $data)
    {
        // $valid = new \lalocespedes\Validation\Receptor;
        // $valid->validate($data, [
        //     'rfc' => v::noWhitespace()->notEmpty()->RFCValid(),
        //     'nombre' => v::stringType()
        // ]);

        // if($valid->failed()) {

        //     $this->valid = false;
        //     return $this->errors = $valid->errors();
        // }
        
        $this->datareceptor = $data;

    }

    /**
	* Sets the data Periodo
	* @param array $data
	*/
    public function setPeriodo(array $data)
    {
        // $valid = new \lalocespedes\Validation\Receptor;
        // $valid->validate($data, [
        //     'rfc' => v::noWhitespace()->notEmpty()->RFCValid(),
        //     'nombre' => v::stringType()
        // ]);

        // if($valid->failed()) {

        //     $this->valid = false;
        //     return $this->errors = $valid->errors();
        // }
        
        $this->dataperiodo = $data;

    }

    /**
	* Sets the data Totales
	* @param array $data
	*/
    public function setTotales(array $data)
    {
        $this->datatotales = $data;
    }

    /**
	* Sets the data ImpRetenidos
	* @param array $data
	*/
    public function setImpRetenidos(array $data)
    {
        $this->dataimpretenidos = $data;
    }

    /**
	* Sets the data Dividendos
	* @param array $data
	*/
    public function setComplemento(array $data)
    {
        $this->datacomplemento = $data;
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

        if(is_null($this->dataretenciones)) {
            $this->errors = [
                "please setRetenciones node"
            ]; 
            $this->valid = false;
            return $this;
        }

        $this->retenciones = new \lalocespedes\Elementos\Retenciones\Retenciones($this->xml, $this->dataretenciones);

        $this->emisor = new \lalocespedes\Elementos\Retenciones\Emisor($this->xml, $this->retenciones, $this->dataemisor);

        $this->receptor = new \lalocespedes\Elementos\Retenciones\Receptor($this->xml, $this->retenciones, $this->datareceptor);

        $this->periodo = new \lalocespedes\Elementos\Retenciones\Periodo($this->xml, $this->retenciones, $this->dataperiodo);

        $this->totales = new \lalocespedes\Elementos\Retenciones\Totales($this->xml, $this->retenciones, $this->datatotales, $this->dataimpretenidos);

        if(!is_null($this->datacomplemento)) {
            $this->complemento = new \lalocespedes\Elementos\Retenciones\Complemento($this->xml, $this->retenciones, $this->datacomplemento);
        }

        //////////////////////////////////////////////////////

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
            
            if (!file_exists($this->cerfile)) {
                $this->errors = [
                    "cer file not found"
                ]; 
                $this->valid = false;
                return $this;
            }

            if (!file_exists($this->keypemfile)) {
                $this->errors = [
                    "key.pem file not found"
                ]; 
                $this->valid = false;
                return $this;
            }

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
        
        $sello = new \lalocespedes\Sello();
        $this->xml = $sello->getSello($this->xml->saveXML(), $this->cerfilecontent, $this->keypemfilecontent, $this->noCertificado);

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
