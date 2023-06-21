<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();
$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();

$usuario = $_SESSION['usuario_restaurante'];

$verificacion = 'Ha ocurrido un error actualice y vuelva a intentarlo.';
$estado = '';

if(isset($_POST['cod_producto']))
{
	$cod_producto = $_POST['cod_producto'];

	$sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode`, `movimientos` FROM `productos` WHERE codigo='$cod_producto'";
	$result_producto=mysqli_query($conexion,$sql_producto);
	$mostrar_producto=mysqli_fetch_row($result_producto);

	$estado = $mostrar_producto[10];

	if($estado == 'DISPONIBLE')
		$estado = 'NO DISPONIBLE';
	else
		$estado = 'DISPONIBLE';

	$sql="UPDATE `productos` SET 
	`estado`='$estado'
	WHERE codigo='$cod_producto'";

	$verificacion = mysqli_query($conexion,$sql);
}

if(isset($_POST['cod_usuario']))
{
	$cod_usuario = $_POST['cod_usuario'];

	$sql_usuario = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `foto`, `telefono`, `rol`, `fecha_registro`, `estado` FROM `usuarios` WHERE codigo='$cod_usuario'";
	$result_usuario=mysqli_query($conexion,$sql_usuario);
	$mostrar_usuario=mysqli_fetch_row($result_usuario);

	$estado = $mostrar_usuario[9];

	if($estado == 'ACTIVO')
		$estado = 'BLOQUEADO';
	else
		$estado = 'ACTIVO';

	$sql="UPDATE `usuarios` SET 
	`estado`='$estado'
	WHERE codigo='$cod_usuario'";

	$verificacion = mysqli_query($conexion,$sql);
}

if(isset($_POST['cod_cotizacion']))
{
	$cod_cotizacion = $_POST['cod_cotizacion'];

	$sql_usuario = "SELECT `codigo`, `cliente`, `servicio`, `cotizó`, `creador`, `fecha_registro`, `observaciones`, `estado` FROM `cotizaciones` WHERE codigo='$cod_cotizacion'";
	$result_usuario=mysqli_query($conexion,$sql_usuario);
	$mostrar_usuario=mysqli_fetch_row($result_usuario);

	$estado = $mostrar_usuario[7];

	if($estado == 'PENDIENTE')
		$estado = 'AGENDADO';
	else
	{
		if($estado == 'AGENDADO')
			$estado = 'REALIZADO';
		else
			$estado = 'PENDIENTE';
	}

	$sql="UPDATE `cotizaciones` SET 
	`estado`='$estado'
	WHERE codigo='$cod_cotizacion'";

	$verificacion = mysqli_query($conexion,$sql);
}

$datos=array(
	'consulta' => $verificacion,
	'estado' => $estado
);

echo json_encode($datos);

?>