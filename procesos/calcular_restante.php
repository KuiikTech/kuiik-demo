<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();
$conexion = $obj_2->conexion();
$conexion = $obj_2->conexion();

$msj = '';

$fecha_h = date('Y-m-d G:i:s');
if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];

	$verificacion = 1;

	$tipo_devolucion = $_POST['tipo_devolucion'];
	$producto_devolucion = $_POST['producto_devolucion'];
	$cant_producto = $_POST['cant_producto'];
	$producto_cambio = $_POST['producto_cambio'];
	$cant_cambio = $_POST['cant_cambio'];
	$valor_diferente = str_replace('.', '', $_POST['valor_diferente']);
	$valor_diferente_cambio = str_replace('.', '', $_POST['valor_diferente_cambio']);

	if ($tipo_devolucion == '')
		$verificacion = 'Seleccione el tipo';

	if ($verificacion == 1) {
		if ($tipo_devolucion == 'Cambio') {
			if ($cant_producto != '' && $cant_cambio != '' && $producto_devolucion != '' && $producto_cambio != '') {
				$producto_devolucion = explode('/', $producto_devolucion);
				$cod_producto = $producto_devolucion[0];
				$num_inventario = $producto_devolucion[1];

				$total_devolucion = 0;

				$sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo='$cod_producto'";
				$result_producto = mysqli_query($conexion, $sql_producto);
				$mostrar_producto = mysqli_fetch_row($result_producto);

				if (isset($_SESSION['usuario_restaurante2']))
					$bodega = 'PDV_2';
				else
					$bodega = 'PDV_1';

				$inventario = array();
				if ($bodega == 'PDV_1') {
					$bodega_inventario = 'inventario_1';
					if ($mostrar_producto[6] != '')
						$inventario = json_decode($mostrar_producto[6], true);
				} else if ($bodega == 'PDV_2') {
					$bodega_inventario = 'inventario_2';
					if ($mostrar_producto[7] != '')
						$inventario = json_decode($mostrar_producto[7], true);
				}

				if ($valor_diferente == '')
					$total_devolucion = $inventario[$num_inventario]['valor_venta'] * $cant_producto;
				else
					$total_devolucion = $valor_diferente * $cant_producto;

				$producto_cambio = explode('/', $producto_cambio);
				$cod_producto = $producto_cambio[0];
				$num_inventario = $producto_cambio[1];

				$sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo='$cod_producto'";
				$result_producto = mysqli_query($conexion, $sql_producto);
				$mostrar_producto = mysqli_fetch_row($result_producto);

				$inventario = array();
				if ($bodega == 'PDV_1') {
					$bodega_inventario = 'inventario_1';
					if ($mostrar_producto[6] != '')
						$inventario = json_decode($mostrar_producto[6], true);
				} else if ($bodega == 'PDV_2') {
					$bodega_inventario = 'inventario_2';
					if ($mostrar_producto[7] != '')
						$inventario = json_decode($mostrar_producto[7], true);
				}

				if ($inventario[$num_inventario]['stock'] < $cant_cambio)
					$msj = 'EL inventario maximo para el producto ' . $mostrar_producto[1] . ' es:' . $inventario[$num_inventario]['stock'];
				else {

					if ($valor_diferente_cambio == '')
						$total_cambio = $inventario[$num_inventario]['valor_venta'] * $cant_cambio;
					else
						$total_cambio = $valor_diferente_cambio * $cant_cambio;

					if ($total_cambio < $total_devolucion) {
						$restante = $total_devolucion - $total_cambio;
						$msj = 'Debe devolver $' . number_format($restante, 0, '.', '.') . ' por diferencia de precios';
					} else {
						if ($total_cambio != $total_devolucion) {
							$restante = $total_cambio - $total_devolucion;
							$msj = 'Debe recibir $' . number_format($restante, 0, '.', '.') . ' por diferencia de precios';
						}
					}
				}
			}
		}

		if ($tipo_devolucion == 'Devolución') {
			if ($producto_devolucion != '' && $cant_producto != '') {
				$producto_devolucion = explode('/', $producto_devolucion);
				$cod_producto = $producto_devolucion[0];
				$num_inventario = $producto_devolucion[1];

				$total_devolucion = 0;

				$sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo='$cod_producto'";
				$result_producto = mysqli_query($conexion, $sql_producto);
				$mostrar_producto = mysqli_fetch_row($result_producto);

				if (isset($_SESSION['usuario_restaurante2']))
					$bodega = 'PDV_2';
				else
					$bodega = 'PDV_1';

				$inventario = array();
				if ($bodega == 'PDV_1') {
					$bodega_inventario = 'inventario_1';
					if ($mostrar_producto[6] != '')
						$inventario = json_decode($mostrar_producto[6], true);
				} else if ($bodega == 'PDV_2') {
					$bodega_inventario = 'inventario_2';
					if ($mostrar_producto[7] != '')
						$inventario = json_decode($mostrar_producto[7], true);
				}

				$total_devolucion = $inventario[$num_inventario]['valor_venta'] * $cant_producto;

				$msj = 'Debe entregar $' . number_format($total_devolucion, 0, '.', '.') . ' por el valor del producto seleccionado';
			}
		}
	}
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion,
	'msj' => $msj
);

echo json_encode($datos);
