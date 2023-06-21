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

	$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
	$result_e = mysqli_query($conexion, $sql_e);
	$ver_e = mysqli_fetch_row($result_e);

	$rol = $ver_e[5];

	if ($rol == 'Mesero' || $rol == 'Administrador') {
		$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado` FROM `caja` WHERE estado = 'ABIERTA' OR estado = 'CREADA'";

		$result = mysqli_query($conexion, $sql);
		$mostrar = mysqli_fetch_row($result);

		if ($mostrar == NULL)
			$verificacion = $obj->agregar_caja();
		else
			$verificacion = 'Ya existe una caja ' . $mostrar[12];
	} else
		$verificacion = 'Para crear caja debe tener el rol de cajero';
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion
);
echo json_encode($datos);
