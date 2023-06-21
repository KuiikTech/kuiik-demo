<?php
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();

$obj_2 = new conectar();
$conexion = $obj_2->conexion();

$fecha_h = date('Y-m-d G:i:s');

if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];

	$cod_reserva = $_POST['cod_reserva'];
	$verificacion = 1;

	$sql_reserva = "SELECT `codigo`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_registro`, `cod_cliente`, `pagos`, `fecha_llegada`, `descuentos`, `code`, `creador` FROM `reservas` WHERE codigo = '$cod_reserva'";
	$result_reserva = mysqli_query($conexion, $sql_reserva);
	$mostrar_reserva = mysqli_fetch_row($result_reserva);

	$productos_reserva = array();
	if ($mostrar_reserva[3] != '')
		$productos_reserva = json_decode($mostrar_reserva[3], true);

	foreach ($productos_reserva as $pos => $producto) {
		$cant = $producto['cant'];
		$cod_producto = $producto['codigo'];

		$sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo='$cod_producto'";
		$result_producto = mysqli_query($conexion, $sql_producto);
		$mostrar_producto = mysqli_fetch_row($result_producto);

		$stock = $mostrar_producto[4];
		$cod_categoria = $mostrar_producto[5];

		if ($mostrar_producto[9] == 'Producto') {
			if ($verificacion == 1) {
				$inventario = $stock + $cant;
				$sql = "UPDATE `productos` SET 
				`inventario`='$inventario'
				WHERE codigo='$cod_producto'";
				$verificacion = mysqli_query($conexion, $sql);
			}
		}
		if ($verificacion == 1) {
			$sql = "UPDATE `reservas` SET
		estado='CANCELADA'
		WHERE codigo='$cod_reserva'";

			$verificacion = mysqli_query($conexion, $sql);
		}
	}
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion
);

echo json_encode($datos);
