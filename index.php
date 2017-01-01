<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

require 'vendor/autoload.php';

$invoice = [];

$invoice['comprobante'] = [
    "version" => "3.2",
    "serie" => "B",
    "folio" => "",
    "fecha" => "",
    "formaDePago" => "PAGOENUNASO LAEXHIBICION",
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

$cfdi = new lalocespedes\Cfdi;

$cfdi->setData($invoice);

if($cfdi->failed()) {

    return dump(($cfdi->errors()));
}

dump($cfdi->build()->getXml());
