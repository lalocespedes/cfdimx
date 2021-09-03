<?php

namespace lalocespedes\Cfdimx\V_33;

use Exception;
use lalocespedes\Cfdimx\V_33\Cfdi;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;

class ComplementoCartaPorte extends Cfdi
{
  public $xml;
  protected $validator;
  public $violations;
  protected $nodeComplemento;
  protected $nodeCartaPorte;
  protected $nodeUbicaciones;
  protected $nodeUbicacion;
  protected $nodeOrigen;
  protected $nodeDestino;
  protected $nodeDomicilio;
  protected $nodeMercancias;
  protected $nodeMercancia;
  protected $nodeCantidadTransporta;
  protected $nodeDetalleMercancia;
  protected $nodeAutotransporteFederal;
  protected $nodeIdentificacionVehicular;
  protected $nodeRemolques;
  protected $nodeRemolque;
  protected $nodeFiguraTransporte;
  protected $nodeOperadores;
  protected $nodeOperador;

  public function setNodeCartaPorte(array $data)
  {
    try {
      // Validate data
      v::arrayType()->notEmpty()->setName('CartaPorte')->assert($data);
      v::stringType()->notEmpty()->setName('CartaPorte:Version')->assert($data['Version']);
      v::stringType()->notEmpty()->setName('CartaPorte:TranspInternac')->assert($data['TranspInternac']);
      v::intVal()->notEmpty()->setName('CartaPorte:TotalDistRec')->assert($data['TotalDistRec']);

      // TODO valdiate suma distancia recorrida

      parent::setAttribute([
        "xsi:schemaLocation" => "http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd http://www.sat.gob.mx/CartaPorte http://www.sat.gob.mx/sitio_internet/cfd/CartaPorte/CartaPorte.xsd",
        "xmlns:cartaporte" => "http://www.sat.gob.mx/CartaPorte"
      ], $this->nodeComprobante);

      $this->nodeComplemento = $this->xml->createElement("cfdi:Complemento");
      $this->nodeComprobante->appendChild($this->nodeComplemento);

      $this->nodeCartaPorte = $this->xml->createElement("cartaporte:CartaPorte");
      $this->nodeComplemento->appendChild($this->nodeCartaPorte);

      parent::setAttribute($data, $this->nodeCartaPorte);
    } catch (NestedValidationException $exception) {
      array_push($this->errors, $exception->getMessages());
    }
  }

