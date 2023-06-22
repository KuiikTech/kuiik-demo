<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
require_once('../plugins/fpdf182/fpdf.php');
$obj= new crud();
$obj_2= new conectar();
$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();
$fecha_h=date('Y-m-d G:i:s');

$cod_factura = 0;
$ruta_pdf = '';

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$files = glob('../pdf_pagos/*');
	foreach($files as $file)
	{
		if(is_file($file))
			unlink($file); 
	}

	$verificacion = 1;

	$cod_pago=$_POST['cod_pago'];

	$sql = "SELECT `codigo`, `comisiones`, `usuario`, `creador`, `fecha_registro` FROM `pagos_usuarios` WHERE codigo = '$cod_pago'";
	$result=mysqli_query($conexion,$sql);
	$mostrar_pagos=mysqli_fetch_row($result);

	$comisiones = array();
	$pos = 0;
	if ($mostrar_pagos[1] != '')
	{
		$comisiones = json_decode($mostrar_pagos[1],true);
		$pos = count($comisiones);
	}

	$usuario = $mostrar_pagos[2];
	$creador = $mostrar_pagos[3];

	$sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
	$result_e=mysqli_query($conexion,$sql_e);
	$ver_e=mysqli_fetch_row($result_e);

	$creador = $ver_e[0];

	$fecha_registro = date("d/m/Y", strtotime($mostrar_pagos[4]));

	$sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Empresa'";
	$result=mysqli_query($conexion,$sql);
	$ver=mysqli_fetch_row($result);

	$empresa = json_decode($ver[2],true);

	$sql = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `foto`, `telefono`, `rol`, `fecha_registro`, `estado`, `permisos`, `comisiones` FROM `usuarios` WHERE codigo = '$usuario'";
	$result=mysqli_query($conexion,$sql);
	$usuario = $result->fetch_object();
	$usuario = json_encode($usuario,JSON_UNESCAPED_UNICODE);
	$usuario = json_decode($usuario, true);

	class PDF extends FPDF
	{
	}

	$codigo = str_pad($cod_pago,6,"0",STR_PAD_LEFT);
	$fecha=date('d/m/Y g:i:s A');

	$tam_caracteres = count($comisiones);

	$alto_pag = 120+(($tam_caracteres)*8);
