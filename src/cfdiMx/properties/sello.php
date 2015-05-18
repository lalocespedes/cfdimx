<?php

namespace cfdiMx\properties;

use Exception;
use SimpleXMLElement;

/**
 *
 */
class sello
{
    final public static function extract(SimpleXMLElement $xml, array $namespace, $version)
    {
        //if (empty($xml['sello'])) throw new Exception('Error. Falta atributo Sello');

        switch ($version) {
            case 3:
            case 3.2:
                return (string) $xml['sello'];
                break;
            default:
                throw new Exception('Unkown document version ' . $version);
                break;
        }
    }
}
