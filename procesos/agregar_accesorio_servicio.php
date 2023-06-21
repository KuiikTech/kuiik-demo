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

	require_once "../clases/permisos.php";
	$obj_permisos = new permisos();
	$acceso = $obj_permisos->buscar_permiso($usuario, 'PDV', 'AGREGAR');

	if ($acceso == 'SI') {
		$caja = 1;
		if (isset($_SESSION['caja_restaurante']))
			$caja = $_SESSION['caja_restaurante'];

		$cod_producto = $_POST['cod_producto'];
		$num_inventario = $_POST['num_inventario'];
		$cod_servicio = $_POST['cod_servicio'];
		$cant = $_POST['cant'];

		if (isset($_SESSION['usuario_restaurante2']))
			$bodega = 'PDV_2';
		else
			$bodega = 'PDV_1';

		$sql_servicio = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro` FROM `servicios` WHERE codigo = '$cod_servicio'";
		$result_servicio = mysqli_query($conexion, $sql_servicio);
		$mostrar_servicio = mysqli_fetch_row($result_servicio);

		$sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo='$cod_producto'";
		$result_producto = mysqli_query($conexion, $sql_producto);
		$mostrar_producto = mysqli_fetch_row($result_producto);

		$nombre_producto = $mostrar_producto[1];
		$cod_categoria = $mostrar_producto[2];

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

		if (isset($inventario[$num_inventario])) {
			$stock = $inventario[$num_inventario]['stock'];
			$valor_unitario = $inventario[$num_inventario]['valor_venta'];
			$costo_unitario = $inventario[$num_inventario]['costo'];
		} else
			$verificacion = 'No se encontró el inventario seleccionado';

		if ($verificacion == 1) {
			$pos = 1;
			$accesorios_servicio = array();

			if ($mostrar_servicio[6] != '') {
				$accesorios_servicio = json_decode($mostrar_servicio[6], true);
				$pos += count($accesorios_servicio);
			}

			foreach ($accesorios_servicio as $a => $producto) {
				if ($cod_producto == $producto['codigo']) {
					if ($num_inventario == $producto['num_inv']) {
						$accesorios_servicio[$a]['cant'] += $cant;
						$encontrado = 1;
						//$inventario[$num_inventario]['stock'] -=$cant;
					}
				}
			}

			if (!isset($encontrado)) {
				$accesorios_servicio[$pos]['codigo'] = $cod_producto;
				$accesorios_servicio[$pos]['cant'] = $cant;
				$accesorios_servicio[$pos]['descripcion'] = $mostrar_producto[1];
				$accesorios_servicio[$pos]['num_inv'] = $num_inventario;
				$accesorios_servicio[$pos]['valor_unitario'] = $valor_unitario;
				$accesorios_servicio[$pos]['costo_unitario'] = $costo_unitario;
				$inventario[$num_inventario]['stock'] -= $cant;
			}

			if ($cant > $stock)
				$verificacion = 'El inventario para ' . $mostrar_producto[1] . ' es: ' . $stock;

			if ($verificacion == 1) {
				$pos = 1;
				if (isset($inventario[$num_inventario])) {
					if (isset($inventario[$num_inventario]['movimientos']))
						$pos += count($inventario[$num_inventario]['movimientos']);
					$inventario[$num_inventario]['movimientos'][$pos] = array(
						'Tipo' => 'Salida por servicio # ' . $cod_servicio . ' (Caja ' . $caja . ')',
						'Cant' => '-' . $cant,
						'creador' => $usuario,
						'Observaciones' => '',
						'fecha' => $fecha_h
					);
				}
			}

			if ($verificacion == 1) {
				$inventario = json_encode($inventario, JSON_UNESCAPED_UNICODE);
				$sql = "UPDATE `productos` SET 
				`$bodega_inventario`='$inventario'
				WHERE codigo='$cod_producto'";
				$verificacion = mysqli_query($conexion, $sql);
			}

			if ($verificacion == 1) {
				$accesorios_servicio = json_encode($accesorios_servicio, JSON_UNESCAPED_UNICODE);
				$sql = "UPDATE `servicios` SET 
				`accesorios`='$accesorios_servicio'
				WHERE codigo='$cod_servicio'";

				$verificacion = mysqli_query($conexion, $sql);
			}

			if ($verificacion == 1) {
				$operacion = 'Subida de accesorio -> ' . $mostrar_producto[1] . ' (Cod: ' . $mostrar_producto[0] . ')';

				$info_respaldo = json_encode($info_respaldo, JSON_UNESCAPED_UNICODE);
				$sql = "INSERT INTO `respaldo_info`(`cod_servicio`, `informacion`, `operacion`, `usuario`, `fecha_registro`) VALUES (
						'$cod_servicio',
						'$info_respaldo',
						'$operacion',
						'$usuario',
						'$fecha_h')";

				mysqli_query($conexion, $sql);
			}
		}
	} else
		$verificacion = 'No tienes permisos para agregar productos en Punto de Venta';
} else
	$verificacion = 'Reload';


$datos = array(
	'consulta' => $verificacion,
	'cod_categoria' => $cod_categoria
);

echo json_encode($datos);