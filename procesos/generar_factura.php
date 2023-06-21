<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
require_once('../vendors/fpdf182/fpdf.php');
$obj = new crud();
$obj_2 = new conectar();
$conexion = $obj_2->conexion();
$fecha_h = date('Y-m-d G:i:s');

$cod_factura = 0;

if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];

	require_once "../clases/permisos.php";
	$obj_permisos = new permisos();
	$acceso = $obj_permisos->buscar_permiso($usuario, 'Facturas', 'CREAR');

	if ($acceso == 'SI') {
		$verificacion = 1;

		if (!isset($_SESSION['items_factura']) && !isset($_POST['cod_venta']))
			$verificacion = 'No existen items agregados.';
		if (!isset($_SESSION['cod_cliente_fact']) && !isset($_POST['cod_venta']))
			$verificacion = 'Seleccione el cliente.';

		if ($verificacion == 1) {
			$sql_resolucion = "SELECT `codigo`, `prefijo`, `sufijo`, `inicio`, `fin`, `actual`, `fecha_resolucion`, `numero`, `fecha_registro` FROM `resoluciones` WHERE estado = 'ACTIVO'";
			$result_resolucion = mysqli_query($conexion, $sql_resolucion);
			$ver_resolucion = mysqli_fetch_row($result_resolucion);

			if ($ver_resolucion != NULL) {
				$actual = $ver_resolucion[5];
				$fin = $ver_resolucion[4];
				$inicio = $ver_resolucion[3];

				if ($actual < $fin) {
					$numero = $actual + 1;

					$config = array(
						'numero' => $numero,
						'resolucion' => $ver_resolucion[7],
						'prefijo' => $ver_resolucion[1],
						'sufijo' => $ver_resolucion[2],
						'inicio' => $inicio,
						'fin' => $fin,
						'fecha_resolucion' => $ver_resolucion[6]
					);

					if (isset($_POST['cod_venta'])) {
						$items_factura = array();
						$cod_venta = $_POST['cod_venta'];
						$cod_cliente = $_POST['cod_cliente'];

						$sql = "SELECT `codigo`, `cliente`, `productos`, `pago`, `fecha`, `cobrador` FROM `ventas` WHERE codigo = '$cod_venta'";
						$result = mysqli_query($conexion, $sql);
						$mostrar = mysqli_fetch_row($result);

						$total = 0;

						$cliente = json_decode($mostrar[1], true);
						$productos_venta = json_decode($mostrar[2], true);
						$pagos = json_decode($mostrar[3], true);

						foreach ($productos_venta as $i => $producto) {
							$items_factura[$i]['descripcion'] = $producto['descripcion'];
							$items_factura[$i]['cant'] = $producto['cant'];
							$items_factura[$i]['valor_unitario'] = $producto['valor_unitario'];
							if (isset($producto['impuesto']))
								$items_factura[$i]['impuesto'] = $producto['impuesto'];
							else
								$items_factura[$i]['impuesto'] = 0;
						}

						$pagos = json_encode($pagos, JSON_UNESCAPED_UNICODE);

						//$cod_cliente = $cliente['codigo'];
					} else {
						$items_factura = $_SESSION['items_factura'];
						$cod_cliente = $_SESSION['cod_cliente_fact'];
						$pagos = '';
					}

					if ($cod_cliente != '') {
						$sql_cliente = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro`, `tipo`, `info` FROM `clientes` WHERE codigo = '$cod_cliente'";
						$result_cliente = mysqli_query($conexion, $sql_cliente);
						$ver_cliente = mysqli_fetch_row($result_cliente);

						$cliente = array(
							'codigo' => $cod_cliente,
							'cedula' => $ver_cliente[1],
							'nombre' => $ver_cliente[2] . ' ' . $ver_cliente[3],
							'telefono' => $ver_cliente[4]
						);
					} else {
						$cliente = array(
							'codigo' => 0,
							'cedula' => '0',
							'nombre' => 'Ventas Diarias',
							'telefono' => 0
						);
					}

					$cliente = json_encode($cliente, JSON_UNESCAPED_UNICODE);
					$items_factura = json_encode($items_factura, JSON_UNESCAPED_UNICODE);
					$config = json_encode($config, JSON_UNESCAPED_UNICODE);

					$sql = "INSERT INTO `facturas`(`cliente`, `items`, `config`, `fecha_registro`, `creador`, `pagos`) VALUES (
						'$cliente',
						'$items_factura',
						'$config',
						'$fecha_h',
						'$usuario',
						'$pagos')";

					$verificacion = mysqli_query($conexion, $sql);

					if ($verificacion == 1 && !isset($_POST['cod_venta'])) {
						unset($_SESSION['items_factura']);
						unset($_SESSION['cod_cliente_fact']);
					}

					if ($verificacion == 1) {
						$sql = "SELECT MAX(codigo)
						as codigo  from facturas";
						$result = mysqli_query($conexion, $sql);
						$ver = mysqli_fetch_row($result);
						$cod_factura = $ver[0];

						$sql = "UPDATE `resoluciones` SET 
						`actual`='$numero'
						WHERE estado = 'ACTIVO'";

						$verificacion = mysqli_query($conexion, $sql);
					}
				} else
					$verificacion = 'El número de factura ya llegó a su limite. por favor ingrese una nueva resolución.';
			} else
				$verificacion = 'No existe ninguna resolución de facturación activa.';
		}
	} else
		$verificacion = 'Usted no tiene permisos para crear facturas';
} else
	$verificacion = 'Reload';


$datos = array(
	'consulta' => $verificacion,
	'cod_factura' => $cod_factura
);

echo json_encode($datos);
