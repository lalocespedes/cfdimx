<?php

namespace lalocespedes\Cfdimx;

use DOMDocument;
use \XSLTProcessor;

class CadenaOriginal {

  public static function getCadenaOriginal(DOMDocument $xml) {

    $xsl = new DOMDocument();
    $xsl->load(__DIR__ . '/utils/xslt/cadenaoriginal_3_3.xslt');

    $proc = new XSLTProcessor;
    $proc->importStyleSheet($xsl);

    $new = new \DOMDocument();
    $new->loadXML($xml->saveXML());

    $cadena_original = $proc->transformToXML($new);
    return $cadena_original;
  }
}
