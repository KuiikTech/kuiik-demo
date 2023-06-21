<?php
date_default_timezone_set('America/Bogota');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();

$fecha_h = date('Y-m-d G:i:s');

$conexion = $obj_2->conexion();
$conexion = $obj_2->conexion();

if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];

	$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
	$result_e = mysqli_query($conexion, $sql_e);
	$ver_e = mysqli_fetch_row($result_e);

	$rol = $ver_e[5];

	if ($rol == 'Administrador') {
		$verificacion = 1;

		$cod_caja = $_POST['cod_caja'];
		$caja = $_POST['caja'];
		$item = $_POST['item'];
		if ($caja == 1) {
			$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `info`, `creador`, `cajero`, `finalizador`, `estado` FROM `caja` WHERE codigo = '$cod_caja'";
		} else if ($caja == 2) {
			$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `info`, `creador`, `cajero`, `finalizador`, `estado` FROM `caja2` WHERE codigo = '$cod_caja'";
		} else {
			$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `info`, `creador`, `cajero`, `finalizador`, `estado` FROM `caja3` WHERE codigo = '$cod_caja'";
		}
		$result = mysqli_query($conexion, $sql);
		$mostrar_caja = mysqli_fetch_row($result);

		$estado = $mostrar_caja[14];

		if ($estado == 'ABIERTA') {
			$recargas = array();
			$nuevos_recargas = array();
			$pos = 1;
			if ($mostrar_caja[10] != '')
				$recargas = json_decode($mostrar_caja[10], true);

			foreach ($recargas as $j => $recarga) {
				if ($j != $item) {
					$nuevos_recargas[$pos] = $recarga;
					$pos++;
				}
			}

			if ($pos > 1)
				$nuevos_recargas = json_encode($nuevos_recargas, JSON_UNESCAPED_UNICODE);
			else
				$nuevos_recargas = '';

			if ($caja == 1) {
				$sql = "UPDATE `caja` SET 
				`info`='$nuevos_recargas'
				WHERE codigo='$cod_caja'";
			} else if ($caja == 2) {
				$sql = "UPDATE `caja2` SET 
				`info`='$nuevos_recargas'
				WHERE codigo='$cod_caja'";
			} else {
				$sql = "UPDATE `caja3` SET 
				`info`='$nuevos_recargas'
				WHERE codigo='$cod_caja'";
			}

			$verificacion = mysqli_query($conexion, $sql);
		} else
			$verificacion = 'No se eliminó el recarga. La caja NO se encuentra abierta';
	} else
		$verificacion = 'Solo los administradores pueden borrar Recargas';
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion
);
echo json_encode($datos);