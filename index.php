<?php

require 'vendor/autoload.php';

$invoice = [];

$invoice['comprobante'] = [
    "version" => "3.2",
    "serie" => "B",
    "folio" => "",
    "fecha" => "",
    "formaDePago" => "PAGO ENUNASOLAEXHIBICION",
    "subTotal" => "",
    "descuento" => "",
    "total" => "",
    "tipoDeComprobante" => "INGRESO",
    "metodoDePago" => "",
    "NumCtaPago" => "",
    "Moneda" => "",
    "TipoCambio" => "",
    "LugarExpedicion" => "Matehuala, San Luis Potosi"
];


$cfdi = new lalocespedes\Cfdi($invoice);

dump($cfdi->build()->getXml());
