<?php

namespace lalocespedes;

use XSLTProcessor;
use DOMDocument;

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

        $XSL = new DOMDocument;
        $XSL->load( __DIR__ . '/utils/xslt32/cadenaoriginal_3_2.xslt');

        $proc = new XSLTProcessor;
        $proc->importStyleSheet($XSL);

        $cadena_original = $proc->transformToXML($xml);

        openssl_sign($cadena_original, $sig, $private);

        $sello = base64_encode($sig);

        $c = $xml->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/3', 'Comprobante')->item(0);
        $c->setAttribute('sello', $sello);
        $c->setAttribute('certificado', $certificado);
        $c->setAttribute('noCertificado', $noCertificado);

        return $xml->saveXML();
    }
}
