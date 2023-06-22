<?php
require('../vendors/fpdf183/fpdf.php');

date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s A');
$fecha=date('Y-m-d');
require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();


$files = glob('../pdf/*');
foreach($files as $file)
{
	if(is_file($file))
		unlink($file); 
}

$verificacion = 1;

class PDF extends FPDF
{
}

$pdf = new PDF('P','mm',array(1,1));
$pdf->AliasNbPages();
$pdf->AddPage();


$pdf->SetTitle(utf8_decode('Abrir cajon'));
$pdf->SetAuthor('Kuiik - Desarrollo de Software');

$pdf->Output('f',utf8_decode('../pdf/abrir_cajon.pdf'));

$ruta_pdf = 'pdf/abrir_cajon.pdf';

$datos=array(
	'consulta' => $verificacion,
	'ruta_pdf' => $ruta_pdf
);

echo json_encode($datos);

?>