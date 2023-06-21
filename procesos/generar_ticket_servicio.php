<?php
require('../vendors/fpdf183/fpdf.php');
include '../vendors/barcode/barcode.php';

date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s A');
$fecha=date('Y-m-d');
require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();

$cod_servicio = $_POST['cod_servicio'];

$files = glob('../pdf/*');
foreach($files as $file)
{
	if(is_file($file))
		unlink($file); 
}

$verificacion = 1;

$sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Empresa'";
$result=mysqli_query($conexion,$sql);
$ver=mysqli_fetch_row($result);

$empresa = preg_replace("/[\r\n|\n|\r]+/", " ", $ver[2]);
$empresa = str_replace('  ', ' ', $empresa);
$empresa = json_decode($empresa,true);

$sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `local`, `fecha_registro` FROM `servicios` WHERE codigo = '$cod_servicio'";
$result=mysqli_query($conexion,$sql);
$mostrar=mysqli_fetch_row($result);

$informacion = array();
$items = array();
$repuestos = array();
$accesorios = array();
$pagos = array();

if($mostrar[4] != '')
{
	$informacion = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[4]);
	$informacion = str_replace('	', ' ', $informacion);
	$informacion = json_decode($informacion,true);
}
if($mostrar[1] != '')
{
	$items = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[1]);
	$items = str_replace('	', ' ', $items);
	$items = json_decode($items,true);
}

if($mostrar[5] != '')
{
	$repuestos = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[5]);
	$repuestos = str_replace('	', ' ', $repuestos);
	$repuestos = json_decode($repuestos,true);
}

if($mostrar[6] != '')
{
	$accesorios = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[6]);
	$accesorios = str_replace('	', ' ', $accesorios);
	$accesorios = json_decode($accesorios,true);
}
if($mostrar[3] != '')
	$pagos = json_decode($mostrar[3],true);
$cod_cliente = $mostrar[2];
$creador = $mostrar[7];
$tecnico = $mostrar[8];
$fecha_entrega = date("d-m-Y",strtotime($mostrar[10]));
$hora_entrega = date("h:i A",strtotime($mostrar[10]));
$fecha_registro = date('d-m-Y h:i A',strtotime($mostrar[12]));
$estado = $mostrar[9];
$local = $mostrar[11];

$fecha_entrega_input = date("Y-m-d",strtotime($mostrar[10]));
$hora_entrega_input = date("H:i",strtotime($mostrar[10]));

if($informacion['tipo'] == 'Orden')
	$informacion['tipo'] = 'Orden de servicio';

if(!isset($informacion['observaciones']))
	$informacion['observaciones'] = array();

if(!isset($informacion['solucion']))
	$informacion['solucion'] =  '<b class="text-warning">Sin asignar</b>';;

$sol_si = '';
$sol_no = '';

$info_equipo = array();
if(isset($informacion['info_equipo']))
	$info_equipo = $informacion['info_equipo'];

if($informacion['solucion']  == 'REPARADO')
{
	$informacion['solucion'] = '<b class="text-success">REPARADO</b>';
	$sol_si = 'selected';
}
if($informacion['solucion']  == 'NO REPARADO')
{
	$informacion['solucion'] = '<b class="text-danger">NO REPARADO</b>';
	$sol_no = 'selected';
}

if(isset($informacion['total_servicios']))
	$total_servicios = $informacion['total_servicios'];
else
	$total_servicios = 0;

$cod_equipo = $informacion['equipo'];

$sql_equipo = "SELECT `codigo`, `nombre`, `estado`, `fecha_creacion`, `creador` FROM `tipo_equipos` WHERE codigo = '$cod_equipo'";
$result_equipo=mysqli_query($conexion,$sql_equipo);
$ver_equipo=mysqli_fetch_row($result_equipo);

if($ver_equipo != null)
	$informacion['equipo'] = $ver_equipo[1];

if($mostrar[2] != '')
{
	$cliente = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[2]);
	$cliente = str_replace('	', ' ', $cliente);
	$cliente = json_decode($cliente,true);
}
$cod_cliente = $cliente['codigo'];

