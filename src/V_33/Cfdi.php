<?php

namespace lalocespedes\Cfdimx\V_33;

use DOMDocument;
use XSLTProcessor;
use Exception;

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

    public $xml;
    
    protected $comprobante;
    protected $emisor;
    protected $receptor;
    protected $conceptos;
    protected $concepto;
    protected $impuestos;
    protected $impuestostraslados;
    protected $traslado;
    protected $impuestosretenciones;
    protected $retencion;

    protected $cerfile;
    protected $certificado;
    protected $noCertificado;

    function __construct()
    {
        $this->xml = new DOMdocument("1.0","UTF-8");
        $this->xml->formatOutput = true;
    }

    public function setComprobante(array $data)
    {
        // valid data

        $this->comprobante = $this->xml->appendChild(
            $this->xml->createElementNS("http://www.sat.gob.mx/cfd/3","cfdi:Comprobante")
        );

        $this->setAttribute($data, 'comprobante');

        $this->comprobante->setAttribute('Certificado', str_replace(array('\n', '\r'), '', base64_encode($this->cerfilecontent)));
        $this->comprobante->setAttribute('Sello', "");
        $this->comprobante->setAttribute('NoCertificado', $this->noCertificado);

    }

    public function setEmisor(array $data)
    {
        // valid data

        $this->emisor = $this->xml->createElement("cfdi:Emisor");
        $this->comprobante->appendChild($this->emisor);

        $this->setAttribute($data, 'emisor');
    }

    public function setReceptor(array $data)
    {
        // valid data

        $this->receptor = $this->xml->createElement("cfdi:Receptor");
        $this->comprobante->appendChild($this->receptor);

        $this->setAttribute($data, 'receptor');
    }

    public function setConceptos(array $data)
    {
        // valid data

        $this->conceptos = $this->xml->createElement("cfdi:Conceptos");
        $this->comprobante->appendChild($this->conceptos);

        foreach ($data as $key => $item) {

            $this->concepto = $this->xml->createElement("cfdi:Concepto");
            $this->conceptos->appendChild($this->concepto);

            $this->setAttribute($item, 'concepto');

        }
    }

    public function setImpuestos(array $data)
    {
        // valid data

        $this->impuestos = $this->xml->createElement("cfdi:Impuestos");
        $this->comprobante->appendChild($this->impuestos);

        $this->setAttribute($data, 'impuestos');
        
    }

    public function setImpuestosRetenciones(array $data)
    {
        // valid data

        $this->impuestosretenciones = $this->xml->createElement("cfdi:Retenciones");
        $this->impuestos->appendChild($this->impuestosretenciones);

        foreach ($data as $key => $value) {
            
            $this->retencion = $this->xml->createElement("cfdi:Retencion");
            $this->impuestosretenciones->appendChild($this->retencion);

            $this->setAttribute($value, 'retencion');
        }

    }

    public function setImpuestosTraslados(array $data)
    {
        // valid data

        $this->impuestostraslados = $this->xml->createElement("cfdi:Traslados");
        $this->impuestos->appendChild($this->impuestostraslados);

        foreach ($data as $key => $value) {
            
            $this->traslado = $this->xml->createElement("cfdi:Traslado");
            $this->impuestostraslados->appendChild($this->traslado);

            $this->setAttribute($value, 'traslado');
        }

    }
    
    public function setCer($cer, $key)
    {
        $this->cerfile = $cer;
        $this->keypemfile = $key;

        $csd = new \lalocespedes\Cfdimx\Csd(dirname($this->cerfile));
        $this->noCertificado = $csd->getnoCertificado($this->cerfile);
        $this->cerfilecontent = $csd->getCer(basename($this->cerfile));
        $this->keypemfilecontent = $csd->getKeyPem(basename($this->keypemfile));
    }

    public function getXML()
    {
        if($this->valid) {
            
            $this->Sellar();

            return $this->xml->saveXml();
        }

        $this->xml = null;
        return $this->errors();
    }

    public function saveXml()
    {
        return $this->xml->save('response.xml');
    }

    public function failed()
    {
        return !empty($this->errors);
    }
    
    public function errors()
    {
        return $this->errors;
    }

    private function setAttribute(array $data, $node)
    {
        foreach ($data as $key => $val) {
		    $val = preg_replace('/\s+/', ' ', $val); // Regla 5a y 5c
		    $val = trim($val); // Regla 5b
		    if (strlen($val)>0) { // Regla 6
		        $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
		        $this->{$node}->setAttribute($key,$val);
		    }
		}

    }

    private function getCadenaOriginal()
    {
        $xsl = new DOMDocument("1.0","UTF-8");
        $xsl->load(__DIR__ . '/../utils/xslt/cadenaoriginal_3_3.xslt');
        $proc = new XSLTProcessor;
        $proc->importStyleSheet($xsl);
        $new = new \DOMDocument("1.0","UTF-8");
        $new->loadXML($this->xml->saveXml());
        
        return $proc->transformToXML($new);
    }
    
    private function Sellar()
    {
        $private = openssl_get_privatekey(file_get_contents($this->keypemfile));
        openssl_sign($this->getCadenaOriginal(), $sig, $private, OPENSSL_ALGO_SHA256);
        openssl_free_key($private);

        $sello = base64_encode($sig);

        $this->comprobante->setAttribute('Sello', $sello);
    }

}
