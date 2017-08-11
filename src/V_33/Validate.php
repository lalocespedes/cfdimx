<?php

namespace lalocespedes\Cfdimx\V_33;

use DOMDocument;
use Exception;

/**
 * 
 */
class Validate
{
    /**
    * @var array
    */
    protected $errors = [];

    protected $xml;

    public function schemaXSD($xml)
    {
        $this->xml = $xml;
        $error = null;

        $xml = new DomDocument;
        $xml->loadXML(utf8_decode($this->xml)) or die("XML invalido");

        libxml_use_internal_errors(true);
       
        $xml->schemaValidate( __DIR__ . '/../utils/xsd/cfdv33.xsd' );

        $errors = libxml_get_errors();

        foreach ($errors as $key => $error_xml) {

            if($error_xml->level === LIBXML_ERR_WARNING) {
                array_push($this->errors, [
                    "message" =>$error_xml->message,
                    "level" => $error_xml->level
                ]);
            }

            if($error_xml->level === LIBXML_ERR_ERROR) {
                array_push($this->errors, [
                    "message" =>$error_xml->message,
                    "level" => $error_xml->level
                ]);
            }

            if($error_xml->level === LIBXML_ERR_FATAL) {
                array_push($this->errors, [
                    "message" =>$error_xml->message,
                    "level" => $error_xml->level
                ]);
            }

        }

        libxml_clear_errors();

        if(count($error)) {

            $this->valid = false;
            $this->errors = $error;
            $this->xml = null;
        }

        return $this;

    }

    public function failed()
    {
        return !empty($this->errors);
    }
    
    public function errors()
    {
        return $this->errors;
    }
}
