<?php

namespace lalocespedes\Cfdimx\V_33;

use DOMDocument;

class NodeComprobante
{

  private $xml;

  function __construct(DOMDocument $xml)
  {
    $this->xml = $xml;
  }

  public function setNodeComprobante(array $data)
  {
    // valid data

    $nodeComprobante = $this->xml->appendChild(
      $this->xml->createElementNS("http://www.sat.gob.mx/cfd/3", "cfdi:Comprobante")
    );

    $nodeComprobante->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
    $nodeComprobante->setAttribute("xsi:schemaLocation", "http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd");

    foreach ($data as $key => $val) {
      $val = trim($val); // Regla 5b
      if (strlen($val) > 0) { // Regla 6
        $val = str_replace(array('"', '>', '<'), "'", $val);  // &...;
        $val = str_replace("|", "/", $val); // Regla 1
        $nodeComprobante->setAttribute($key, $val);
      }
    }

    return $nodeComprobante;
  }
}