  public function setNodeUbicaciones(array $data)
  {
    try {

      if (!$this->nodeCartaPorte) {
        return false;
      }

      // Validate data
      v::arrayType()->notEmpty()->setName('Ubicaciones')->assert($data);

      if ($data['origen']) {
        v::stringType()->notEmpty()->setName('Ubicaciones:Origen:FechaHoraSalida')->assert($data['origen']['attributes']['FechaHoraSalida']);
        // v::stringType()->notEmpty()->setName('Ubicaciones:Origen:IDOrigen')->assert($ubicacion['origen']['IDOrigen']);
      }

      if ($data['destinos']) {
        foreach ($data['destinos'] as $key => $destino) {
          // TODO validar IDOrigen q sea el formato correcto
          v::intVal()->notEmpty()->setName('Ubicaciones:Destino:DistanciaRecorrida')->assert($destino['nodeUbicacion']['DistanciaRecorrida']);
          v::stringType()->notEmpty()->setName('Ubicaciones:Destino:IDDestino')->assert($destino['attributes']['IDDestino']);
          v::stringType()->notEmpty()->setName('Ubicaciones:Destino:FechaHoraProgLlegada')->assert($destino['attributes']['FechaHoraProgLlegada']);

          v::stringType()->notEmpty()->setName('Ubicaciones:Domicilio:Calle')->assert($destino['domicilio']['Calle']);
          v::stringType()->notEmpty()->setName('Ubicaciones:Domicilio:Estado')->assert($destino['domicilio']['Estado']);
          v::stringType()->notEmpty()->setName('Ubicaciones:Domicilio:Municipio')->assert($destino['domicilio']['Municipio']);
          v::stringType()->notEmpty()->setName('Ubicaciones:Domicilio:Pais')->assert($destino['domicilio']['Pais']);
          v::stringType()->notEmpty()->setName('Ubicaciones:Domicilio:CodigoPostal')->assert($destino['domicilio']['CodigoPostal']);
        }
      }
      $this->nodeUbicaciones = $this->xml->createElement("cartaporte:Ubicaciones");
      $this->nodeCartaPorte->appendChild($this->nodeUbicaciones);

      if ($data['origen']) {
        $this->nodeUbicacion = $this->xml->createElement("cartaporte:Ubicacion");
        $this->nodeUbicaciones->appendChild($this->nodeUbicacion);

        $this->nodeOrigen = $this->xml->createElement("cartaporte:Origen");
        $this->nodeUbicacion->appendChild($this->nodeOrigen);
        parent::setAttribute($data['origen']['attributes'], $this->nodeOrigen);

        $this->nodeDomicilio = $this->xml->createElement("cartaporte:Domicilio");
        $this->nodeUbicacion->appendChild($this->nodeDomicilio);
        parent::setAttribute($data['origen']['domicilio'], $this->nodeDomicilio);
      }

      if ($data['destinos']) {
        foreach ($data['destinos'] as $key => $destino) {
          $this->nodeUbicacion = $this->xml->createElement("cartaporte:Ubicacion");
          $this->nodeUbicaciones->appendChild($this->nodeUbicacion);
          parent::setAttribute($destino['nodeUbicacion'], $this->nodeUbicacion);

          $this->nodeDestino = $this->xml->createElement("cartaporte:Destino");
          $this->nodeUbicacion->appendChild($this->nodeDestino);
          parent::setAttribute($destino['attributes'], $this->nodeDestino);

          $this->nodeDomicilio = $this->xml->createElement("cartaporte:Domicilio");
          $this->nodeUbicacion->appendChild($this->nodeDomicilio);
          parent::setAttribute($destino['domicilio'], $this->nodeDomicilio);
        }
      }
    } catch (NestedValidationException $exception) {
      array_push($this->errors, $exception->getMessages());
    }
  }

