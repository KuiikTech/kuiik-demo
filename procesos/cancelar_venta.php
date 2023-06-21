<?php
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();

$obj = new conectar();
$conexion = $obj->conexion();
$conexion = $obj->conexion();

$fecha_h = date('Y-m-d G:i:s');
$cod_categoria = 0;
$verificacion = 1;

if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];
	$bodega = '';

	$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
	$result_e = mysqli_query($conexion, $sql_e);
	$ver_e = mysqli_fetch_row($result_e);

	$rol = $ver_e[5];

	$sql_acceso = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Acceso a mesas'";
	$result_acceso = mysqli_query($conexion, $sql_acceso);
	$mostrar_acceso = mysqli_fetch_row($result_acceso);

	$acceso_mesas = $mostrar_acceso[2];

	$cod_mesa = $_POST['cod_mesa'];

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
		$productos_mesa = array();
		if ($mostrar_producto_mesa[2] != '')
			$productos_mesa = json_decode($mostrar_producto_mesa[2], true);

		if ($rol != 'Administrador') {
			foreach ($productos_mesa as $pos => $producto) {
				if ($producto['estado'] != 'EN ESPERA' && $producto['estado'] != 'CANCELADO' && $producto['estado'] != 'PENDIENTE')
					$verificacion = 'No se puede cancelar la venta, existen productos en proceso. Solo los Administradores pueden cancelar la pedidos en proceso.';
			}
		}
		if ($verificacion == 1) {
			foreach ($productos_mesa as $pos => $producto) {
				$cod_producto = $producto['codigo'];

				$sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo='$cod_producto'";
				$result_producto = mysqli_query($conexion, $sql_producto);
				$mostrar_producto = mysqli_fetch_row($result_producto);

				$cant = $producto['cant'];
				$cod_categoria = $mostrar_producto[5];

				$estado = $producto['estado'];

				if ($estado != 'EN ESPERA') {
					$code = $producto['code'];
					$cod_pedido = $producto['cod_pedido'];

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
		}

		if ($verificacion == 1) {
			$sql = "UPDATE `mesas` SET 
			`productos`='',
			`estado`='LIBRE',
			`cod_cliente`= NULL,
			`fecha_apertura`= NULL,
			`pagos`= ''
			WHERE cod_mesa='$cod_mesa'";

			$verificacion = mysqli_query($conexion, $sql);
		}
	}
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion
);

echo json_encode($datos);
