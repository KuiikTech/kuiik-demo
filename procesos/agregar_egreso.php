<?php 
date_default_timezone_set('America/Bogota');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();

$fecha_h=date('Y-m-d G:i:s');

$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$verificacion = 1;

	$cod_caja = $_POST['cod_caja'];
	$caja=$_POST['caja'];
	$concepto_egreso = $_POST['concepto_egreso'];
	$valor_egreso=str_replace('.', '', $_POST['valor_egreso']);
	if($caja == 1)
	{
		$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `egresos`, `creador`, `cajero`, `finalizador`, `estado` FROM `caja` WHERE codigo = '$cod_caja'";
	}
	else if($caja == 2)
	{
		$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `egresos`, `creador`, `cajero`, `finalizador`, `estado` FROM `caja2` WHERE codigo = '$cod_caja'";
	}
	else
	{
		$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `egresos`, `creador`, `cajero`, `finalizador`, `estado` FROM `caja3` WHERE codigo = '$cod_caja'";
	}
	$result=mysqli_query($conexion,$sql);
	$mostrar_caja=mysqli_fetch_row($result);

	$estado = $mostrar_caja[14];

	if($estado == 'ABIERTA')
	{
		$egresos = array();
		$pos = 1;
		if ($mostrar_caja[10] != '')
		{
			$egresos = json_decode($mostrar_caja[10],true);
			$pos += count($egresos);
		}

		$egresos[$pos]["concepto"] = $concepto_egreso;
		$egresos[$pos]["valor"] = $valor_egreso;
		$egresos[$pos]["fecha"] = $fecha_h;
		$egresos[$pos]["creador"] = $usuario;

		$egresos = json_encode($egresos,JSON_UNESCAPED_UNICODE);

		if($caja == 1)
		{
			$sql="UPDATE `caja` SET 
			`egresos`='$egresos'
			WHERE codigo='$cod_caja'";
		}
		else if($caja == 2)
		{
			$sql="UPDATE `caja2` SET 
			`egresos`='$egresos'
			WHERE codigo='$cod_caja'";
		}
		else
		{
			$sql="UPDATE `caja3` SET 
			`egresos`='$egresos'
			WHERE codigo='$cod_caja'";
		}

		$verificacion = mysqli_query($conexion,$sql);
	}
	else
		$verificacion = 'No se agregó el egreso. La caja NO se encuentra abierta';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
