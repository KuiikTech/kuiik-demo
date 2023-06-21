<?php
require('../plugins/fpdf183/fpdf.php');

date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s A');
$fecha=date('Y-m-d');
require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();

$cod_venta = $_POST['cod_venta'];

$files = glob('../detalles/impresion/*');
foreach($files as $file)
{
	if(is_file($file))
		unlink($file); 
}

$verificacion = 1;

$items_venta = array();

$sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Empresa'";
$result=mysqli_query($conexion,$sql);
$ver=mysqli_fetch_row($result);

$empresa = json_decode($ver[2],true);

$sql = "SELECT `codigo`, `cliente`, `productos`, `pago`, `fecha`, `cobrador`, `estado`, `caja` FROM `ventas` WHERE codigo = '$cod_venta'";
$result=mysqli_query($conexion,$sql);
$mostrar=mysqli_fetch_row($result);

$total = 0;
$cliente = json_decode($mostrar[1],true);
$caja = $mostrar[7];

$items_venta = json_decode($mostrar[2],true);
$pagos = json_decode($mostrar[3],true);
foreach ($items_venta as $i => $producto)
	$total += $producto['valor_unitario']*$producto['cant'];

$creador = $mostrar[5];

$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
$result_e=mysqli_query($conexion,$sql_e);
$ver_e=mysqli_fetch_row($result_e);

$creador = $ver_e[0].' '.$ver_e[1];

$fecha_venta = date("d/m/Y", strtotime($mostrar[4]));

class PDF extends FPDF
{
}

$codigo = str_pad($cod_venta,6,"0",STR_PAD_LEFT);
$fecha=date('d/m/Y g:i:s A');

$tam_caracteres = count($items_venta);

$alto_pag = 180+(($tam_caracteres)*8);
// Creación del objeto de la clase heredada
$pdf = new PDF('P','mm',array(70,$alto_pag));
$pdf->AliasNbPages();
$pdf->AddPage();


$pos_y = 20;
		//$fecha = date('d/m/Y',$fecha);
$pdf->AddFont('framd','','framd.php');
		// Fondo
//$pdf->Image('recursos/logo_empresa.png',4,10,60);

$pdf->SetFont('framd','',9);

$pdf->SetXY(0,$pos_y);
$pdf->Cell(68,3,utf8_decode($empresa['nombre']),0,0,'C');
$pos_y += 3;
$pdf->SetXY(0,$pos_y);
$pdf->Cell(68,3,utf8_decode("NIT: ".$empresa['nit']),0,0,'C');
$pos_y += 3;
$pdf->SetXY(0,$pos_y);
$pdf->Cell(68,3,utf8_decode($empresa['direccion']),0,0,'C');
$pos_y += 3;
$pdf->SetXY(0,$pos_y);
$pdf->Cell(68,3,utf8_decode($empresa['ciudad']),0,0,'C');
$pos_y += 3;
$pdf->SetXY(0,$pos_y);
$pdf->Cell(68,3,utf8_decode($empresa['telefono']),0,0,'C');

$pos_y += 4;

$pdf->SetXY(0,$pos_y);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFillColor(0,0,0);
$pdf->Cell(70,0.1,'',1,1,'C',true);


$pos_y = $pdf->GetY()+1;


$pdf->SetFont('framd','',9);

$pdf->SetXY(0,$pos_y);
$pdf->Cell(40,5,utf8_decode("Venta # ".$codigo),0,0,'L');
$pdf->SetXY(40,$pos_y);
$pdf->Cell(30,5,utf8_decode("Fecha: ".$fecha_venta),0,0,'R');

$pos_y += 5;

$pdf->SetXY(0,$pos_y);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFillColor(0,0,0);
$pdf->Cell(70,0.1,'',1,1,'C',true);

$pdf->SetTextColor(0,0,0);

$pos_y += 2;

$pdf->SetFont('framd','',9);
$pdf->SetXY(0,$pos_y);
$pdf->Cell(68,3,utf8_decode("Cliente: "),0,0,'L');
$pdf->SetFont('framd','',10);
$pdf->SetXY(12,$pos_y);
$pdf->Cell(65,3,utf8_decode($cliente['nombre']),0,0,'L');

$pos_y += 4;

$pdf->SetFont('framd','',9);
$pdf->SetXY(0,$pos_y);
$pdf->Cell(68,3,utf8_decode("Tel: "),0,0,'L');
$pdf->SetFont('framd','',10);
$pdf->SetXY(6,$pos_y);
$pdf->Cell(67,3,utf8_decode($cliente['telefono']),0,0,'L');

$pos_y += 4;

