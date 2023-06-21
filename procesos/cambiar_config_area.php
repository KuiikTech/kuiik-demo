<?php
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();

$obj = new conectar();
$conexion = $obj->conexion();

$fecha_h = date('Y-m-d G:i:s');

if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];

	require_once "../clases/permisos.php";
	$obj_permisos = new permisos();
	$acceso = $obj_permisos->buscar_permiso($usuario, 'Config PDV', 'GENERAL');

	if ($acceso == 'SI') {
		$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado` FROM `caja` WHERE estado = 'ABIERTA'";
		$result = mysqli_query($conexion, $sql);
		$mostrar = mysqli_fetch_row($result);

		if ($mostrar == NULL) {
			$area = $_POST['area'];
			$valor = $_POST['valor'];

			$control_area = 'Control ' . $area;

			$sql = "UPDATE `configuraciones` SET 
		`valor`='$valor'
		WHERE descripcion='$control_area'";

			$verificacion = mysqli_query($conexion, $sql);
		} else
			$verificacion = 'La caja debe estar cerrada para cambiar esta configuración';
	} else
		$verificacion = 'Usted no tiene permisos para cambiar esta configuración';
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion
);

echo json_encode($datos);
