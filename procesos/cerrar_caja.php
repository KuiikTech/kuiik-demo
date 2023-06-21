<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();
$conexion = $obj_2->conexion();
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');

$obj = new crud();

$obj = new conectar();
$conexion = $obj->conexion();

if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];

	$cod_caja = $_POST['cod_caja'];

	$sql = "SELECT `cod_mesa`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_apertura`, `cod_cliente`, `pagos`, `mesero` FROM `mesas` WHERE estado = 'OCUPADA'";
	$result = mysqli_query($conexion, $sql);
	$mostrar = mysqli_fetch_row($result);

	if ($mostrar == null) {
		$sql = "UPDATE `caja` SET `fecha_cierre`='$fecha_h', `finalizador`='$usuario', `estado`='CERRADA' WHERE codigo='$cod_caja'";

		$verificacion = mysqli_query($conexion, $sql);
	} else
		$verificacion = 'Cierre o procese las ventas abiertas';
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion
);

echo json_encode($datos);