$pdf->SetXY(0,$pos_y);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFillColor(0,0,0);
$pdf->SetTextColor(255,255,255);

$pdf->SetFont('framd','',9);
$pdf->SetXY(0,$pos_y);
$pdf->Cell(40,4,utf8_decode("DESCRIPCION"),0,0,'C',true);
$pdf->SetXY(40,$pos_y);
$pdf->Cell(6,4,utf8_decode("CANT"),0,0,'R',true);
$pdf->SetXY(46,$pos_y);
$pdf->Cell(24,4,utf8_decode("VALOR"),0,0,'C',true);

$pos_y += 5;

$pdf->SetTextColor(0,0,0);
$total = 0;
$impuestos = array();
$iva = 0;
foreach ($items_venta as $i => $item)
{
	$descripcion = $item['descripcion'];
	$valor = $item['valor_unitario'];
	$cant = $item['cant'];

	$total += $valor*$cant;

	$total_producto = $item['valor_unitario'] * $item['cant'];

	$valor = '$'.number_format($total_producto,0,'.','.');

	$pdf->SetXY(40,$pos_y);
	$pdf->Cell(6,3,utf8_decode($cant),0,0,'C');
	$pdf->SetXY(46,$pos_y);
	$pdf->Cell(24,3,utf8_decode($valor),0,0,'R');
	$pdf->SetXY(65,$pos_y);
	$pdf->SetFont('framd','',9);
	$pdf->SetXY(0,$pos_y);
	$pdf->MultiCell(40,3,utf8_decode($descripcion),'L');

	$pos_y = $pdf->gety();
	$pos_y += 1;
}

$pdf->SetXY(0,$pos_y);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFillColor(0,0,0);
$pdf->Cell(70,0.1,'',1,1,'C',true);

$pos_y += 5;

$pdf->SetFont('framd','',15);
$pdf->SetXY(0,$pos_y);
$pdf->Cell(68,3,utf8_decode("TOTAL: $".number_format($total,0,'.','.')),0,0,'R');

$pos_y += 5;

$pdf->SetXY(0,$pos_y);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFillColor(0,0,0);
$pdf->Cell(70,0.1,'',1,1,'C',true);

$pos_y += 1;

$pdf->SetFont('framd','',10);
$pdf->SetXY(0,$pos_y);
$pdf->Cell(68,3,utf8_decode("Metodo de pago"),0,0,'C');

foreach ($pagos as $i => $pago)
{
	$pos_y += 4;

	$pdf->SetFont('framd','',12);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode($pago['tipo']."     $".number_format($pago['valor'],0,'.','.')),0,0,'R');
}

$pos_y += 5;

$pdf->SetXY(0,$pos_y);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFillColor(0,0,0);
$pdf->Cell(70,0.1,'',1,1,'C',true);

$pos_y += 1;

$pdf->SetFont('framd','',10);
$pdf->SetXY(0,$pos_y);
$pdf->Cell(68,3,utf8_decode("Caja #".$caja.": "),0,0,'L');
$pdf->SetFont('framd','',9);
$pdf->SetXY(15,$pos_y);
$pdf->Cell(67,3,utf8_decode($creador),0,0,'L');

$pos_y += 4;

$pdf->SetXY(0,$pos_y);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFillColor(0,0,0);
$pdf->Cell(70,0.1,'',1,1,'C',true);

$pos_y += 3;
$pdf->SetFont('framd','',9);

$pdf->SetXY(0,$pos_y);
$pdf->Cell(68,3,utf8_decode("Muchas gracias por preferirnos"),0,0,'C');

$pos_y += 3;
$pdf->SetFont('framd','',9);

$pdf->SetXY(0,$pos_y);
$pdf->Cell(68,3,utf8_decode("Vuelve pronto"),0,0,'C');

$pos_y += 8;
$pdf->SetXY(0,$pos_y);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFillColor(0,0,0);
$pdf->Cell(70,0.1,'',1,1,'C',true);
$pos_y += 2;
$pdf->Image('recursos/logo_witsoft.jpg',0,$pos_y,70);

$pdf->SetTitle(utf8_decode('Venta No '.$codigo));
$pdf->SetAuthor('Witsoft - Desarrollo de Software');

$pdf->Output('f',utf8_decode('../detalles/impresion/'.$cod_venta.'.pdf'));

$ruta_pdf = '../detalles/impresion/'.$cod_venta.'.pdf';

$datos=array(
	'consulta' => $verificacion,
	'cod_venta' => $cod_venta,
	'ruta_pdf' => $ruta_pdf
);

echo json_encode($datos);

?>