<?php

namespace lalocespedes\Cfdimx\Complementos\Cfdi;

use lalocespedes\Cfdimx\V_33\Cfdi;

/**
 *
 */
class OtrosDerechosImpuestos extends Cfdi
{
    public $xml;
    protected $complemento;
    protected $otrosDerechosImpuestos;

    function __construct()
    {

        return $this->xml;

        $this->complemento = $this->xml->createElement("cfdi:Complemento");
        $comprobante->appendChild($this->complemento);

        $this->otrosDerechosImpuestos = $this->complemento->createElement("implocal:implocal");

        // foreach ($data as $key => $val) {
		//     $val = preg_replace('/\s+/', ' ', $val); // Regla 5a y 5c
		//     $val = trim($val); // Regla 5b
		//     if (strlen($val)>0) { // Regla 6
		//         $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
		//         $this->impuestos->setAttribute($key,$val);
		//     }
		// }
    }

    // public function appendChild($value)
    // {
    //     $this->impuestos->appendChild($value);
    // }
}
