<?php

namespace lalocespedes\Cfdimx\Elementos\Cfdi;

/**
 * 
 */
class Complemento
{
    protected $complemento;
    protected $comercioExterior;

    function __construct($xml, $comprobante, $data)
    {
        // Create Element retenciones:Complemento
        $this->complemento = $xml->createElement("cfdi:Complemento");
        $comprobante->appendChild($this->complemento);

        if(array_key_exists("ComercioExterior", $data)) {

            $comprobante->setAttributeNS(
                'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation',
                'http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd http://www.sat.gob.mx/ComercioExterior http://www.sat.gob.mx/sitio_internet/cfd/ComercioExterior/ComercioExterior10.xsd');

             $comprobante->setAttributeNS(
                'http://www.w3.org/2000/xmlns/',
                'xmlns:cce',
                'http://www.sat.gob.mx/ComercioExterior'
            );

            $comercioext = $xml->createElement('cce:ComercioExterior');
            $comercioext = $this->complemento->appendChild($comercioext);

            $this->setAttr($comercioext, $data['ComercioExterior']['datos']);

            $comercioextreceptor = $xml->createElement('cce:Receptor');

            $comercioextreceptor = $comercioext->appendChild($comercioextreceptor);

            $this->setAttr($comercioextreceptor, [
                "NumRegIdTrib" => $data['ComercioExterior']['Receptor']['taxIdReceptor']
            ]);

            $comercioextdestinatario = $xml->createElement('cce:Destinatario');
            $comercioextdestinatario = $comercioext->appendChild($comercioextdestinatario);

            $this->setAttr($comercioextdestinatario, [
                "Curp" => "",
                "NumRegIdTrib" => $data['ComercioExterior']['Receptor']['taxIdReceptor']
            ]);

            $comercioextdestinatariodomicilio = $xml->createElement('cce:Domicilio');
            $comercioextdestinatariodomicilio = $comercioextdestinatario->appendChild($comercioextdestinatariodomicilio);

            $this->setAttr($comercioextdestinatariodomicilio, [
                "Calle" => $data['ComercioExterior']['Receptor']['calle'],
                "Estado" => $data['ComercioExterior']['Receptor']['estado'],
                "Pais" => $data['ComercioExterior']['Receptor']['pais'],
                "CodigoPostal" => $data['ComercioExterior']['Receptor']['CodigoPostal']
            ]);

            $comercioextmercancias = $xml->createElement('cce:Mercancias');
            $comercioextmercancias = $comercioext->appendChild($comercioextmercancias);

            foreach($data['ComercioExterior']['Mercancias'] as $item) {

                $comercioextmercancia = $xml->createElement('cce:Mercancia');
                $comercioextmercancia = $comercioextmercancias->appendChild($comercioextmercancia);

                $this->setAttr($comercioextmercancia, [
                    "NoIdentificacion" =>  $item['NoIdentificacion'],
                    "FraccionArancelaria" => $item['FraccionArancelaria'],
                    "ValorDolares" => number_format((float)$item['ValorDolares'], 2, '.',''),
                    "CantidadAduana" => number_format((float)$item['CantidadAduana'], 3, '.',''),
                    "UnidadAduana" => $item['UnidadAduana'],
                    "ValorUnitarioAduana" => number_format((float)$item['ValorUnitarioAduana'], 2, '.','')
                ]);

            }

        }
    }

    public function appendChild($value)
    {
        $this->complemento->appendChild($value);
    }

    function setAttr($element ,array $data) {

        foreach ($data as $key => $val) {
            $val = preg_replace('/\s+/', ' ', $val); // Regla 5a y 5c
            $val = trim($val); // Regla 5b
            if (strlen($val)>0) { // Regla 6
                $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
                $element->setAttribute($key,$val);
            }
        }
    }
}
