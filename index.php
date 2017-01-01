<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

require 'vendor/autoload.php';

$invoice = [];

$invoice['comprobante'] = [
    "version" => "3.2",
    "serie" => "B",
    "folio" => "",
    "fecha" => "111",
    "formaDePago" => "PAGOENUNASOLAE XHIBICION",
    "subTotal" => "111",
    "descuento" => "22",
    "total" => "22",
    "tipoDeComprobante" => "22 ",
    "metodoDePago" => "22",
    "NumCtaPago" => "",
    "Moneda" => "",
    "TipoCambio" => "",
    "LugarExpedicion" => "Matehuala, San Luis Potosi",
    "lalo" => "cespedes"
];

$cfdi = new lalocespedes\Cfdi;

$cfdi->setComprobante($invoice['comprobante']);

if($cfdi->failed()) {

    return dump(($cfdi->errors()));
}

dump($cfdi->build()->getXml());
