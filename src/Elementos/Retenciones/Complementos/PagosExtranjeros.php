<?php

namespace lalocespedes\Cfdimx\Elementos\Retenciones\Complementos;

/**
 * 
 */
class PagosExtranjeros
{
    protected $complemento;
    protected $pagosaextranjeros;
    protected $beneficiario;
    protected $nobeneficiario;

    function __construct($xml, $retenciones, $data)
    {
        // Create Element retenciones:Complemento
        $complemento = $xml->createElement("retenciones:Complemento");
        $retenciones->appendChild($complemento);

        $pagosaextranjeros = $xml->createElement("pagosaextranjeros:Pagosaextranjeros");
        $this->pagosaextranjeros = $complemento->appendChild($pagosaextranjeros);

        // Set Attibutes
        $this->setAttribute([
            'Version' => $data['Pagosaextranjeros']['Version'],
            'EsBenefEfectDelCobro' => $data['Pagosaextranjeros']['EsBenefEfectDelCobro']
        ],"pagosaextranjeros");

        $datosBeneficiario = [];
        $datosBeneficiario['ConceptoPago'] = $data['Pagosaextranjeros']['ConceptoPago'];
        $datosBeneficiario['DescripcionConcepto'] = $data['Pagosaextranjeros']['DescripcionConcepto'];

        // if is Beneficiario
        if($data['Pagosaextranjeros']['EsBenefEfectDelCobro'] === "SI") {

            $beneficiario = $xml->createElement("pagosaextranjeros:Beneficiario");
            $this->beneficiario = $pagosaextranjeros->appendChild($beneficiario);

            $datosBeneficiario['RFC'] = $data['Pagosaextranjeros']['RFC'];
            $datosBeneficiario['NomDenRazSocB'] = $data['Pagosaextranjeros']['NomDenRazSocB'];
            $datosBeneficiario['CURP'] = $data['Pagosaextranjeros']['CURP'];

            $this->setAttribute($datosBeneficiario, "beneficiario");
        }

        if($data['Pagosaextranjeros']['EsBenefEfectDelCobro'] === "NO") {

            $Nobeneficiario = $xml->createElement("pagosaextranjeros:NoBeneficiario");
            $this->nobeneficiario = $pagosaextranjeros->appendChild($Nobeneficiario);

            $datosBeneficiario['PaisDeResidParaEfecFisc'] = $data['Pagosaextranjeros']['PaisDeResidParaEfecFisc'];

            $this->setAttribute($datosBeneficiario, "nobeneficiario");
        }
    }

    public function appendChild($value)
    {
        $this->complemento->appendChild($value);
    }
    
    private function setAttribute(array $data, $nodo)
    {
        foreach ($data as $key => $val) {
		    $val = preg_replace('/\s\s+/', ' ', $val); // Regla 5a y 5c
		    $val = trim($val); // Regla 5b
		    if (strlen($val)>0) { // Regla 6
		        $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
                $this->{$nodo}->setAttribute($key,$val);
		    }
        }
    }
}
