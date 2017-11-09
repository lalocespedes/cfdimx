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
    protected $CfdiRelacionados;
    protected $CfdiRelacionado;
    protected $conceptoimpuestos;
    protected $conceptoimpuestosTraslados;
    protected $conceptoimpuestosTraslado;
    protected $conceptoimpuestosretenciones;
    protected $conceptoimpuestosRetencion;
    protected $impuestos;
    protected $impuestostraslados;
    protected $traslado;
    protected $impuestosretenciones;
    protected $retencion;
    protected $Complemento;
    protected $Pagos;
    protected $Pago;

    protected $cerfile;
    protected $certificado;
    protected $noCertificado;
    protected $cerfilecontent;
    protected $keypemfilecontent;

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

    public function setCfdiRelacionados(array $data)
    {
        if(!count($data)) {
            return false;
        }

        $this->CfdiRelacionados = $this->xml->createElement("cfdi:CfdiRelacionados");
        $this->comprobante->appendChild($this->CfdiRelacionados);

        $this->setAttribute([
            'TipoRelacion' => $data['TipoRelacion']
        ], 'CfdiRelacionados');

        foreach ($data['UUIDS'] as $key => $item) {

            $this->CfdiRelacionado = $this->xml->createElement("cfdi:CfdiRelacionado");
            $this->CfdiRelacionados->appendChild($this->CfdiRelacionado);

            $this->setAttribute([
                'UUID' => $item['UUID']
            ], 'CfdiRelacionado');

        }

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
        $this->conceptos = $this->xml->createElement("cfdi:Conceptos");
        $this->comprobante->appendChild($this->conceptos);

        foreach ($data as $key => $item) {

            $this->concepto = $this->xml->createElement("cfdi:Concepto");
            $this->conceptos->appendChild($this->concepto);

            // Atributos
            $this->setAttribute($item['Attributes'], 'concepto');

            if(count($item['Impuestos']['Traslados']) || count($item['Impuestos']['Retenciones'])) {

                // Impuestos
                if(count($item['Impuestos'])) {

                    $this->conceptoimpuestos = $this->xml->createElement("cfdi:Impuestos");
                    $this->concepto->appendChild($this->conceptoimpuestos);
                }

                // Impuestos Traslados

                if(array_key_exists('Traslados', $item['Impuestos']) && count($item['Impuestos']['Traslados'])) {

                    $this->conceptoimpuestosTraslados = $this->xml->createElement("cfdi:Traslados");
                    $this->conceptoimpuestos->appendChild($this->conceptoimpuestosTraslados);

                    foreach($item['Impuestos']['Traslados'] as $key_imp => $tax) {
                        $this->conceptoimpuestosTraslado = $this->xml->createElement("cfdi:Traslado");
                        $this->conceptoimpuestosTraslados->appendChild($this->conceptoimpuestosTraslado);
                        $this->setAttribute($tax, 'conceptoimpuestosTraslado');
                    }
                }

                // Impuestos Retencion

                if(array_key_exists('Retenciones', $item['Impuestos']) && count($item['Impuestos']['Retenciones'])) {

                    $this->conceptoimpuestosretenciones = $this->xml->createElement("cfdi:Retenciones");
                    $this->conceptoimpuestos->appendChild($this->conceptoimpuestosretenciones);

                    foreach($item['Impuestos']['Retenciones'] as $key_imp => $tax) {
                        $this->conceptoimpuestosRetencion = $this->xml->createElement("cfdi:Retencion");
                        $this->conceptoimpuestosretenciones->appendChild($this->conceptoimpuestosRetencion);
                        $this->setAttribute($tax, 'conceptoimpuestosRetencion');
                    }
                }

                // Informacion aduanera

                if(array_key_exists('InformacionAduanera', $item) && trim($item['InformacionAduanera']['NumeroPedimento'] !== '')) {

                    $this->conceptoInformacionAduanera = $this->xml->createElement("cfdi:InformacionAduanera");
                    $this->concepto->appendChild($this->conceptoInformacionAduanera);
                    $this->setAttribute($item['InformacionAduanera'], 'conceptoInformacionAduanera');

                    $this->conceptoInformacionAduanera->setAttribute('NumeroPedimento',$item['InformacionAduanera']['NumeroPedimento']);
                }
            }
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

        if(!count($data)) {
            return false;
        }

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
        if(!count($data)) {
            return false;
        }

        $this->impuestostraslados = $this->xml->createElement("cfdi:Traslados");
        $this->impuestos->appendChild($this->impuestostraslados);

        foreach ($data as $key => $value) {

            $this->traslado = $this->xml->createElement("cfdi:Traslado");
            $this->impuestostraslados->appendChild($this->traslado);

            $this->setAttribute($value, 'traslado');
        }
    }

    public function setComplementoPagos(array $data)
    {
        if(!count($data)) { return false; }

        $this->Complemento = $this->xml->createElement("cfdi:Complemento");
        $this->comprobante->appendChild($this->Complemento);

        $this->Pagos = $this->xml->createElement("pago10:Pagos");
        $this->Complemento->appendChild($this->Pagos);

        $this->setAttribute([
            "xmlns:xsi"=>"http://www.w3.org/2001/XMLSchema-instance",
            "xmlns:pago10" => "http://www.sat.gob.mx/Pagos",
            "Version" => "1.0",
            "xsi:schemaLocation" => "http://www.sat.gob.mx/Pagos http://www.sat.gob.mx/sitio_internet/cfd/Pagos/Pagos.xsd"
        ], 'Pagos');

        foreach ($data as $key => $pago) {

            $this->Pago = $this->xml->createElement("pago10:Pago");
            $this->Pagos->appendChild($this->Pago);

            $this->setAttribute($pago, 'Pago');

            foreach ($pago['doctos_rela'] as $key => $docto_rela) {
                $this->DoctoRelacionado = $this->xml->createElement("pago10:DoctoRelacionado");
                $this->Pago->appendChild($this->DoctoRelacionado);

                $this->setAttribute($docto_rela, 'DoctoRelacionado');
            }
        }
    }

    public function setCer($cer, $key)
    {
        // $this->cerfile = $cer;
        // $this->keypemfile = $key;

        // $csd = new \lalocespedes\Cfdimx\Csd(dirname($this->cerfile));
        // $this->noCertificado = $csd->getnoCertificado($this->cerfile);
        $this->noCertificado = \lalocespedes\Cfdimx\Csd::getnoCertificado($cer);
        $this->cerfilecontent = $cer;
        $this->keypemfilecontent = $key;
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

            for ($i=0;$i<strlen($val); $i++) {
                $a = substr($val,$i,1);
                if ($a > chr(127) && $a !== chr(219) && $a !== chr(211) && $a !== chr(209)) {
                    $val = substr_replace($val, ".", $i, 1);
                }
            }

		    $val = preg_replace('/\s+/', ' ', $val); // Regla 5a y 5c
		    $val = trim($val); // Regla 5b
            if (strlen($val)>0) { // Regla 6
                $val = str_replace(array('"','>','<'),"'",$val);  // &...;
		        // $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
		        $this->{$node}->setAttribute($key,$val);
		    }
		}

    }

    public function getCadenaOriginal()
    {
        $xsl = new DOMDocument("1.0","UTF-8");
        $xsl->load(__DIR__ . '/../utils/xslt/cadenaoriginal_3_3.xslt');
        $proc = new XSLTProcessor;
        $proc->importStyleSheet($xsl);
        $new = new \DOMDocument("1.0","UTF-8");
        $new->loadXML($this->xml->saveXml());

        $cadena_original = $proc->transformToXML($new);

        return $cadena_original;
    }

    private function Sellar()
    {
        $cer64 = str_replace(array('\n', '\r'), '', base64_encode($this->cerfilecontent));
        $this->comprobante->setAttribute('Certificado', $cer64);
        $this->comprobante->setAttribute('NoCertificado', $this->noCertificado);

        $private = openssl_get_privatekey($this->keypemfilecontent);
        openssl_sign($this->getCadenaOriginal(), $sig, $private, OPENSSL_ALGO_SHA256);
        openssl_free_key($private);

        $sello64 = base64_encode($sig);

        $this->comprobante->setAttribute('Sello', $sello64);
    }

}
