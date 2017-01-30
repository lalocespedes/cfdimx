<?php

namespace lalocespedes;

use XSLTProcessor;
use DOMDocument;
use SimpleXMLElement;

/**
 * 
 */
class Sello
{
    protected $xml;
    protected $cerfile;
    protected $keypemfile;

    public function getSello($xml, $cerfile, $keypemfile, $noCertificado)
    {
        $this->xml = $xml;
        $this->cerfile = $cerfile;
        $this->keypemfile = $keypemfile;

        $certificado = str_replace(array('\n', '\r'), '', base64_encode($cerfile));

        $private = openssl_pkey_get_private($keypemfile);
        $xml = new DomDocument;
        $xml->loadXML(utf8_decode($this->xml)) or die("XML invalido");

        $sxe = new SimpleXMLElement($this->xml);
        $namespaces = $sxe->getNamespaces(true);

        $XSL = new DOMDocument;
        $XSL->load( __DIR__ . '/utils/xsltretenciones/retenciones.xslt');

        $proc = new XSLTProcessor;
        $proc->importStyleSheet($XSL);

        $cadena_original = $proc->transformToXML($xml);

        openssl_sign($cadena_original, $sig, $private);

        $sello = base64_encode($sig);

        $c = $xml->getElementsByTagNameNS(array_values($namespaces)[0], ucfirst(array_keys($namespaces)[0]))->item(0);

        if($c->prefix == "retenciones") {

            $c->setAttribute('Sello', $sello);
            $c->setAttribute('Cert', $certificado);
            $c->setAttribute('NumCert', $noCertificado);

        }

        if($c->prefix == "cfdi") {

            $c->setAttribute('sello', $sello);
            $c->setAttribute('certificado', $certificado);
            $c->setAttribute('noCertificado', $noCertificado);

        }

        return $xml->saveXML();
    }
}
