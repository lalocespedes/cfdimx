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

    /**
     * @var DomDocument
     */
    public $xml;

    public $comprobante;
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
    protected $ComercioExterior;
    protected $ComercioExteriorEmisor;
    protected $ComercioExteriorEmisorDomicilio;
    protected $ComercioExteriorReceptor;
    protected $ComercioExteriorReceptorDomicilio;
    protected $ComercioExteriorMercancias;
    protected $ComercioExteriorMercancia;
    protected $OtrosDerechosImpuestos;
    protected $TrasladosLocales;

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

        $this->setAttribute([
            "xmlns:xsi"=>"http://www.w3.org/2001/XMLSchema-instance",
            "xsi:schemaLocation"=>"http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd"
        ], 'comprobante');

        $this->setAttribute($data, 'comprobante');

        $this->comprobante->setAttribute('Certificado', str_replace(array('\n', '\r'), '', base64_encode($this->cerfilecontent)));
        $this->comprobante->setAttribute('Sello', "");
        $this->comprobante->setAttribute('NoCertificado', $this->noCertificado);

    }

    public function setCfdiRelacionados(array $data)
    {
        if(!$data['TipoRelacion']) {
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
                'UUID' => $item
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

                if(array_key_exists('pedimentos', $item) && count($item['pedimentos']) > 0) {

                    foreach ($item['pedimentos'] as $key => $pedimento) {

                        if(!empty($pedimento['NumeroPedimento'])) {
                            $this->conceptoInformacionAduanera = $this->xml->createElement("cfdi:InformacionAduanera");
                            $this->concepto->appendChild($this->conceptoInformacionAduanera);
                            $this->setAttribute($pedimento, 'conceptoInformacionAduanera');

                            $this->conceptoInformacionAduanera->setAttribute('NumeroPedimento', $pedimento);
                        }
                    }
                }

                // Cuenta Predial

                if(array_key_exists('CuentaPredial', $item) && !is_null($item['CuentaPredial']['Numero'])) {

                    $this->conceptoCuentaPredial = $this->xml->createElement("cfdi:CuentaPredial");
                    $this->concepto->appendChild($this->conceptoCuentaPredial);
                    $this->setAttribute($item['CuentaPredial'], 'conceptoCuentaPredial');

                    $this->conceptoCuentaPredial->setAttribute('Numero', $item['CuentaPredial']['Numero']);
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
            "xsi:schemaLocation" => "http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd http://www.sat.gob.mx/Pagos http://www.sat.gob.mx/sitio_internet/cfd/Pagos/Pagos10.xsd"
        ], 'comprobante');

        $this->setAttribute([
            "Version" => "1.0"
        ], 'Pagos');

        foreach ($data as $key => $pago) {

            $this->Pago = $this->xml->createElement("pago10:Pago");
            $this->Pagos->appendChild($this->Pago);

            $this->setAttribute($pago['Attributes'], 'Pago');

            foreach ($pago['doctos_rela'] as $key => $docto_rela) {
                $this->DoctoRelacionado = $this->xml->createElement("pago10:DoctoRelacionado");
                $this->Pago->appendChild($this->DoctoRelacionado);

                $this->setAttribute($docto_rela, 'DoctoRelacionado');
            }
        }
    }

    public function setComplementoComercioExterior(array $data)
    {
        if(!count($data)) { return false; }

        $this->Complemento = $this->xml->createElement("cfdi:Complemento");
        $this->comprobante->appendChild($this->Complemento);

        $this->setAttribute([
            "xmlns:cce11"=>"http://www.sat.gob.mx/ComercioExterior11",
			"xsi:schemaLocation" => "http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd http://www.sat.gob.mx/ComercioExterior11 http://www.sat.gob.mx/sitio_internet/cfd/ComercioExterior11/ComercioExterior11.xsd"
        ], 'comprobante');

        $this->ComercioExterior = $this->xml->createElement("cce11:ComercioExterior");
        $this->Complemento->appendChild($this->ComercioExterior);

        $this->setAttribute($data['header'], 'ComercioExterior');

        // Emisor

        $this->ComercioExteriorEmisor = $this->xml->createElement("cce11:Emisor");
        $this->ComercioExterior->appendChild($this->ComercioExteriorEmisor);
        $this->setAttribute($data['emisor'], 'ComercioExteriorEmisor');

        $this->ComercioExteriorEmisorDomicilio = $this->xml->createElement("cce11:Domicilio");
        $this->ComercioExteriorEmisor->appendChild($this->ComercioExteriorEmisorDomicilio);
        $this->setAttribute($data['emisor']['Domicilio'], 'ComercioExteriorEmisorDomicilio');

        // Receptor

        $this->ComercioExteriorReceptor = $this->xml->createElement("cce11:Receptor");
        $this->ComercioExterior->appendChild($this->ComercioExteriorReceptor);

        $this->setAttribute($data['receptor'], 'ComercioExteriorReceptor');

        $this->ComercioExteriorReceptorDomicilio = $this->xml->createElement("cce11:Domicilio");
        $this->ComercioExteriorReceptor->appendChild($this->ComercioExteriorReceptorDomicilio);

        $this->setAttribute($data['receptor']['Domicilio'], 'ComercioExteriorReceptorDomicilio');

        $this->ComercioExteriorMercancias = $this->xml->createElement("cce11:Mercancias");
        $this->ComercioExterior->appendChild($this->ComercioExteriorMercancias);

        foreach ($data['mercancias'] as $key => $value) {

            $this->ComercioExteriorMercancia = $this->xml->createElement("cce11:Mercancia");
            $this->ComercioExteriorMercancias->appendChild($this->ComercioExteriorMercancia);
            $this->setAttribute($value, 'ComercioExteriorMercancia');
        }
    }

    public function setComplementoOtrosDerechosImpuestos(array $data)
    {
        if(!count($data)) { return false; }

        // todo separar
        // $OtrosDerechosImpuestos = new \lalocespedes\Cfdimx\Complementos\Cfdi\OtrosDerechosImpuestos();

        $this->Complemento = $this->xml->createElement("cfdi:Complemento");
        $this->comprobante->appendChild($this->Complemento);

        $this->OtrosDerechosImpuestos = $this->xml->createElement("implocal:ImpuestosLocales");
        $this->Complemento->appendChild($this->OtrosDerechosImpuestos);

        $this->setAttribute([
            "xsi:schemaLocation" => "http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd http://www.sat.gob.mx/implocal http://www.sat.gob.mx/sitio_internet/cfd/implocal/implocal.xsd",
            "xmlns:implocal" => "http://www.sat.gob.mx/implocal",
            "xmlns:cfdi" => "http://www.sat.gob.mx/cfd/3",
            "xmlns:xsi"=>"http://www.w3.org/2001/XMLSchema-instance"
        ], 'comprobante');

        $this->setAttribute([
            "version" => "1.0",
            "TotaldeRetenciones" => substr($data['ImpuestosLocales']['TotaldeRetenciones'],0,strpos($data['ImpuestosLocales']['TotaldeRetenciones'],".") + 3),
            "TotaldeTraslados" => substr($data['totalImpuestosLocalesTrasladados'],0,strpos($data['totalImpuestosLocalesTrasladados'],".") + 3)
        ], 'OtrosDerechosImpuestos');

        foreach ($data['ImpuestosLocalesTraslados'] as $key => $local) {
            $this->TrasladosLocales = $this->xml->createElement("implocal:TrasladosLocales");
            $this->OtrosDerechosImpuestos->appendChild($this->TrasladosLocales);
            $this->setAttribute($local, 'TrasladosLocales');
        }

    }

    public function setCer($cer, $key)
    {
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

            // for ($i=0;$i<strlen($val); $i++) {
            //     $a = substr($val,$i,1);
            //     if ($a > chr(127) && $a !== chr(219) && $a !== chr(211) && $a !== chr(209)) {
            //         $val = substr_replace($val, ".", $i, 1);
            //     }
            // }

            // $val = preg_replace('/\s+/', ' ', $val); // Regla 5a y 5c
            // // $val = preg_replace('/\s\s+/', ' ', $val);   // Regla 5a y 5c
            $val = trim($val); // Regla 5b
            if (strlen($val)>0) { // Regla 6
                $val = str_replace(array('"','>','<'),"'",$val);  // &...;
                $val = str_replace("|","/",$val); // Regla 1
                // $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
                $this->{$node}->setAttribute($key,$val);
            }
        }
    }

    public function getCadenaOriginal()
    {
        $new = new \DOMDocument("1.0","UTF-8");
        $new->loadXML($this->xml->saveXml());

        $xsl = new DOMDocument();
        $xsl->load(__DIR__ . '/../utils/xslt/cadenaoriginal_3_3.xslt');
        $proc = new XSLTProcessor;
        $proc->importStyleSheet($xsl);


        $cadena_original = $proc->transformToXML($new);
        $cadena_original = str_replace(array("\r", "\n"), '', $cadena_original);

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
