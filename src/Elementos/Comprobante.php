<?php

namespace lalocespedes\Elementos;

/**
 * 
 */
class Comprobante
{
    protected $comprobante;

    function __construct($xml)
    {
       $this->comprobante = $xml->appendChild(
            $xml->createElementNS("http://www.sat.gob.mx/cfd/3","cfdi:Comprobante")
        );

        $this->comprobante->setAttributeNS(
            'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation',
            'http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd'
        );

        $attr = [
            "version" =>"3.2",
            "serie" => "B"
//         // "folio" => $gen['folio'],
//         // "fecha" => $gen['fecha_factura'],
//         // "formaDePago" => $gen['formaDePago'] ? : "PAGO EN UNA SOLA EXHIBICION",
//         // "subTotal" => $gen['subTotal'],
//         // "descuento" => $gen['descuento'],
//         // "total" => $gen['total'],
//         // "tipoDeComprobante" => $gen['tipoDeComprobante'],
//         // "metodoDePago" => $gen['metodoDePago'],
//         // "NumCtaPago" => $gen['NumCtaPago'],
//         // "Moneda" => $gen['Moneda'],
//         // "TipoCambio" => $gen['TipoCambio'],
//         // "LugarExpedicion" => $gen['LugarExpedicion'] ?: "Matehuala, San Luis Potosi"
        ];

        foreach ($attr as $key => $val) {
		    $val = preg_replace('/\s\s+/', ' ', $val); // Regla 5a y 5c
		    $val = trim($val); // Regla 5b
		    if (strlen($val)>0) { // Regla 6
		        $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
		        $this->comprobante->setAttribute($key,$val);
		    }
		}
    }

    public function appendChild($value)
    {
        $this->comprobante->appendChild($value);
    }
}
