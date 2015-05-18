<?php

namespace cfdiMx;

use nusoap_client;
/**
* 
*/
class Sat
{

    function valida_cfdi($impo, $rfc_emisor, $rfc_receptor, $uuid) {

    $url = "https://consultaqr.facturaelectronica.sat.gob.mx/consultacfdiservice.svc?wsdl";

    $this->nusoap_client = new nusoap_client($url,$esWSDL=true);
    $this->nusoap_client->soap_defencoding = 'UTF-8';
    $this->nusoap_client->decode_utf8 = false;

    $impo = (double)$impo;
    $impo =sprintf("%.6f", $impo);
    $impo = str_pad($impo,17,"0",STR_PAD_LEFT);

    $uuid = strtoupper($uuid);

    $factura = "?re=$rfc_emisor&rr=$rfc_receptor&tt=$impo&id=$uuid";

    $prm = array('expresionImpresa'=>$factura);

    $buscar = $this->nusoap_client->call('Consulta',$prm);

    $result = substr($buscar['ConsultaResult']['CodigoEstatus'], 0, 1);

    return $result;

    }
}