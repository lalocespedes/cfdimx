<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

require 'vendor/autoload.php';

$invoice = [];

$invoice['comprobante'] = [
    "version" => "3.2",
    "serie" => "B",
    "folio" => "10599",
    "fecha" => "111",
    "formaDePago" => "PAGOENUNASOLAE XHIBICION",
    "subTotal" => "111",
    "descuento" => "22",
    "total" => "22",
    "tipoDeComprobante" => "INGRESO",
    "metodoDePago" => "EFE",
    "NumCtaPago" => "1234",
    "Moneda" => "MXN",
    "TipoCambio" => "1",
    "LugarExpedicion" => "Matehuala, San Luis Potosi",
    "sello" => "1111",
    "noCertificado" => "2222",
    "certificado" => "333"
];

$invoice['emisor'] = [
    "rfc" => "CANN780217",
    "nombre" => "aassdfff"
];

$invoice['emisordomiciliofiscal'] = [
    "calle" => "calle uno",
    "municipio" => "mate",
    "estado" => "slp",
    "pais" => "mex",
    "codigoPostal" => "78700"
];

$invoice['regimenfiscal'] = [
    "Regimen" => "REGIMEN GENERAL"
];

$invoice['receptor'] = [
    "rfc" => "RNP721026",
    "nombre" => "REFA"
];

$invoice['receptordomicilio'] = [
    "calle" => "calle receptor",
    "municipio" => "mate receptor",
    "estado" => "slp receptor",
    "pais" => "mex receptor",
    "codigoPostal" => "78700"
];

$invoice['conceptos'] = [
	"0" => [
        "noIdentificacion" => "222",
        "cantidad" => "100",
        "unidad" => "PZA",
        "descripcion" => "DESC",
        "valorUnitario" => number_format("1000", 2, '.',''),
        "importe" => number_format("1100", 2, '.','')
    ],
    "1" => [
        "noIdentificacion" => "111",
        "cantidad" => "50",
        "unidad" => "PZA",
        "descripcion" => "DESC2",
        "valorUnitario" => number_format("10002", 2, '.',''),
        "importe" => number_format("11002", 2, '.','')
    ]
];

$cfdi = new lalocespedes\Cfdi;

$cfdi->setComprobante($invoice['comprobante']);
$cfdi->setEmisor($invoice['emisor']);
$cfdi->setEmisorDomicilioFiscal($invoice['emisordomiciliofiscal']);
$cfdi->setRegimenFiscal($invoice['regimenfiscal']);
$cfdi->setReceptor($invoice['receptor']);
$cfdi->setReceptorDomicilio($invoice['receptordomicilio']);
$cfdi->setConceptos($invoice['conceptos']);

if($cfdi->failed()) {

    return dump(($cfdi->errors()));
}

dump($cfdi->build()->getXml());