  public function setNodeMercancias(array $data)
  {
    try {

      if (!$this->nodeCartaPorte) {
        return false;
      }
      // Validate data
      v::arrayType()->notEmpty()->setName('Mercancias')->assert($data['items']);

      if ($data['attributes']['NumTotalMercancias'] !== count($data['items'])) {
        array_push($this->errors, 'Mercancias:NumTotalMercancias El valor registrado no coincide con el número de elementos "Mercancia" que se registraron en el complemento.');
      }

      v::intVal()->notEmpty()->setName('Mercancias:NumTotalMercancias')->assert($data['attributes']['NumTotalMercancias']);

      $this->nodeMercancias = $this->xml->createElement("cartaporte:Mercancias");
      $this->nodeCartaPorte->appendChild($this->nodeMercancias);
      parent::setAttribute($data['attributes'], $this->nodeMercancias);

      foreach ($data['items'] as $key => $mercancia) {
        v::intVal()->notEmpty()->setName('Mercancias:Mercancia:PesoEnKg')->assert($mercancia['attributes']['PesoEnKg']);

        // validar solo si hay mas de dos destinos
        // v::arrayType()->notEmpty()->setName('Mercancias:Mercancia:CantidadTransporta')->assert($mercancia['CantidadTransporta']);
        // v::intVal()->notEmpty()->setName('Mercancias:Mercancia:CantidadTransporta:Cantidad')->assert($mercancia['CantidadTransporta']['Cantidad']);
        // v::stringType()->notEmpty()->setName('Mercancias:Mercancia:CantidadTransporta:IDOrigen')->assert($mercancia['CantidadTransporta']['IDOrigen']);
        // v::stringType()->notEmpty()->setName('Mercancias:Mercancia:CantidadTransporta:IDDestino')->assert($mercancia['CantidadTransporta']['IDDestino']);


        $this->nodeMercancia = $this->xml->createElement("cartaporte:Mercancia");
        $this->nodeMercancias->appendChild($this->nodeMercancia);
        parent::setAttribute($mercancia['attributes'], $this->nodeMercancia);

        // solo si hay dos o mas destinos
        if (v::arrayType()->notEmpty()->setName('Mercancias:AutotransporteFederal')->validate($mercancia['CantidadTransporta'])) {
          foreach ($mercancia['CantidadTransporta'] as $key => $cantidadTransporta) {
            $this->nodeCantidadTransporta = $this->xml->createElement("cartaporte:CantidadTransporta");
            $this->nodeMercancia->appendChild($this->nodeCantidadTransporta);
            parent::setAttribute($cantidadTransporta, $this->nodeCantidadTransporta);
          }
        }
      }

      if (key_exists('autotransporteFederal', $data)) {
        if (v::arrayType()->notEmpty()->setName('Mercancias:AutotransporteFederal')->validate($data['autotransporteFederal'])) {

          // Validate data
          v::stringType()->notEmpty()->setName('Mercancias:AutotransporteFederal:PermSCT')->assert($data['autotransporteFederal']['attributes']['PermSCT']);
          v::stringType()->notEmpty()->setName('Mercancias:AutotransporteFederal:NumPermisoSCT')->assert($data['autotransporteFederal']['attributes']['NumPermisoSCT']);
          v::stringType()->notEmpty()->setName('Mercancias:AutotransporteFederal:NombreAseg')->assert($data['autotransporteFederal']['attributes']['NombreAseg']);
          v::stringType()->notEmpty()->setName('Mercancias:AutotransporteFederal:NumPolizaSeguro')->assert($data['autotransporteFederal']['attributes']['NumPolizaSeguro']);

          v::arrayType()->notEmpty()->setName('Mercancias:AutotransporteFederal:IdentificacionVehicular')->assert($data['autotransporteFederal']['identificacionVehicular']);
          v::stringType()->notEmpty()->setName('Mercancias:AutotransporteFederal:IdentificacionVehicular:ConfigVehicular')->assert($data['autotransporteFederal']['identificacionVehicular']['ConfigVehicular']);
          v::stringType()->notEmpty()->setName('Mercancias:AutotransporteFederal:IdentificacionVehicular:PlacaVM')->assert($data['autotransporteFederal']['identificacionVehicular']['PlacaVM']);
          v::stringType()->notEmpty()->setName('Mercancias:AutotransporteFederal:IdentificacionVehicular:AnioModeloVM')->assert($data['autotransporteFederal']['identificacionVehicular']['AnioModeloVM']);

          $this->nodeAutotransporteFederal = $this->xml->createElement("cartaporte:AutotransporteFederal");
          $this->nodeMercancias->appendChild($this->nodeAutotransporteFederal);
          parent::setAttribute($data['autotransporteFederal']['attributes'], $this->nodeAutotransporteFederal);

          $this->nodeIdentificacionVehicular = $this->xml->createElement("cartaporte:IdentificacionVehicular");
          $this->nodeAutotransporteFederal->appendChild($this->nodeIdentificacionVehicular);
          parent::setAttribute($data['autotransporteFederal']['identificacionVehicular'], $this->nodeIdentificacionVehicular);

          if (key_exists('remolques', $data['autotransporteFederal'])) {
            // Remolques
            if (v::arrayType()->notEmpty()->setName('Mercancias:AutotransporteFederal:Remolques')->validate($data['autotransporteFederal']['remolques'])) {
              $this->nodeRemolques = $this->xml->createElement("cartaporte:Remolques");
              $this->nodeAutotransporteFederal->appendChild($this->nodeRemolques);

              foreach ($data['autotransporteFederal']['remolques'] as $key => $remolque) {
                $this->nodeRemolque = $this->xml->createElement("cartaporte:Remolque");
                $this->nodeRemolques->appendChild($this->nodeRemolque);
                $this->setAttribute($remolque, $this->nodeRemolque);
              }
            }
          }
        }
      }
    } catch (NestedValidationException $exception) {
      array_push($this->errors, $exception->getMessages([
        'Mercancias:AutotransporteFederal:PermSCT' => '{{name}} es requerido',
        'Mercancias:AutotransporteFederal:PermSCT' => '{{name}} es requerido',
        'Mercancias:AutotransporteFederal:NumPermisoSCT' => '{{name}} es requerido',
        'Mercancias:AutotransporteFederal:NombreAseg' => '{{name}} es requerido',
        'Mercancias:AutotransporteFederal:NumPolizaSeguro' => '{{name}} es requerido',
        'Mercancias:AutotransporteFederal:IdentificacionVehicular' => '{{name}} es requerido',
        'Mercancias:AutotransporteFederal:IdentificacionVehicular:ConfigVehicular' => '{{name}} es requerido',
      ]));
    }
  }

