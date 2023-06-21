<?php
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();

$obj = new conectar();
$conexion = $obj->conexion();
$conexion = $obj->conexion();

$fecha_h = date('Y-m-d G:i:s');

$bodega = '';
$cod_categoria = 0;
$verificacion = 1;

if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];

	$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
	$result_e = mysqli_query($conexion, $sql_e);
	$ver_e = mysqli_fetch_row($result_e);

	$rol = $ver_e[5];

	$sql_acceso = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Acceso a mesas'";
	$result_acceso = mysqli_query($conexion, $sql_acceso);
	$mostrar_acceso = mysqli_fetch_row($result_acceso);

	$acceso_mesas = $mostrar_acceso[2];

	$pos = $_POST['num_item'];
	$cod_mesa = $_POST['cod_mesa'];

	$productos_nuevos = array();

	$cantidad_descontar = 0;

	$sql_mesa = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura`, `mesero` FROM `mesas` WHERE cod_mesa = '$cod_mesa'";
	$result_mesa = mysqli_query($conexion, $sql_mesa);
	$mostrar_producto_mesa = mysqli_fetch_row($result_mesa);

	$mesero = $mostrar_producto_mesa[5];

	if ($acceso_mesas == 'CreadorVer' && $rol != 'Administrador') {
		if ($mesero != $usuario) {
			$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$mesero'";
			$result_e = mysqli_query($conexion, $sql_e);
			$ver_e = mysqli_fetch_row($result_e);
			if ($ver_e != null)
				$mesero = $ver_e[0] . ' ' . $ver_e[1];

			$verificacion = 'Para esta mesa el autorizado es <b>' . $mesero . '</b>.';
		}
	}

	if ($verificacion == 1) {
		$productos_mesa = json_decode($mostrar_producto_mesa[2], true);
		$cod_producto = $productos_mesa[$pos]['codigo'];

		$sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo='$cod_producto'";
		$result_producto = mysqli_query($conexion, $sql_producto);
		$mostrar_producto = mysqli_fetch_row($result_producto);

		$cant = $productos_mesa[$pos]['cant'];
		$cod_categoria = $mostrar_producto[5];

		$estado = $productos_mesa[$pos]['estado'];

		if ($estado != 'EN ESPERA') {
			$code = $productos_mesa[$pos]['code'];
			$cod_pedido = $productos_mesa[$pos]['cod_pedido'];
		}

		unset($productos_mesa[$pos]);

		$i = count($productos_mesa);
		if (!isset($productos_mesa[$pos]) && $i == 0)
			$productos_mesa = '';
		else {
			$j = 1;
			foreach ($productos_mesa as $i => $producto) {
				$productos_nuevos[$j] = $producto;
				$j++;
			}
		}

		if (count($productos_nuevos) == 0)
			$productos_mesa = '';
		else
			$productos_mesa = json_encode($productos_nuevos, JSON_UNESCAPED_UNICODE);

		$sql = "UPDATE `mesas` SET 
			`productos`='$productos_mesa'
			WHERE cod_mesa='$cod_mesa'";

		$verificacion = mysqli_query($conexion, $sql);

		if ($estado != 'EN ESPERA') {
			if ($verificacion == 1) {
				$sql_pedido = "SELECT `codigo`, `productos`, `mesa`, `solicitante`, `fecha_registro`, `fecha_envio`, `fecha_entrega`, `estado`, `area` FROM `pedidos` WHERE codigo = '$cod_pedido'";
				$result_pedido = mysqli_query($conexion, $sql_pedido);
				$mostrar_pedido = mysqli_fetch_array($result_pedido);

				$productos_pedido = array();
				if ($mostrar_pedido[1] != '')
					$productos_pedido = json_decode($mostrar_pedido[1], true);

				foreach ($productos_pedido as $i => $producto) {
					if ($producto['code'] == $code) {
						if ($productos_pedido[$i]['estado'] == 'PENDIENTE') {
							$productos_pedido[$i]['estado'] = 'CANCELADO';
							$productos_pedido[$i]['fecha_cancelado'] = $fecha_h;
							$productos_pedido[$i]['cancelador'] = $usuario;

							$productos_pedido = json_encode($productos_pedido, JSON_UNESCAPED_UNICODE);

							$sql = "UPDATE `pedidos` SET 
						`productos` = '$productos_pedido'
						WHERE codigo='$cod_pedido'";

							$verificacion = mysqli_query($conexion, $sql);
						} else {
							if ($rol == 'Administrador') {
								$productos_pedido[$i]['estado'] = 'CANCELADO';
								$productos_pedido[$i]['fecha_cancelado'] = $fecha_h;
								$productos_pedido[$i]['cancelador'] = $usuario;

								$productos_pedido = json_encode($productos_pedido, JSON_UNESCAPED_UNICODE);

								$sql = "UPDATE `pedidos` SET 
							`productos` = '$productos_pedido'
							WHERE codigo='$cod_pedido'";

								$verificacion = mysqli_query($conexion, $sql);
							} else
								$verificacion = 'EL pedido YA está ' . $productos_pedido[$i]['estado'] . '. Solo los Administradores pueden cancelar pedidos [' . $productos_pedido[$i]['estado'] . ']';
						}
					}
				}
			}
		}

		if ($verificacion == 1) {
			if ($mostrar_producto[9] == 'Producto') {
				$inventario = $mostrar_producto[4];
				$inventario += $cant;

				$sql = "UPDATE `productos` SET 
		`inventario`='$inventario'
		WHERE codigo='$cod_producto'";
				$verificacion = mysqli_query($conexion, $sql);
			}
		}
	}
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion,
	'cod_categoria' => $cod_categoria
);

echo json_encode($datos);
