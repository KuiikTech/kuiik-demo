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
		$tipo = $_POST['tipo'];
		$valor = $_POST['valor'];

		$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado` FROM `caja` WHERE estado = 'ABIERTA'";
		$result = mysqli_query($conexion, $sql);
		$mostrar = mysqli_fetch_row($result);

		if ($mostrar == NULL || $tipo == 'Vista Caja') {
			$sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = '$tipo'";
			$result = mysqli_query($conexion, $sql);
			$mostrar = mysqli_fetch_row($result);

			if ($mostrar != NULL) {
				$sql = "UPDATE `configuraciones` SET 
				`valor`='$valor'
				WHERE descripcion='$tipo'";
			} else {
				$sql = "INSERT INTO `configuraciones`(`descripcion`, `valor`) VALUES ('$tipo', '$valor')";
			}

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