// Creación del objeto de la clase heredada
	$pdf = new PDF('P','mm',array(70,$alto_pag));
	$pdf->AliasNbPages();
	$pdf->AddPage();


	$pos_y = 23;
		//$fecha = date('d/m/Y',$fecha);
	$pdf->AddFont('framd','','framd.php');
		// Fondo
	$pdf->Image('../recursos/logo_empresa.png',4,5,60);

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
//$pos_y += 3;
//$pdf->SetXY(0,$pos_y);
//$pdf->Cell(68,3,utf8_decode("Cel: 324 644 0817"),0,0,'C');

	$pos_y += 4;

	$pdf->SetXY(0,$pos_y);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(0,0,0);
	$pdf->Cell(70,0.1,'',1,1,'C',true);


	$pos_y = $pdf->GetY()+1;


	$pdf->SetFont('framd','',9);

	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(40,5,utf8_decode("Pago # ".$codigo),0,0,'L');
	$pdf->SetXY(40,$pos_y);
	$pdf->Cell(30,5,utf8_decode("Fecha: ".$fecha_registro),0,0,'R');

	$pos_y += 5;

	$pdf->SetXY(0,$pos_y);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(0,0,0);
	$pdf->Cell(70,0.1,'',1,1,'C',true);

	$pdf->SetTextColor(0,0,0);

	$pos_y += 2;

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode("Usuario: "),0,0,'L');
	$pdf->SetFont('framd','',10);
	$pdf->SetXY(12,$pos_y);
	$pdf->Cell(65,3,utf8_decode($usuario['nombre']),0,0,'L');

	$pos_y += 4;

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode("Tel: "),0,0,'L');
	$pdf->SetFont('framd','',10);
	$pdf->SetXY(6,$pos_y);
	$pdf->Cell(67,3,utf8_decode($usuario['telefono']),0,0,'L');

	$pos_y += 4;

	$pdf->SetXY(0,$pos_y);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetTextColor(255,255,255);

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(40,4,utf8_decode("Producto"),0,0,'C',true);
	$pdf->SetXY(40,$pos_y);
	$pdf->Cell(6,4,utf8_decode("Cant"),0,0,'R',true);
	$pdf->SetXY(46,$pos_y);
	$pdf->Cell(12,4,utf8_decode("Valor"),0,0,'C',true);
	$pdf->SetXY(58,$pos_y);
	$pdf->Cell(12,4,utf8_decode("Total"),0,0,'C',true);

	$pos_y += 5;

	$pdf->SetTextColor(0,0,0);
	$total = 0;
	$impuestos = array();
	$iva = 0;
	foreach ($comisiones as $i => $comision)
	{
		$cod_producto = $comision['cod_producto'];
		$num_inv = $comision['num_inv'];
		$cant = $comision['cant'];
		$descripcion = $comision['descripcion'];
		$valor_unitario = $comision['valor_unitario'];
		$porcentaje = $comision['porcentaje'];
		$total_comision = $comision['total_comision'];

		$total += $total_comision;

		$valor_unitario = '$'.number_format($valor_unitario*$porcentaje,0,'.','.');
		$total_comision = '$'.number_format($total_comision,0,'.','.');

		$pdf->SetFont('framd','',9);
		$pdf->SetXY(40,$pos_y);
		$pdf->Cell(6,4,utf8_decode($cant),0,0,'C');
		$pdf->SetXY(46,$pos_y);
		$pdf->Cell(12,4,utf8_decode($valor_unitario),0,0,'R');
		$pdf->SetXY(58,$pos_y);
		$pdf->Cell(12,4,utf8_decode($total_comision),0,0,'R');

		$pdf->SetXY(0,$pos_y);
		$pdf->MultiCell(40,4,utf8_decode($descripcion),0,'L');


		$pos_y = $pdf->gety();
		$pos_y += 1;
	}

	$pdf->SetXY(0,$pos_y);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(0,0,0);
	$pdf->Cell(70,0.1,'',1,1,'C',true);

	$pos_y += 3;

	$pdf->SetFont('framd','',15);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode("TOTAL PAGADO: $".number_format($total,0,'.','.')),0,0,'R');

	$pos_y += 5;

	$pdf->SetXY(0,$pos_y);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(0,0,0);
	$pdf->Cell(70,0.1,'',1,1,'C',true);

	$pos_y += 2;

	$pdf->SetFont('framd','',10);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode("Creador: "),0,0,'L');
	$pdf->SetFont('framd','',9);
	$pdf->SetXY(15,$pos_y);
	$pdf->Cell(67,3,utf8_decode($creador),0,0,'L');

	$pos_y += 5;
	$pdf->SetXY(0,$pos_y);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(0,0,0);
	$pdf->Cell(70,0.1,'',1,1,'C',true);
	$pos_y += 4;
	$pdf->Image('../recursos/logo_Kuiik.jpg',0,$pos_y,70);

	$pdf->SetTitle(utf8_decode('Pago No '.$codigo));
	$pdf->SetAuthor('Kuiik - Desarrollo de Software');

	$pdf->Output('f',utf8_decode('../pdf_pagos/Pago No '.$codigo.'.pdf'));

	$ruta_pdf = 'pdf_pagos/Pago*No*'.$codigo.'.pdf';
}
else
	$verificacion = 'Reload';


$datos=array(
	'consulta' => $verificacion,
	'cod_pago' => $cod_pago,
	'ruta_pdf' => $ruta_pdf
);

echo json_encode($datos);

?>
