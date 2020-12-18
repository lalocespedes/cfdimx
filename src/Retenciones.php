<?php

namespace lalocespedes\Cfdimx;

use DOMDocument;

use lalocespedes\Cfdimx\Elementos\Retenciones\Retenciones as Retenc;
use lalocespedes\Cfdimx\Elementos\Retenciones\Emisor;
use lalocespedes\Cfdimx\Elementos\Retenciones\Receptor;
use lalocespedes\Cfdimx\Elementos\Retenciones\Periodo;
use lalocespedes\Cfdimx\Elementos\Retenciones\Totales;
use lalocespedes\Cfdimx\Elementos\Retenciones\Complemento;
use lalocespedes\Cfdimx\Elementos\Retenciones\Complementos\PagosExtranjeros;

use Respect\Validation\Validator as v;

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
    protected $complementopagosextranjeros;

    // Data
    protected $dataretenciones;
    protected $dataemisor;
    protected $datareceptor;
    protected $dataperiodo;
    protected $datatotales;
    protected $dataimpretenidos = [];
    protected $datacomplemento = [];
    protected $datacomplementopagosextranjeros = [];

    public function __construct()
    {
        v::with('lalocespedes\\Cfdimx\\Validation\\Rules\\');
    }

    /**
     * Sets the data Retenciones
     * @param array $data
     */
    public function setRetenciones(array $data = [])
    {
        $valid = new \lalocespedes\Cfdimx\Validation\Retenciones;

        $this->dataretenciones = $data;
    }

    /**
     * Sets the data Emisor
     * @param array $data
     */
    public function setEmisor(array $data)
    {
        // Valid data
        $valid = new \lalocespedes\Cfdimx\Validation\Emisor;
        $valid->validate($data, [
            'rfc' => v::noWhitespace()->notEmpty()->RFCValid(),
            'nombre' => v::stringType()
        ]);

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
        // $valid = new \lalocespedes\Cfdimx\Validation\Receptor;
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
        // $valid = new \lalocespedes\Cfdimx\Validation\Receptor;
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
     * Sets the data Dividendos
     * @param array $data
     */
    public function setComplementoPagosExtranjeros(array $data)
    {
        $this->datacomplementopagosextranjeros = $data;
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
        $this->xml = new DOMdocument("1.0", "UTF-8");

        if (is_null($this->dataretenciones)) {
            $this->errors = [
                "please setRetenciones node"
            ];
            $this->valid = false;
            return $this;
        }

        $this->retenciones = new Retenc($this->xml, $this->dataretenciones, $this->datacomplemento, $this->datacomplementopagosextranjeros);

        $this->emisor = new Emisor($this->xml, $this->retenciones, $this->dataemisor);

        $this->receptor = new Receptor($this->xml, $this->retenciones, $this->datareceptor);

        $this->periodo = new Periodo($this->xml, $this->retenciones, $this->dataperiodo);

        $this->totales = new Totales($this->xml, $this->retenciones, $this->datatotales, $this->dataimpretenidos);

        if (count($this->datacomplemento)) {

            $this->complemento = new Complemento($this->xml, $this->retenciones, $this->datacomplemento);
        }

        if (count($this->datacomplementopagosextranjeros)) {

            $this->complementopagosextranjeros = new PagosExtranjeros($this->xml, $this->retenciones, $this->datacomplementopagosextranjeros);
        }

        //////////////////////////////////////////////////////

        $this->xml->formatOutput = true;

        // sellar xml
        if (is_null($this->cerfile) || is_null($this->keypemfile)) {
            $this->errors = [
                "please set setCertificado"
            ];
            $this->valid = false;
            return $this;
        }
        //Get CSD
        try {
            $csd = new \lalocespedes\Cfdimx\Csd(dirname('/'));
            $this->noCertificado = $csd->getnoCertificado($this->cerfile);
        } catch (\League\Flysystem\FileNotFoundException $e) {
            $this->errors = [
                $e->getMessage()
            ];
            $this->valid = false;
            return $this;
        }

        $sello = new \lalocespedes\Cfdimx\Sello();
        $this->xml = $sello->getSello($this->xml->saveXML(), $this->cerfile, $this->keypemfile, $this->noCertificado);
        return $this;
    }

    public function getXml()
    {
        if ($this->valid) {

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
