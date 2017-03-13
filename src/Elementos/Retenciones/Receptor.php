<?php

namespace lalocespedes\Cfdimx\Elementos\Retenciones;

/**
 * 
 */
class Receptor
{
    protected $receptor;

    function __construct($xml, $retenciones, array $data)
    {
        $this->receptor = $xml->createElement("retenciones:Receptor");

        $retenciones->appendChild($this->receptor);

        $this->setAttribute([
            "Nacionalidad" => $data['Nacionalidad']
        ], "receptor");

        // Add nodo Nacionalidad

        $this->$data['Nacionalidad'] = $xml->createElement("retenciones:".$data['Nacionalidad']);
        $this->receptor->appendChild($this->$data['Nacionalidad']);

        $this->setAttribute([
            "CURPR" => (in_array('CURPR', $data)) ? $data['CURPR'] : '',
            "NomDenRazSocR" => $data['NomDenRazSocR'],
            "RFCRecep" => $data['RFCRecep'],
            "CURPR" => $data['CURPR'],
        ], $data['Nacionalidad']);

    }

    public function appendChild($value)
    {
        $this->receptor->appendChild($value);
    }

    private function setAttribute($data, $nodo)
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
