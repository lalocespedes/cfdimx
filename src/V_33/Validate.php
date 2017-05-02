<?php

namespace lalocespedes\Cfdimx\V_33;

use DOMDocument;
use Exception;

/**
 * 
 */
class Validate
{
    function __construct()
    {
        dump($this);
        exit;

        libxml_use_internal_errors(true);

        $xml->schemaValidate('/home/user/dev/timbrado/vendor/lalocespedes/cfdimx/src/utils/xsd33/cfdv33.xsd');

        $errors = libxml_get_errors();

        $error = [];

        foreach ($errors as $key => $error_xml) {

            if($error_xml->level === LIBXML_ERR_WARNING) {
                array_push($error, [
                    "message" =>$error_xml->message,
                    "level" => $error_xml->level
                ]);
            }

            if($error_xml->level === LIBXML_ERR_ERROR) {
                array_push($error, [
                    "message" =>$error_xml->message,
                    "level" => $error_xml->level
                ]);
            }

            if($error_xml->level === LIBXML_ERR_FATAL) {
                array_push($error, [
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
}
