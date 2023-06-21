<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();
$conexion = $obj_2->conexion();

$fecha_h = date('Y-m-d G:i:s');

if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];

	$cod_caja = $_POST['cod_caja'];

	$sql = "UPDATE `caja` SET 
		`fecha_apertura`='$fecha_h',
		`cajero`='$usuario',
		`estado`='ABIERTA'
		WHERE codigo='$cod_caja'";

	$verificacion = mysqli_query($conexion, $sql);
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion
);

echo json_encode($datos);
