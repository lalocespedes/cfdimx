<?php

namespace cfdiMx;

use mPDF;
use Twig_Loader_Filesystem;
use Twig_Environment;
/**
* 
*/
class Pdf
{
	function __construct()
	{
		$loader = new Twig_Loader_Filesystem('../templates');
        $this->twig = new Twig_Environment($loader);
	}

	public function render($data)
	{
		//$template = ($template) ? $template : 'default.html';
        //header("Content-type:application/pdf");
        $html = $this->twig->render('default.html', $data);
        $mpdf = new mPDF();
        $mpdf->WriteHTML($html);
        $mpdf->Output();
	}
}
