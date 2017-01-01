<?php

require 'vendor/autoload.php';

// $cfdi = new lalocespedes\Elementos\Comprobante;

$cfdi = new lalocespedes\Cfdi;

dump($cfdi->build()->getXml());
