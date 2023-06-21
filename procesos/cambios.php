<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();
$conexion = $obj_2->conexion();
$conexion = $obj_2->conexion();

if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];

	$verificacion = 'Sin Cambios';

	$area = $_POST['area'];

	$sql_config = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Cambios $area'";
	$result_config = mysqli_query($conexion, $sql_config);
	$mostrar_config = mysqli_fetch_row($result_config);

	if ($mostrar_config[2] == 1) {
		$verificacion = 1;
		$sql = "UPDATE `configuraciones` SET 
			`valor`='0'
			WHERE descripcion='Cambios $area'";

		$verificacion = mysqli_query($conexion, $sql);
	}
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion
);

echo json_encode($datos);
