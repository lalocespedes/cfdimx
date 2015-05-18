<?php

namespace cfdiMx;

use Exception;
use SimpleXMLElement;
use DomDocument;

/**
* 
*/
class Validator
{
	
	public $_xml;

	/**
    * Construct
    * @param string $file Path to the xml file
    */
    final public function __construct($file = null)
    {
        if (!is_file($file)) throw new Exception('Error: no file found');

        $xdoc = new DomDocument();
		$xdoc->load(utf8_decode($file)) or die("XML invalido");

        $xml = new SimpleXMLElement(file_get_contents($file));

       	$this->_xml = $xml;

    }

    public function init()
    {
    	$xml = $this->_xml;

    	$error = array();

        $namespace = $xml->getNamespaces(true);

    	$cfdi = $xml->children($namespace['cfdi']);

        // las doble diagonales es para determinar errores graves e informativos

    	if(empty($this->_xml['fecha'])) $error[] = 'Falta fecha'; // con cierto formato
        if(empty($this->_xml['LugarExpedicion'])) $error[] = 'Falta Lugar de Expedicion';
        if(empty($this->_xml['certificado'])) $error[] = 'Falta Certificado';
        if(empty($this->_xml['noCertificado'])) $error[] = 'Falta Numero de Certificado';
        if(empty($this->_xml['formaDePago'])) $error[] = 'Falta Forma de Pago';
        if(empty($this->_xml['metodoDePago'])) $error[] = 'Falta Metodo de Pago';
        if(empty($this->_xml['tipoDeComprobante'])) $error[] = 'Falta Tipo de Comprobante';
        if(empty($this->_xml['sello'])) $error[] = 'Falta sello'; //validar que el sello sea correcto
        if(empty($this->_xml['subTotal'])) $error[] = 'Falta subtotal';
        if(empty($this->_xml['total'])) $error[] = 'Falta Total';
        if(empty($this->_xml['version'])) $error[] = 'Falta Version';                

        // //emisor
        if(empty($cfdi->Emisor->attributes()->nombre)) $error[] = 'Falta nombre Emisor';
        if(empty($cfdi->Emisor->attributes()->rfc)) $error[] = 'Falta RFC Emisor';
        //if(empty($cfdi->Emisor->DomicilioFiscal->attributes()->calle)) $error[] = 'Falta Calle Emisor';
        //if(empty($cfdi->Emisor->DomicilioFiscal->attributes()->noExterior)) $error[] = 'Falta numero Exterior Emisor';
        //if(empty($cfdi->Emisor->DomicilioFiscal->attributes()->colonia)) $error[] = 'Falta Colonia Emisor';
        if(empty($cfdi->Emisor->DomicilioFiscal->attributes()->codigoPostal)) $error[] = 'Falta codigo Postal Emisor';
        //if(empty($cfdi->Emisor->DomicilioFiscal->attributes()->municipio)) $error[] = 'Falta Municipio Emisor';
        //if(empty($cfdi->Emisor->DomicilioFiscal->attributes()->estado)) $error[] = 'Falta Estado Emisor';
        if(empty($cfdi->Emisor->DomicilioFiscal->attributes()->pais)) $error[] = 'Falta Pais Emisor';
        //if(empty($cfdi->Emisor->RegimenFiscal->attributes()->Regimen)) $error[] = 'Falta Regimen';


        // //receptor
        if(empty($cfdi->Receptor->attributes()->nombre)) $error[] = 'Falta Receptor';
        if(empty($cfdi->Receptor->attributes()->rfc)) $error[] = 'Falta RFC Receptor';
        //if(empty($cfdi->Receptor->Domicilio->attributes()->calle)) $error[] = 'Falta Calle Receptor';
        //if(empty($cfdi->Receptor->Domicilio->attributes()->noExterior)) $error[] = 'Falta numero Exterior Receptor';
        //if(empty($cfdi->Receptor->Domicilio->attributes()->colonia)) $error[] = 'Falta Colonia Receptor';
        if(empty($cfdi->Receptor->Domicilio->attributes()->codigoPostal)) $error[] = 'Falta codigo Postal Receptor';
        //if(empty($cfdi->Receptor->Domicilio->attributes()->municipio)) $error[] = 'Falta Municipio Receptor';
        //if(empty($cfdi->Receptor->Domicilio->attributes()->estado)) $error[] = 'Falta Estado Receptor';
        if(empty($cfdi->Receptor->Domicilio->attributes()->pais)) $error[] = 'Falta Pais Receptor';

        foreach ($cfdi->Conceptos->children($namespace['cfdi']) as $key => $value) {
                
        if(empty($value->attributes()->cantidad)) $error[] = 'Falta cantidad';
        if(empty($value->attributes()->descripcion)) $error[] = 'Falta descripcion';
        if(empty($value->attributes()->importe)) $error[] = 'Falta importe';
        if(empty($value->attributes()->unidad)) $error[] = 'Falta unidad';
        if(empty($value->attributes()->valorUnitario)) $error[] = 'Falta Valor Unitario';

        }

        // //impuestos
	    $impuestos = $cfdi->Impuestos->children($namespace['cfdi']);

	    if (isset($impuestos->Traslados)) {
			foreach ($impuestos->Traslados->Traslado as $value) {
	            if(empty($value->attributes()->tasa)) $error[] = 'Falta tasa';
	            if(empty($value->attributes()->importe)) $error[] = 'Falta importe';
	            if(empty($value->attributes()->impuesto)) $error[] = 'Falta impuesto';
			}

		}

	    if (isset($impuestos->Retenciones)) {

			foreach ($impuestos->Retenciones as $value) {
	            if(empty($value->attributes()->importe)) $error[] = 'Falta importe';
	            if(empty($value->attributes()->impuesto)) $error[] = 'Falta impuesto';
			}

		}

		$impuestos_locales = $cfdi->implocal->children($namespace['cfdi']);

	    // // Impuesto local
	    // 	if (isset($ns['implocal'])) {
	    //       $imp = $cfdi->Complemento->children($ns['implocal']);
	    //       if (isset($imp->ImpuestosLocales)) {
	    //           $ImpuestosLocales['ImpuestosLocales']['@atributos'] = array(
	    //               'TotaldeRetenciones' => (float) $imp->ImpuestosLocales->attributes()->TotaldeRetenciones,
	    //               'TotaldeTraslados'   => (float) $imp->ImpuestosLocales->attributes()->TotaldeTraslados,
	    //               'Version'            => (float) $imp->ImpuestosLocales->attributes()->Version,
	    //               );
	    //       }
	    //       if (isset($imp->ImpuestosLocales->RetencionesLocales)) {
	    //           $ImpuestosLocales['ImpuestosLocales']['RetencionesLocales']['@atributos'] = array(
	    //               'ImpLocRetenido'  => (string) $imp->ImpuestosLocales->RetencionesLocales->attributes()->ImpLocRetenido,
	    //               'TasadeRetencion' => (float) $imp->ImpuestosLocales->RetencionesLocales->attributes()->TasadeRetencion,
	    //               'Importe'         => (float) $imp->ImpuestosLocales->RetencionesLocales->attributes()->Importe,
	    //               );
	    //       }
	    //       if (isset($imp->ImpuestosLocales->TrasladosLocales)) {
	    //           $ImpuestosLocales['ImpuestosLocales']['TrasladosLocales']['@atributos'] = array(
	    //               'ImpLocTrasladado' => (string) $imp->ImpuestosLocales->TrasladosLocales->attributes()->ImpLocTrasladado,
	    //               'TasadeTraslado'   => (float) $imp->ImpuestosLocales->TrasladosLocales->attributes()->TasadeTraslado,
	    //               'Importe'          => (float) $imp->ImpuestosLocales->TrasladosLocales->attributes()->Importe,
	    //               );
	    //       }
	    //   }

        //complementos
		$complemento = $cfdi->Complemento->children($namespace['tfd']);

		if(empty($complemento->attributes()->FechaTimbrado)) $error[] = 'Falta fecha timbrado'; // con cierto formato
		if(empty($complemento->attributes()->UUID)) $error[] = 'Falta UUID';
		if(empty($complemento->attributes()->noCertificadoSAT)) $error[] = 'Falta noCertificadoSAT';
		if(empty($complemento->attributes()->selloCFD)) $error[] = 'Falta selloCFD';
		if(empty($complemento->attributes()->selloSAT)) $error[] = 'Falta selloSAT';

        return $error;

    }

}
