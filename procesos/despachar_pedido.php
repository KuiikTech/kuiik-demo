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

	$verificacion = 1;

	$cod_pedido = $_POST['cod_pedido'];
	$num_item	= $_POST['num_item'];

	$sql_pedido = "SELECT `codigo`, `productos`, `mesa`, `solicitante`, `fecha_registro`, `fecha_envio`, `fecha_entrega`, `estado`, `area`, `respuesta` FROM `pedidos` WHERE codigo = '$cod_pedido'";
	$result_pedido = mysqli_query($conexion, $sql_pedido);
	$mostrar_pedido = mysqli_fetch_array($result_pedido);

	$respuesta = $mostrar_pedido['respuesta'];

	if ($respuesta == 'VENTA') {
		$cod_venta = $mostrar_pedido['mesa'];

		$sql_venta = "SELECT `codigo`, `cliente`, `productos`, `pago`, `fecha`, `cobrador`, `estado`, `caja`, `info` FROM `ventas` WHERE codigo = '$cod_venta'";
		$result_venta = mysqli_query($conexion, $sql_venta);
		$mostrar_venta = mysqli_fetch_row($result_venta);
	} else {
		$cod_mesa = $mostrar_pedido['mesa'];

		$sql_mesa = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura`, `cod_cliente`, `pagos` FROM `mesas` WHERE cod_mesa = '$cod_mesa'";
		$result_mesa = mysqli_query($conexion, $sql_mesa);
		$mostrar_mesa = mysqli_fetch_row($result_mesa);
	}

	$productos_pedido = array();
	if ($mostrar_pedido['productos'] != '')
		$productos_pedido = json_decode($mostrar_pedido['productos'], true);

	if (isset($productos_pedido[$num_item])) {
		if ($productos_pedido[$num_item]['estado'] == 'PREPARANDO') {
			$productos_pedido[$num_item]['estado'] = 'DESPACHADO';
			$productos_pedido[$num_item]['fecha_despachado'] = $fecha_h;
			$productos_pedido[$num_item]['despachador'] = $usuario;

			$code = $productos_pedido[$num_item]['code'];

			$productos_pedido = json_encode($productos_pedido, JSON_UNESCAPED_UNICODE);

			$sql = "UPDATE `pedidos` SET 
				`productos` = '$productos_pedido'
				WHERE codigo='$cod_pedido'";

			$verificacion = mysqli_query($conexion, $sql);

			if ($verificacion == 1) {
				if ($respuesta == 'VENTA') {
					if ($mostrar_venta[2] != '')
						$productos_venta = json_decode($mostrar_venta[2], true);

					foreach ($productos_venta as $key => $value) {
						if ($value['code'] == $code) {
							$productos_venta[$key]['estado'] = 'DESPACHADO';
							$productos_venta[$key]['fecha_despachado'] = $fecha_h;
						}
					}

					$productos_venta = json_encode($productos_venta, JSON_UNESCAPED_UNICODE);

					$sql = "UPDATE `ventas` SET
					`productos` = '$productos_venta'
					WHERE codigo='$cod_venta'";
					$verificacion = mysqli_query($conexion, $sql);
				} else {
					if ($mostrar_mesa[2] != '')
						$productos_mesa = json_decode($mostrar_mesa[2], true);

					foreach ($productos_mesa as $key => $value) {
						if ($value['code'] == $code) {
							$productos_mesa[$key]['estado'] = 'DESPACHADO';
							$productos_mesa[$key]['fecha_despachado'] = $fecha_h;
						}
					}

					$productos_mesa = json_encode($productos_mesa, JSON_UNESCAPED_UNICODE);

					$sql = "UPDATE `mesas` SET 
					`productos` = '$productos_mesa'
					WHERE cod_mesa='$cod_mesa'";

					$verificacion = mysqli_query($conexion, $sql);
				}
			}
		} else
			$verificacion = 'El producto debe estar en preparación';
	} else
		$verificacion = 'No se encontró el producto seleccionado en el pedido';
} else
	$verificacion = 'Reload';
$datos = array(
	'consulta' => $verificacion
);

echo json_encode($datos);
