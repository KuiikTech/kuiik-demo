<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();

$conexion = $obj_2->conexion();
$cod_mesa = 0;

if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];

	$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
	$result_e = mysqli_query($conexion, $sql_e);
	$ver_e = mysqli_fetch_row($result_e);

	$rol = $ver_e[5];

	$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado` FROM `caja` WHERE estado = 'ABIERTA'";
	$result = mysqli_query($conexion, $sql);
	$mostrar = mysqli_fetch_row($result);

	if ($mostrar != NULL) {
		$cod_caja = $mostrar[0];
		if ($mostrar[7] == NULL)
			$count = 1;
		else
			$count = 1 + $mostrar[7];

		$datos = array(
			uniqid(),
			$count,
			1
		);

		$verificacion = $obj->agregar_mesa($datos);

		if ($verificacion == 1) {
			$sql = "SELECT MAX(cod_mesa)
					as codigo  from mesas";
			$result = mysqli_query($conexion, $sql);
			$ver = mysqli_fetch_row($result);
			$cod_mesa = $ver[0];
			$sql = "UPDATE `caja` SET 
						`dinero`='$count'
						WHERE codigo='$cod_caja'";

			$verificacion = mysqli_query($conexion, $sql);
		}
	} else
		$verificacion = 'No existe una caja ABIERTA';
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion,
	'cod_mesa' => $cod_mesa
);
echo json_encode($datos);
