<?php

namespace lalocespedes\Cfdimx;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Carbon\Carbon;

/**
 *
 */
 class Csd
{
    public function __construct($path)
    {
        $adapter = new Local($path);
        $this->fs = new Filesystem($adapter);
    }

    public function getCer($file)
    {
        return $this->fs->read($file);
    }

    public function getKeyPem($file)
    {
        return $this->fs->read($file);
    }

    static function getnoCertificado($cer)
    {
        $certemp = tempnam('/tmp', 'cer');
        $fp = fopen($certemp, 'w');
        fwrite($fp, $cer);
        fclose($fp);

        $SerieCer = "";
        $xserial = exec('openssl x509 -inform DER -in '. realpath($certemp).' -serial -noout');

        $serie = str_replace('serial=', '', $xserial);
        $serie = str_split($serie);
        $max = count($serie);

        for ($i = 1; $i < $max ; $i++) {
            $SerieCer.=$serie[$i];
            $i++;
        }

        unlink(realpath($certemp));
        return $SerieCer;

    }

    private function belongsRfc($filepath)
    {
        $result = shell_exec('openssl x509 -inform DER -in '.$cer_path.' -subject');

        $result = explode("\n", $result);

        $line = substr($result[0], strpos($result[0], "UniqueIdentifier=") + 17);
        $rfc = trim(substr($line, 0, strpos($line, "/")));

        if($rfc != $this->user->tax_id_number){
            throw new \Exception('Este Certificado, pertenece a '. $rfc);
        }

        return true;
    }

    private function Outdate($filepath)
    {
        $result = shell_exec('openssl x509 -inform DER -in '.$filepath.' -enddate');
        $result = explode("\n", $result);
        $xfecha_vence = str_replace('notAfter=', '', $result[0]);

        if(Carbon::now()->timestamp >= Carbon::parse($xfecha_vence)->timestamp){
            throw new \Exception('Certificado vencido');
        }

        return true;
    }

}
