<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();
$conexion = $obj_2->conexion();
$conexion = $obj_2->conexion();

$fecha_h = date('Y-m-d G:i:s');

$verificacion = 1;
$cod_categoria = 0;
$bodega = '';

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

	$sql_stock = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Validar Stock'";
	$result_stock = mysqli_query($conexion, $sql_stock);
	$mostrar_stock = mysqli_fetch_row($result_stock);

	$validar_stock = $mostrar_stock[2];

	$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado` FROM `caja` WHERE estado = 'ABIERTA'";
	$result = mysqli_query($conexion, $sql);
	$mostrar = mysqli_fetch_row($result);

	if ($mostrar != NULL) {
		$cod_producto = $_POST['cod_producto'];
		$cod_mesa = $_POST['cod_mesa'];
		$cant = $_POST['cant'];

		$sql_mesa = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura`, `mesero` FROM `mesas` WHERE cod_mesa = '$cod_mesa'";
		$result_mesa = mysqli_query($conexion, $sql_mesa);
		$mostrar_mesa = mysqli_fetch_row($result_mesa);

		$mesero = $mostrar_mesa[5];

		if ($acceso_mesas == 'CreadorVer' && $rol != 'Administrador') {
			if ($mesero != $usuario) {
				$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$mesero'";
				$result_e = mysqli_query($conexion, $sql_e);
				$ver_e = mysqli_fetch_row($result_e);
				if ($ver_e != null)
					$mesero = $ver_e[0] . ' ' . $ver_e[1];

				$verificacion = 'Para esta mesa el autorizado es <b>' . $mesero . '</b>.';
			} else
				$verificacion = 1;
		} else
			$verificacion = 1;

		if ($verificacion == 1) {
			$sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo='$cod_producto'";
			$result_producto = mysqli_query($conexion, $sql_producto);
			$mostrar_producto = mysqli_fetch_row($result_producto);

			$nombre_producto = $mostrar_producto[1];
			$cod_categoria = $mostrar_producto[5];
			$inventario = $mostrar_producto[4];
			$stock = $inventario;
			$valor_unitario = $mostrar_producto[3];
			$area = $mostrar_producto[8];

			$pos = 1;
			$productos_mesa = array();

			if ($mostrar_mesa[2] != '') {
				$productos_mesa = json_decode($mostrar_mesa[2], true);
				$pos += count($productos_mesa);
			}

			foreach ($productos_mesa as $a => $producto) {
				if ($cod_producto == $producto['codigo']) {
					if ($producto['estado'] == 'EN ESPERA') {
						if ($producto['valor_unitario'] == $valor_unitario) {
							$productos_mesa[$a]['cant'] += $cant;
							$encontrado = 1;
							$inventario -= $cant;
							break;
						}
					}
				}
			}

			if (!isset($encontrado)) {
				$productos_mesa[$pos]['codigo'] = $cod_producto;
				$productos_mesa[$pos]['cant'] = $cant;
				$productos_mesa[$pos]['descripcion'] = $mostrar_producto[1];
				$productos_mesa[$pos]['valor_unitario'] = $valor_unitario;
				$productos_mesa[$pos]['estado'] = 'EN ESPERA';
				$productos_mesa[$pos]['area'] = $area;
				$productos_mesa[$pos]['fecha_registro'] = $fecha_h;
				$productos_mesa[$pos]['creador'] = $usuario;
				$inventario -= $cant;
			}

			if ($mostrar_producto[9] == 'Producto') {
				if ($validar_stock == 'SI') {
					if ($cant > $stock)
						$verificacion = 'El inventario para ' . $mostrar_producto[1] . ' es: ' . $stock;
				}

				if ($verificacion == 1) {
					$inventario = json_encode($inventario, JSON_UNESCAPED_UNICODE);
					$sql = "UPDATE `productos` SET 
					`inventario`='$inventario'
					WHERE codigo='$cod_producto'";
					$verificacion = mysqli_query($conexion, $sql);
				}
			}

			if ($verificacion == 1) {
				$productos_mesa = json_encode($productos_mesa, JSON_UNESCAPED_UNICODE);
				$sql = "UPDATE `mesas` SET 
				`productos`='$productos_mesa'
				WHERE cod_mesa='$cod_mesa'";

				$verificacion = mysqli_query($conexion, $sql);
			}
		}
	} else
		$verificacion = 'No se pueden agregar productos porque la caja NO se encuentra abierta';
} else
	$verificacion = 'Reload';


$datos = array(
	'consulta' => $verificacion,
	'cod_categoria' => $cod_categoria
);

echo json_encode($datos);