  public function setNodeFiguraTransporte(array $data)
  {
    try {

      if (!$this->nodeCartaPorte) {
        return false;
      }

      // Validate data
      v::stringType()->notEmpty()->setName('FiguraTransporte:CveTransporte')->assert($data['attributes']['CveTransporte']);
      v::arrayType()->notEmpty()->setName('FiguraTransporte:Operadores')->assert($data['operadores']);

      foreach ($data['operadores'] as $key => $operador) {
        v::stringType()->notEmpty()->setName('FiguraTransporte:Operadores:Operador:RFCOperador')->assert($operador['attributes']['RFCOperador']);
        v::arrayType()->notEmpty()->setName('FiguraTransporte:Operadores:Operador:domicilio')->assert($operador['domicilio']);
        v::stringType()->notEmpty()->setName('FiguraTransporte:Operadores:Operador:domicilio:CodigoPostal')->assert($operador['domicilio']['CodigoPostal']);
        v::stringType()->notEmpty()->setName('FiguraTransporte:Operadores:Operador:domicilio:Calle')->assert($operador['domicilio']['Calle']);
        v::stringType()->notEmpty()->setName('FiguraTransporte:Operadores:Operador:domicilio:Estado')->assert($operador['domicilio']['Estado']);
        v::stringType()->notEmpty()->setName('FiguraTransporte:Operadores:Operador:domicilio:Municipio')->assert($operador['domicilio']['Municipio']);
        v::stringType()->notEmpty()->setName('FiguraTransporte:Operadores:Operador:domicilio:Pais')->assert($operador['domicilio']['Pais']);
      }

      $this->nodeFiguraTransporte = $this->xml->createElement("cartaporte:FiguraTransporte");
      $this->nodeCartaPorte->appendChild($this->nodeFiguraTransporte);
      parent::setAttribute($data['attributes'], $this->nodeFiguraTransporte);

      $this->nodeOperadores = $this->xml->createElement("cartaporte:Operadores");
      $this->nodeFiguraTransporte->appendChild($this->nodeOperadores);

      foreach ($data['operadores'] as $key => $operador) {
        $this->nodeOperador = $this->xml->createElement("cartaporte:Operador");
        $this->nodeOperadores->appendChild($this->nodeOperador);
        parent::setAttribute($operador['attributes'], $this->nodeOperador);

        $this->nodeDomicilio = $this->xml->createElement("cartaporte:Domicilio");
        $this->nodeOperador->appendChild($this->nodeDomicilio);
        parent::setAttribute($operador['domicilio'], $this->nodeDomicilio);
      }
    } catch (NestedValidationException $exception) {
      array_push($this->errors, $exception->getMessages([
        'FiguraTransporte:CveTransporte' => '{{name}} es requerido',
        'FiguraTransporte:Operadores' => '{{name}} es requerido',
        'FiguraTransporte:Operadores:Operador:RFCOperador' => '{{name}} es requerido',
        'FiguraTransporte:Operadores:Operador:Domicilio' => '{{name}} es requerido',
        'FiguraTransporte:Operadores:Operador:CodigoPostal' => '{{name}} es requerido',
        'FiguraTransporte:Operadores:Operador:domicilio:Estado' => '{{name}} no existe o no contiene un valor  o no corresponde con una clave del "catCFDI:c_Estado',
        'FiguraTransporte:Operadores:Operador:domicilio:Municipio' => '{{name}} tiene un valor no permitido',
        'FiguraTransporte:Operadores:Operador:domicilio:Pais' => '{{name}} tiene un valor no permitido',
      ]));
    }
  }
}