$sql_cliente = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro` FROM `clientes` WHERE `codigo`='$cod_cliente'";
$result_cliente=mysqli_query($conexion,$sql_cliente);
$cliente = $result_cliente->fetch_object();
$cliente = json_encode($cliente,JSON_UNESCAPED_UNICODE);
$cliente = json_decode($cliente, true);

if ($estado == 'PENDIENTE') 
	$bg_estado = 'bg-danger';
else if($estado == 'TERMINADO')
	$bg_estado = 'bg-success';
else
	$bg_estado = 'bg-info';

$nombre_tec = '<b class="text-danger">No asignado</b>';
if($mostrar[8] != null)
{
	$cod_tecnico = $mostrar[8];

	$sql_tec = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo = '$cod_tecnico'";
	$result_tec=mysqli_query($conexion,$sql_tec);
	$mostrar_tec=mysqli_fetch_row($result_tec);

	$nombre_tec = $mostrar_tec[2].' '.$mostrar_tec[3];
}

class PDF extends FPDF
{
}

$codigo = str_pad($cod_servicio,6,"0",STR_PAD_LEFT);
$codigo_pos = str_pad($cod_servicio,3,"0",STR_PAD_LEFT);
$fecha=date('d/m/Y g:i:s A');


$alto_pag = 180+(25*8);
// Creación del objeto de la clase heredada
$pdf = new PDF('P','mm',array(70,$alto_pag));
$pdf->AliasNbPages();
$pdf->AddPage();

//$fecha = date('d/m/Y',$fecha);
$pdf->AddFont('framd','','framd.php');
// Fondo
$pdf->Image('../recursos/logo_empresa.png',4,10,60);
$pos_y = 20;
$pdf->SetFont('framd','',9);

$pdf->SetXY(0,$pos_y);
$pdf->Cell(68,3,utf8_decode($empresa['nombre']),0,0,'C');
$pos_y += 3;
$pdf->SetXY(0,$pos_y);
$pdf->Cell(68,3,utf8_decode("NIT: ".$empresa['nit']),0,0,'C');
$pos_y += 3;
$pdf->SetXY(0,$pos_y);
$pdf->MultiCell(68,3,utf8_decode($empresa['direccion']),0,'C');
$pos_y = $pdf->GetY();
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

barcode('../recursos/barcode/'.$codigo_pos.'.png', $codigo_pos, 18, 'horizontal', 'Code39', false);
$pdf->Image('../recursos/barcode/'.$codigo_pos.'.png',4,$pos_y,60,5,'PNG');
unlink('../recursos/barcode/'.$codigo_pos.'.png');

$pos_y += 6;


$pdf->SetFont('framd','',9);

$pdf->SetXY(0,$pos_y);
$pdf->Cell(40,5,utf8_decode("Servicio # ".$codigo),0,0,'L');
$pdf->SetXY(40,$pos_y);
$pdf->Cell(30,5,utf8_decode("Fecha: ".$fecha_registro),0,0,'R');

$pos_y += 5;

$pdf->SetXY(0,$pos_y);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFillColor(0,0,0);
$pdf->Cell(70,0.1,'',1,1,'C',true);

$pdf->SetTextColor(255,255,255);

$pdf->SetXY(0,$pos_y);
$pdf->Cell(70,5,utf8_decode($estado),1,1,'C',true);

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

$pos_y += 5;

$pdf->SetXY(0,$pos_y);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFillColor(0,0,0);
$pdf->Cell(70,0.1,'',1,1,'C',true);

$pdf->SetTextColor(0,0,0);

$pos_y += 1;

$pdf->SetFont('framd','',9);
$pdf->SetXY(0,$pos_y);
$pdf->Cell(8,3,utf8_decode("Tipo: "),0,0,'L');
$pdf->SetFont('framd','',10);
$pdf->SetXY(8,$pos_y);
$pdf->Cell(64,3,utf8_decode($informacion['tipo']),0,0,'L');

$pos_y += 4;

$pdf->SetFont('framd','',9);
$pdf->SetXY(0,$pos_y);
$pdf->Cell(12,3,utf8_decode("Equipo: "),0,0,'L');
$pdf->SetFont('framd','',10);
$pdf->SetXY(12,$pos_y);
$pdf->Cell(60,3,utf8_decode($informacion['equipo']),0,0,'L');

$pos_y += 4;

$info_equipo = array();
if(isset($informacion['equipo']))
{	
	if(isset($informacion['lista_info']))
	{
		$info_equipo = $informacion['lista_info'];
		$total = 0;
		foreach ($info_equipo as $i => $item)
		{
			$nombre = $item['nombre'];
			$tipo = $item['tipo'];
			$valor = $item['valor'];

			$pdf->SetFont('framd','',10);
			$pdf->SetXY(5,$pos_y);
			$pdf->Cell(67,3,utf8_decode($nombre.': '.$valor),0,0,'L');

			$pos_y += 4;
		} 
	}
}

$pos_y += 2;

$pdf->SetFont('framd','',9);
$pdf->SetXY(0,$pos_y);
$pdf->Cell(20,3,utf8_decode("Observaciones: "),0,0,'L');
$pdf->SetFont('framd','',10);

$observaciones = $informacion['observaciones'];
$pos_y += 4;
foreach ($observaciones as $i => $obs)
{
	
	$pdf->SetXY(2,$pos_y);
	$pdf->MultiCell(67,3,utf8_decode('- '.$obs['obs']),0,'L');

	$pos_y = $pdf->gety();
	$pos_y += 1;
}

$pdf->SetXY(0,$pos_y);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFillColor(0,0,0);
$pdf->SetTextColor(255,255,255);

$pdf->SetFont('framd','',9);
$pdf->SetXY(0,$pos_y);
$pdf->Cell(40,4,utf8_decode("Daño"),0,0,'C',true);
$pdf->SetXY(40,$pos_y);
$pdf->Cell(32,4,utf8_decode("Observaciones"),0,0,'C',true);

$pos_y += 5;

$pdf->SetTextColor(0,0,0);

foreach ($items as $i => $item)
{
	$daño = $item['daño'];
	$observaciones = $item['observaciones'];

	$sql_daño = "SELECT `codigo`, `nombre`, `estado`, `fecha_creacion`, `creador` FROM `tipo_daños` WHERE codigo = '$daño'";
	$result_daño=mysqli_query($conexion,$sql_daño);
	$ver_daño=mysqli_fetch_row($result_daño);

	if($ver_daño != null)
		$daño = $ver_daño[1];

	$pdf->SetXY(0,$pos_y);
	$pdf->MultiCell(40,3,utf8_decode($daño),0,'L');
	$pdf->SetXY(40,$pos_y);
	$pdf->MultiCell(32,3,utf8_decode($observaciones),0,'L');
	
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
$pdf->Cell(68,3,utf8_decode("TOTAL: $".number_format($total_servicios,0,'.','.')),0,0,'R');

$pos_y += 5;

$pdf->SetXY(0,$pos_y);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFillColor(0,0,0);
$pdf->Cell(70,0.1,'',1,1,'C',true);

$pos_y += 1;

$pdf->SetFont('framd','',10);
$pdf->SetXY(0,$pos_y);
$pdf->Cell(68,3,utf8_decode("Pagos/Abonos"),0,0,'C');

foreach ($pagos as $i => $pago)
{
	$pos_y += 4;

	if(isset($pago['fecha']))
		$fecha_pago = date('d-m-Y H:i',strtotime($pago['fecha'])).' -> ';
	else
		$fecha_pago = '';

	$pdf->SetFont('framd','',7);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode($fecha_pago),0,0,'L');

	$pdf->SetFont('framd','',11);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode($pago['tipo']."     $".number_format($pago['valor'],0,'.','.')),0,0,'R');
}

$pos_y += 5;

$pdf->SetXY(0,$pos_y);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFillColor(0,0,0);
$pdf->Cell(70,0.1,'',1,1,'C',true);

$pos_y += 1;

$sql_e = "SELECT nombre, apellido, foto FROM `usuarios` WHERE codigo = '$creador'";
$result_e=mysqli_query($conexion,$sql_e);
$ver_e=mysqli_fetch_row($result_e);

if($ver_e != null)
	$creador = $ver_e[0].' '.$ver_e[1];

$pdf->SetFont('framd','',9);
$pdf->SetXY(2,$pos_y);
$pdf->Cell(67,3,utf8_decode('Creador: '.$creador),0,0,'L');

$pos_y += 4;

if(isset($informacion['observaciones_ticket']))
{
	$pdf->SetXY(0,$pos_y);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(0,0,0);
	$pdf->Cell(70,0.1,'',1,1,'C',true);

	$pos_y += 1;

	$pdf->SetFont('framd','',11);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(67,3,utf8_decode('Observaciones: '),0,0,'L');

	$observaciones = $informacion['observaciones_ticket'];
		$pos_y += 4;
	foreach ($observaciones as $i => $obs)
	{

		$pdf->SetXY(2,$pos_y);
		$pdf->MultiCell(67,3,utf8_decode('- '.$obs['obs']),0,'L');

		$pos_y = $pdf->gety();
		$pos_y += 1;
	}

	$pos_y = $pdf->gety();
	$pos_y += 1;
}

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
$pdf->Image('../recursos/logo_witsoft.jpg',0,$pos_y,70);

$pdf->SetTitle(utf8_decode('Servicio No '.$codigo));
$pdf->SetAuthor('Witsoft - Desarrollo de Software');

$pdf->Output('f',utf8_decode('../pdf/s'.$cod_servicio.'.pdf'));

$ruta_pdf = 'pdf/s'.$cod_servicio.'.pdf';

$datos=array(
	'consulta' => $verificacion,
	'cod_servicio' => $cod_servicio,
	'ruta_pdf' => $ruta_pdf
);

echo json_encode($datos);

?>