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
    "formaDePago" => "PAGO EN UNA SOLA EXHIBICION",
    "subTotal" => "111",
    "descuento" => "22",
    "total" => "22",
    "tipoDeComprobante" => "INGRESO",
    "metodoDePago" => "EFE",
    "NumCtaPago" => "1234",
    "Moneda" => "MXN",
    "TipoCambio" => "1",
    "LugarExpedicion" => "Matehuala, San Luis Potosi"
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

$invoice['impuestos'] = [
    "totalImpuestosRetenidos" => "1000",
    "totalImpuestosTrasladados" => "500"
];

$invoice['impuestosretenciones'] = [
    "0" => [
        "impuesto" => "IVA",
        "importe" => "16.00"
    ]
];

$invoice['impuestostrasladados'] = [
    "0" => [
        "impuesto" => "IVA",
        "tasa" => "16",
        "importe" => "16.00"
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
$cfdi->setImpuestos($invoice['impuestos']);
$cfdi->setImpuestosRetenciones($invoice['impuestosretenciones']);
$cfdi->setImpuestosTrasladados($invoice['impuestostrasladados']);
$cfdi->setCertificado("/Users/arthaleon/AAA010101AAA/20001000000200001437.cer", "/Users/arthaleon/AAA010101AAA/20001000000200001437.key.pem");

if($cfdi->failed()) {

    return dump(($cfdi->errors()));
}

$xml = $cfdi->build()->getXml();

dump($xml);
