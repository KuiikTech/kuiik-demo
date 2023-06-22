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

$files = glob('../pdf/*');
foreach($files as $file)
{
	if(is_file($file))
		unlink($file); 
}

$verificacion = 1;

session_set_cookie_params(7*24*60*60);
session_start();

$cod_caja = 0;
$ruta_pdf = '';
$transferencias = array();
$transferencias['Bancolombia'] = 0;
$transferencias['Nequi'] = 0;
$transferencias['Tarjeta'] = 0;
$transferencias['Daviplata'] = 0;

if (isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

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

	if($ver[2] != '')
	{
		$empresa = preg_replace("/[\r\n|\n|\r]+/", "&%&", $ver[2]);
		$empresa = str_replace('	', '&%&', $empresa);
		$empresa = json_decode($empresa,true);
	}

	$cod_caja=$_POST['cod_caja'];
	$caja=$_POST['caja'];

	$inventario= '';
	$ventas = '';
	$anular = 0;

	$base = 0;
	$total_preparaciones = 0;
	$total_productos = 0;
	$total_ventas = 0;
	$total_efectivo = 0;
	$total_transferencias = 0;
	$total_creditos = 0;
	$total_egresos = 0;
	$resultado = 0;
	$total_descuentos = 0;
	$total_otros = 0;

	$total_recargas = 0;
	$total_recargas_e = 0;
	$total_servicios = 0;

	$fecha_apertura = '---';
	$fecha_apertura_v = '---';
	$fecha_cierre = '---';

	$cajero = '---';
	$finalizador = '---';

	if($caja == 1)
		$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `finalizador`, `egresos`, `info`, `kilos_fin` FROM `caja` WHERE codigo = '$cod_caja'";
	else if($caja == 2)
		$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `finalizador`, `egresos`, `info`, `kilos_fin` FROM `caja2` WHERE codigo = '$cod_caja'";
	else
		$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `finalizador`, `egresos`, `info`, `kilos_fin` FROM `caja3` WHERE codigo = '$cod_caja'";
	$result=mysqli_query($conexion,$sql);
	$mostrar=mysqli_fetch_row($result);

	$estado = $mostrar[12];

	$fecha_registro = $mostrar[1];

	$base = $mostrar[8];
	$inventario = json_decode($mostrar[4],true);

	$ingresos = array();
	if($mostrar[9]!= NULL)
		$ingresos = json_decode($mostrar[9],true);

	$egresos = array();
	if ($mostrar[14] != '')
		$egresos = json_decode($mostrar[14],true);

	$recargas = array();
	if($mostrar[15]!= NULL)
		$recargas = json_decode($mostrar[15],true);

	$servicios = array();
	if($mostrar[16]!= NULL)
		$servicios = json_decode($mostrar[16],true);

	$creador = $mostrar[10];

	$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
	$result_e=mysqli_query($conexion,$sql_e);
	$ver_e=mysqli_fetch_row($result_e);

	if($ver_e != null)
		$creador = $ver_e[0].' '.$ver_e[1];

	if($estado == 'CREADA')
	{
		$sql_ventas = "SELECT `codigo`, `cliente`, `productos`, `pago`, `fecha`, `cobrador`, `estado`  FROM `ventas` WHERE fecha > '$mostrar[1]' AND caja = '$caja' order by fecha ASC";
		$sql_gastos = "SELECT `codigo`, `descripcion`, `valor`, `fecha_registro` FROM `gastos` WHERE fecha_registro > '$mostrar[1]' order by fecha_registro ASC";
	}

	if($estado == 'ABIERTA' || $estado == 'CERRADA')
	{
		$anular = 1;
		$cajero = $mostrar[11];
		if($estado == 'CERRADA')
			$fecha_cierre = $mostrar[3];
		else
			$fecha_cierre = date('Y-m-d G:i:s');

		$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$cajero'";
		$result_e=mysqli_query($conexion,$sql_e);
		$ver_e=mysqli_fetch_row($result_e);

		if($ver_e != null)
			$cajero = $ver_e[0].' '.$ver_e[1];

		$fecha_apertura = $mostrar[2];

		foreach ($ingresos as $i => $ingreso)
		{
			if(isset($ingreso['metodo']))
			{
				if($ingreso['metodo'] == 'Efectivo')
					$total_efectivo += $ingreso['valor'];

				if($ingreso['metodo'] == 'Devolución')
					$total_efectivo += $ingreso['valor'];

				if($ingreso['metodo'] == 'Bancolombia' || $ingreso['metodo'] == 'Nequi' || $ingreso['metodo'] == 'Tarjeta' || $ingreso['metodo'] == 'Daviplata')
					$total_transferencias += $ingreso['valor'];

				if($ingreso['metodo'] == 'Descuento')
				{
					$total_descuentos += $ingreso['valor'];
					$total_ventas += $ingreso['valor'];
				}
			}
			else
				$total_efectivo += $ingreso['valor'];

			$total_otros += $ingreso['valor'];
		}


		foreach ($egresos as $i => $egreso)
			$total_egresos += $egreso['valor'];

		foreach ($recargas as $i => $recarga)
		{
			$total_recargas += $recarga['valor'];

			if(isset($recarga['metodo']))
			{
				if($recarga['metodo'] == 'Efectivo')
					$total_recargas_e += $recarga['valor'];

				if($recarga['metodo'] == 'Bancolombia' || $recarga['metodo'] == 'Nequi' || $recarga['metodo'] == 'Tarjeta' || $recarga['metodo'] == 'Daviplata')
				{
					$total_transferencias += $recarga['valor'];
					if(isset($transferencias[$recarga['metodo']]))
						$transferencias[$recarga['metodo']] += $recarga['valor'];
					else
						$transferencias[$recarga['metodo']] = $recarga['valor'];
				}
			}
			else
				$total_recargas_e += $recarga['valor'];
		}

		foreach ($servicios as $i => $servicio)
		{
			$total_servicios += $servicio['valor'];
			if($servicio['metodo'] == 'Efectivo')
				$total_efectivo += $servicio['valor'];

			if($servicio['metodo'] == 'Devolución')
				$total_efectivo += $servicio['valor'];

			if($servicio['metodo'] == 'Bancolombia' || $servicio['metodo'] == 'Nequi' || $servicio['metodo'] == 'Tarjeta' || $servicio['metodo'] == 'Daviplata')
			{
				$total_transferencias += $servicio['valor'];
				if(isset($transferencias[$servicio['metodo']]))
					$transferencias[$servicio['metodo']] += $servicio['valor'];
				else
					$transferencias[$servicio['metodo']] = $servicio['valor'];
			}

			if($servicio['metodo'] == 'Crédito')
				$total_creditos += $servicio['valor'];

			if($servicio['metodo'] == 'Descuento')
			{
				$total_descuentos += $servicio['valor'];
				$total_ventas += $servicio['valor'];
			}


		}

		$sql_ventas = "SELECT `codigo`, `cliente`, `productos`, `pago`, `fecha`, `cobrador`, `estado`, `caja` FROM `ventas` WHERE caja = '$caja' AND fecha BETWEEN '$fecha_apertura' AND '$fecha_cierre'";
		$result_ventas=mysqli_query($conexion,$sql_ventas);

		while ($mostrar_ventas=mysqli_fetch_row($result_ventas)) 
		{
			$estado_venta = $mostrar_ventas[6];
			if($estado_venta != 'ANULADA')
			{
				$productos_venta = json_decode($mostrar_ventas[2],true);

				foreach ($productos_venta as $i => $producto)
					$total_ventas += $producto['valor_unitario']*$producto['cant'];

				$pagos_venta = json_decode($mostrar_ventas[3],true);
				foreach ($pagos_venta as $i => $pago)
				{
					if($pago['tipo'] == 'Efectivo')
						$total_efectivo += $pago['valor'];

					if($pago['tipo'] == 'Bancolombia' || $pago['tipo'] == 'Nequi' || $pago['tipo'] == 'Tarjeta' || $pago['tipo'] == 'Daviplata')
					{
						$total_transferencias += $pago['valor'];
						if(isset($transferencias[$pago['tipo']]))
							$transferencias[$pago['tipo']] += $pago['valor'];
						else
							$transferencias[$pago['tipo']] = $pago['valor'];
					}

					if($pago['tipo'] == 'Crédito')
						$total_creditos += $pago['valor'];

					if($pago['tipo'] == 'Descuento')
					{
						$total_descuentos += $pago['valor'];
						$total_ventas += $pago['valor'];
					}
				}
			}
		}

		$cajero = $mostrar[11];
		$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$cajero'";
		$result_e=mysqli_query($conexion,$sql_e);
		$ver_e=mysqli_fetch_row($result_e);

		if($ver_e != null)
			$cajero = $ver_e[0].' '.$ver_e[1];

		$fecha_apertura_v = strftime("%A, %e %b %Y", strtotime($mostrar[2]));
		$fecha_apertura_v = ucfirst(iconv("ISO-8859-1","UTF-8",$fecha_apertura_v));
		$fecha_apertura_v .= date(' | h:i A',strtotime($mostrar[2]));

	}

	if($estado == 'CERRADA')
	{
		$fecha_cierre = $mostrar[3];

		$fecha_cierre_v = strftime("%A, %e %b %Y", strtotime($mostrar[3]));
		$fecha_cierre_v = ucfirst(iconv("ISO-8859-1","UTF-8",$fecha_cierre_v));
		$fecha_cierre_v .= date(' | h:i A',strtotime($mostrar[3]));

		$finalizador = $mostrar[13];
		$sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$finalizador'";
		$result_e=mysqli_query($conexion,$sql_e);
		$ver_e=mysqli_fetch_row($result_e);

		if($ver_e != null)
			$finalizador = $ver_e[0];
	}
	else
		$fecha_cierre_v = '---';

	$efectivo_caja = $base+$total_efectivo-$total_egresos+$total_recargas_e;

	$base = '$'.number_format($base,0,'.','.');
	$total_ventas = '$'.number_format($total_ventas,0,'.','.');
	$total_efectivo = '$'.number_format($total_efectivo,0,'.','.');
	$total_transferencias = '$-'.number_format($total_transferencias,0,'.','.');
	$total_creditos = '$-'.number_format($total_creditos,0,'.','.');
	$total_egresos = '$-'.number_format($total_egresos,0,'.','.');
	$total_descuentos = '$'.number_format($total_descuentos,0,'.','.');
	$efectivo_caja = '$'.number_format($efectivo_caja,0,'.','.');
	$total_otros = '$'.number_format($total_otros,0,'.','.');

	$total_recargas = '$'.number_format($total_recargas,0,'.','.');
	$total_servicios = '$'.number_format($total_servicios,0,'.','.');

	$resultado = '$ '.number_format($resultado,0,'.','.');

	if (isset($_SESSION['usuario_restaurante2']))
		$local = 'Restaurante 2';
	else
		$local = 'Restaurante 1';

	class PDF extends FPDF
	{
	}

	$codigo = str_pad($cod_caja,6,"0",STR_PAD_LEFT);
	$fecha=date('d/m/Y g:i:s A');

	$alto_pag = 120+(10*8);
	// Creación del objeto de la clase heredada
	$pdf = new PDF('P','mm',array(70,$alto_pag));
	$pdf->AliasNbPages();
	$pdf->AddPage();


	$pos_y = 18;
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
	$pdf->MultiCell(68,3,utf8_decode(str_replace('&%&', "\n", $empresa['direccion'])),0,'C');
	$pos_y = $pdf->GetY();
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

	$pos_y = $pdf->GetY();

	$pdf->SetFont('framd','',9);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(70,4,utf8_decode("CIERRE DE CAJA"),1,1,'C',true);
	$pos_y += 4;

	$pdf->SetXY(0,$pos_y);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(0,0,0);
	$pdf->Cell(70,0.1,'',1,1,'C',true);

	$pos_y = $pdf->GetY()+1;

	$pdf->SetTextColor(0,0,0);

	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(70,4,utf8_decode("Local: ".$local.'  Caja '.$caja),0,0,'L');
	$pos_y += 4;
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(70,4,utf8_decode("Creación:   ".$fecha_registro),0,0,'L');
	$pos_y += 4;
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(70,4,utf8_decode("Apertura:   ".$fecha_apertura),0,0,'L');
	$pos_y += 4;
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(70,4,utf8_decode("Cierre:        ".$fecha_cierre),0,0,'L');

	$pos_y += 4;

	$pdf->SetXY(0,$pos_y);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(0,0,0);
	$pdf->Cell(70,0.1,'',1,1,'C',true);

	$pos_y = $pdf->GetY();
	$pdf->SetFont('framd','',12);

	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(70,5,utf8_decode("Reporte - Caja # ".$cod_caja),0,0,'C');

	$pos_y += 6;

	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(70,0.1,'',1,1,'C',true);

	$pdf->SetFont('framd','',9);

	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(70,5,utf8_decode("Fecha Creación: ".$fecha_registro),0,0,'L');

	$pos_y += 5;

	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(70,0.1,'',1,1,'C',true);

	$pdf->SetTextColor(0,0,0);

	$pos_y += 2;

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode("Creador: "),0,0,'L');
	$pdf->SetFont('framd','',10);
	$pdf->SetXY(12,$pos_y);
	$pdf->Cell(58,3,utf8_decode($creador),0,0,'L');

	$pos_y += 4;

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode("Cajero: "),0,0,'L');
	$pdf->SetFont('framd','',10);
	$pdf->SetXY(12,$pos_y);
	$pdf->Cell(58,3,utf8_decode($cajero),0,0,'L');

	$pos_y += 4;

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode("Fecha apertura: "),0,0,'L');
	$pdf->SetFont('framd','',10);
	$pdf->SetXY(24,$pos_y);
	$pdf->Cell(46,3,utf8_decode($fecha_apertura),0,0,'L');

	$pos_y += 4;

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode("Fecha cierre: "),0,0,'L');
	$pdf->SetFont('framd','',10);
	$pdf->SetXY(20,$pos_y);
	$pdf->Cell(50,3,utf8_decode($fecha_cierre),0,0,'L');

	$pos_y += 4;

	$pdf->SetXY(0,$pos_y);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(0,0,0);
	$pdf->Cell(70,0.1,'',1,1,'C',true);

	$pos_y += 2;

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode("Base: "),0,0,'L');
	$pdf->SetFont('framd','',10);
	$pdf->SetXY(24,$pos_y);
	$pdf->Cell(46,3,utf8_decode($base),0,0,'R');

	$pos_y += 4;

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode("Total Ventas: "),0,0,'L');
	$pdf->SetFont('framd','',10);
	$pdf->SetXY(24,$pos_y);
	$pdf->Cell(46,3,utf8_decode($total_ventas),0,0,'R');

	$pos_y += 4;

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode("Total Servicios: "),0,0,'L');
	$pdf->SetFont('framd','',10);
	$pdf->SetXY(24,$pos_y);
	$pdf->Cell(46,3,utf8_decode($total_servicios),0,0,'R');

	$pos_y += 4;

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode("Total Recargas: "),0,0,'L');
	$pdf->SetFont('framd','',10);
	$pdf->SetXY(24,$pos_y);
	$pdf->Cell(46,3,utf8_decode($total_recargas),0,0,'R');

	$pos_y += 4;

	$pdf->SetXY(0,$pos_y);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(0,0,0);
	$pdf->Cell(70,0.1,'',1,1,'C',true);

	$pos_y += 2;

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode("Total Efectivo: "),0,0,'L');
	$pdf->SetFont('framd','',10);
	$pdf->SetXY(24,$pos_y);
	$pdf->Cell(46,3,utf8_decode($total_efectivo),0,0,'R');

	$pos_y += 4;

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode("Transferencia: "),0,0,'L');

	$pos_y += 4;
	foreach ($transferencias as $m => $metodo)
	{
		if($m != 'Descuento')
		{
			$pdf->SetFont('framd','',8);
			$pdf->SetXY(0,$pos_y);
			$pdf->Cell(40,3,utf8_decode("   ".$m.":"),0,0,'R');
			$pdf->SetFont('framd','',9);
			$pdf->SetXY(24,$pos_y);
			$pdf->Cell(46,3,utf8_decode("$".number_format($metodo,0,'.','.')),0,0,'R');

			$pos_y += 4;
		}
	}

	$pos_y += 2;

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode("Créditos: "),0,0,'L');
	$pdf->SetFont('framd','',10);
	$pdf->SetXY(24,$pos_y);
	$pdf->Cell(46,3,utf8_decode($total_creditos),0,0,'R');

	$pos_y += 4;

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode("Descuentos: "),0,0,'L');
	$pdf->SetFont('framd','',10);
	$pdf->SetXY(24,$pos_y);
	$pdf->Cell(46,3,utf8_decode($total_descuentos),0,0,'R');

	$pos_y += 4;

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode("Gastos: "),0,0,'L');
	$pdf->SetFont('framd','',10);
	$pdf->SetXY(24,$pos_y);
	$pdf->Cell(46,3,utf8_decode($total_egresos),0,0,'R');

	$pos_y += 4;

	$pdf->SetFont('framd','',9);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode("Otros Ingresos: "),0,0,'L');
	$pdf->SetFont('framd','',10);
	$pdf->SetXY(24,$pos_y);
	$pdf->Cell(46,3,utf8_decode($total_otros),0,0,'R');

	$pos_y += 4;

	$pdf->SetXY(0,$pos_y);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(0,0,0);
	$pdf->Cell(70,0.1,'',1,1,'C',true);

	$pos_y += 3;

	$pdf->SetFont('framd','',15);
	$pdf->SetXY(0,$pos_y);
	$pdf->Cell(68,3,utf8_decode("EFECTIVO CAJA: ".$efectivo_caja),0,0,'R');

	$pos_y += 5;

	$pdf->SetXY(0,$pos_y);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(0,0,0);
	$pdf->Cell(70,0.1,'',1,1,'C',true);

	$pos_y += 8;
	$pdf->SetXY(0,$pos_y);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(0,0,0);
	$pdf->Cell(70,0.1,'',1,1,'C',true);
	$pos_y += 2;
	$pdf->Image('../recursos/logo_Kuiik.jpg',0,$pos_y,70);

	$pdf->SetTitle(utf8_decode('Cierre No '.$codigo.' MOVILAB'));
	$pdf->SetAuthor('Kuiik - Desarrollo de Software');

	$pdf->Output('f',utf8_decode('../pdf/c'.$cod_caja.'-Caja-'.$caja.'.pdf'));

	$ruta_pdf = 'pdf/c'.$cod_caja.'-Caja-'.$caja.'.pdf';
}
else
	$verificacion = 'Reload';


$datos=array(
	'consulta' => $verificacion,
	'cod_caja' => $cod_caja,
	'ruta_pdf' => $ruta_pdf
);

echo json_encode($datos);

?>
