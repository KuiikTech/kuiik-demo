<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();
$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();

$fecha_h = date('Y-m-d G:i:s');
if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$cod_caja = $_POST['cod_caja'];

	$caja = $_POST['caja'];
	$descripcion_ingreso = $_POST['descripcion_ingreso'];
	$valor_ingreso = str_replace('.', '', $_POST['valor_ingreso']);

	if($caja == 1)
		$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `finalizador`, `egresos` FROM `caja` WHERE codigo = '$cod_caja'";
	else if($caja == 2)
		$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `finalizador`, `egresos` FROM `caja2` WHERE codigo = '$cod_caja'";
	else
		$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `finalizador`, `egresos` FROM `caja3` WHERE codigo = '$cod_caja'";
	$result=mysqli_query($conexion,$sql);
	$mostrar=mysqli_fetch_row($result);

	$ingresos = array();
	$pos = 1;
	if($mostrar[9]!= NULL)
		$ingresos = json_decode($mostrar[9],true);
	$pos += count($ingresos);

	$ingresos[$pos]['descripcion'] = $descripcion_ingreso;
	$ingresos[$pos]['valor'] = $valor_ingreso;
	$ingresos[$pos]['metodo'] = 'Efectivo';
	$ingresos[$pos]['fecha'] = $fecha_h;

	$ingresos = json_encode($ingresos,JSON_UNESCAPED_UNICODE);
	if($caja == 1)
		$sql="UPDATE `caja` SET `ingresos`='$ingresos' WHERE codigo='$cod_caja'";
	else if($caja == 2)
		$sql="UPDATE `caja2` SET `ingresos`='$ingresos' WHERE codigo='$cod_caja'";
	else
		$sql="UPDATE `caja3` SET `ingresos`='$ingresos' WHERE codigo='$cod_caja'";
	
	$verificacion = mysqli_query($conexion,$sql);
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>