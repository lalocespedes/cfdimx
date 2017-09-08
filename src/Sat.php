<?php

namespace lalocespedes\CfdiMx;

use nusoap_client;
use Exception;

/**
* 
*/
class Sat
{
    protected $qr_sat;

    static function valida_cfdi($impo, $rfc_emisor, $rfc_receptor, $uuid)
    {
        $url = "https://consultaqr.facturaelectronica.sat.gob.mx/consultacfdiservice.svc?wsdl";

        $soap = new nusoap_client($url,$esWSDL=true);
        $soap->soap_defencoding = 'UTF-8';
        $soap->decode_utf8 = false;

        $impo = (double)$impo;
        $impo =sprintf("%.6f", $impo);
        $impo = str_pad($impo,17,"0",STR_PAD_LEFT);

        $uuid = strtoupper($uuid);

        $factura = "?re=$rfc_emisor&rr=$rfc_receptor&tt=$impo&id=$uuid";

        $buscar = $soap->call('Consulta',[
            'expresionImpresa'=>$factura
        ]);

        return substr($buscar['ConsultaResult']['Estado'], 0, 1);

    }

    public static function qr_sat($impo, $rfc_emisor, $rfc_receptor, $uuid)
    {
        $impo = (double)$impo;
        $impo =sprintf("%.6f", $impo);
        $impo = str_pad($impo,17,"0",STR_PAD_LEFT);

        $uuid = strtoupper($uuid);

        return "?re=$rfc_emisor&rr=$rfc_receptor&tt=$impo&id=$uuid";
    }
}
