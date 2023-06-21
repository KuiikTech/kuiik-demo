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

	$files = glob('../pdf/*');
	foreach($files as $file)
	{
		if(is_file($file))
			unlink($file); 
	}

	$verificacion = 1;

	$cod_cotizacion=$_POST['cod_cotizacion'];

	$sql = "SELECT `codigo`, `cliente`, `servicio`, `cotizó`, `creador`, `fecha_registro`, `observaciones`, `estado` FROM `cotizaciones` WHERE codigo = '$cod_cotizacion'";
	$result=mysqli_query($conexion,$sql);
	$mostrar_cotizacion=mysqli_fetch_row($result);

	$servicio = array();
	$pos = 0;
	if ($mostrar_cotizacion[2] != '')
	{
		$servicio = json_decode($mostrar_cotizacion[2],true);
		$pos = count($servicio);
	}

	$usuario = $mostrar_cotizacion[3];
	$creador = $mostrar_cotizacion[4];

	$sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
	$result_e=mysqli_query($conexion,$sql_e);
	$ver_e=mysqli_fetch_row($result_e);

	$creador = $ver_e[0];

	$fecha_registro = date("d/m/Y", strtotime($mostrar_cotizacion[5]));

	$sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Empresa'";
	$result=mysqli_query($conexion,$sql);
	$ver=mysqli_fetch_row($result);

	$empresa = json_decode($ver[2],true);

	$sql = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `foto`, `telefono`, `rol`, `fecha_registro`, `estado`, `color`, `movimientos`, `tel_emergencia`, `direccion`, `tipo_sangre`, `agendas`, `posicion` FROM `usuarios` WHERE codigo = '$usuario'";
	$result=mysqli_query($conexion,$sql);
	$usuario = $result->fetch_object();
	$usuario = json_encode($usuario,JSON_UNESCAPED_UNICODE);
	$usuario = json_decode($usuario, true);

	$cod_cliente = $mostrar_cotizacion[1];
	$sql = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `telefono`, `direccion`, `fecha_registro` FROM `clientes` WHERE codigo = '$cod_cliente'";
	$result=mysqli_query($conexion,$sql);
	$ver=mysqli_fetch_row($result);

	$cliente = array(
		'codigo' => $ver[0], 
		'id' => $ver[1], 
		'nombre' => ucwords(strtolower($ver[2].' '.$ver[3])), 
		'telefono' => $ver[4], 
		'direccion' => $ver[5]
	);

	class PDF extends FPDF
	{
	}

	$codigo = str_pad($cod_cotizacion,6,"0",STR_PAD_LEFT);
	$fecha=date('d/m/Y g:i:s A');

	$tam_caracteres = count($servicio);

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
	$pdf->Cell(40,5,utf8_decode("Cotizacion # ".$codigo),0,0,'L');
	$pdf->SetXY(40,$pos_y);
	$pdf->Cell(30,5,utf8_decode("Fecha: ".$fecha_registro),0,0,'R');

	$pos_y += 4;

	$pdf->SetXY(0,$pos_y);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(0,0,0);
	$pdf->Cell(70,0.1,'',1,1,'C',true);

	$pos_y += 1;

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(4,$pos_y);
	$pdf->Cell(6,3,utf8_decode("ID: "),0,0,'L');
	$pdf->SetFont('framd','',10);
	$pdf->SetXY(10,$pos_y);
	$pdf->Cell(65,3,utf8_decode($cliente['id']),0,0,'L');

	$pos_y += 4;

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(4,$pos_y);
	$pdf->Cell(13,3,utf8_decode("Nombre: "),0,0,'L');
	$pdf->SetFont('framd','',10);
	$pdf->SetXY(18,$pos_y);
	$pdf->Cell(57,3,utf8_decode($cliente['nombre']),0,0,'L');

	$pos_y += 4;

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(4,$pos_y);
	$pdf->Cell(14,3,utf8_decode("Telefono: "),0,0,'L');
	$pdf->SetFont('framd','',10);
	$pdf->SetXY(19,$pos_y);
	$pdf->Cell(67,3,utf8_decode($cliente['telefono']),0,0,'L');

	$pos_y += 4;

	$pdf->SetXY(0,$pos_y);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(0,0,0);
	$pdf->Cell(70,0.1,'',1,1,'C',true);

	$pdf->SetTextColor(0,0,0);

	$pos_y += 2;

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode("Cotizó: "),0,0,'L');
	$pdf->SetFont('framd','',10);
	$pdf->SetXY(12,$pos_y);
	$pdf->Cell(65,3,utf8_decode($usuario['nombre']),0,0,'L');

	$pos_y += 4;

	$pdf->SetXY(0,$pos_y);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetTextColor(255,255,255);

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(58,4,utf8_decode("Servicio"),0,0,'C',true);
	$pdf->SetXY(58,$pos_y);
	$pdf->Cell(12,4,utf8_decode("Valor"),0,0,'C',true);

	$pos_y += 5;

	$pdf->SetTextColor(0,0,0);

	$cod_producto = $servicio['codigo'];
	$descripcion = $servicio['descripcion'];
	$valor = $servicio['valor'];

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(58,$pos_y);
	$pdf->Cell(12,4,utf8_decode("$".number_format($valor,0,'.','.')),0,0,'R');

	$pdf->SetXY(0,$pos_y);
	$pdf->MultiCell(58,4,utf8_decode($descripcion),0,'L');


	$pos_y = $pdf->gety();
	$pos_y += 1;

	$pdf->SetXY(0,$pos_y);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(0,0,0);
	$pdf->Cell(70,0.1,'',1,1,'C',true);

	$pos_y += 3;

	$pdf->SetFont('framd','',15);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode("TOTAL COT: $".number_format($valor,0,'.','.')),0,0,'R');

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
	$pdf->Image('../recursos/logo_witsoft.jpg',0,$pos_y,70);

	$pdf->SetTitle(utf8_decode('Cotizacion No '.$codigo));
	$pdf->SetAuthor('Witsoft - Desarrollo de Software');

	$pdf->Output('f',utf8_decode('../pdf/Cotizacion No '.$codigo.'.pdf'));

	$ruta_pdf = 'pdf/Cotizacion*No*'.$codigo.'.pdf';
}
else
	$verificacion = 'Reload';


$datos=array(
	'consulta' => $verificacion,
	'cod_cotizacion' => $cod_cotizacion,
	'ruta_pdf' => $ruta_pdf
);

echo json_encode($datos);

?>
