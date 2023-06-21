<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();
$conexion = $obj_2->conexion();
$conexion = $obj_2->conexion();

require_once "../clases/permisos.php";
$obj_permisos = new permisos();

$fecha_h = date('Y-m-d G:i:s');
if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];

	if (isset($_SESSION['caja_restaurante']))
		$caja = $_SESSION['caja_restaurante'];
	else
		$caja = 1;

	$verificacion = 1;

	$acceso = $obj_permisos->buscar_permiso($usuario, 'Devoluciones', 'GENERAR');

	if ($acceso == 'SI') {
		$tipo_devolucion = $_POST['tipo_devolucion'];
		$descripcion_devolucion = $_POST['descripcion_devolucion'];
		$producto_devolucion = $_POST['producto_devolucion'];
		$cant_producto = $_POST['cant_producto'];
		$producto_cambio = $_POST['producto_cambio'];
		$cant_cambio = $_POST['cant_cambio'];
		$valor_diferente = str_replace('.', '', $_POST['valor_diferente']);
		$valor_diferente_cambio = str_replace('.', '', $_POST['valor_diferente_cambio']);

		$sumar_inventario = $_POST['sumar_inventario'];

		if ($descripcion_devolucion == '')
			$verificacion = 'Escriba una descripción para la devolución';

		if ($tipo_devolucion == 'Cambio') {
			if ($cant_cambio == '')
				$verificacion = 'Ingrese la cantidad de cambio';
			if ($producto_cambio == '')
				$verificacion = 'Seleccione un producto';
			if ($cant_producto == '')
				$verificacion = 'Ingrese la cantidad de devolución';
			if ($producto_devolucion == '')
				$verificacion = 'Seleccione un producto';
			if ($descripcion_devolucion == '')
				$verificacion = 'Escriba una descripción para la devolución';
		} else if ($tipo_devolucion == 'Devolución') {
			if ($cant_producto == '')
				$verificacion = 'Ingrese la cantidad de devolución';
			if ($producto_devolucion == '')
				$verificacion = 'Seleccione un producto';
		} else
			$verificacion = 'Seleccione el tipo';

		if ($verificacion == 1) {
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

			if (isset($_SESSION['usuario_restaurante2']))
				$local = 'Restaurante 2';
			else
				$local = 'Restaurante 1';

			$inventario = array();
			$pos_1 = 1;
			if ($bodega == 'PDV_1') {
				$bodega_inventario = 'inventario_1';
				if ($mostrar_producto[6] != '') {
					$inventario = json_decode($mostrar_producto[6], true);
					$pos_1 += count($inventario[$num_inventario]['movimientos']);
				}
			} else if ($bodega == 'PDV_2') {
				$bodega_inventario = 'inventario_2';
				if ($mostrar_producto[7] != '') {
					$inventario = json_decode($mostrar_producto[7], true);
					$pos_1 += count($inventario[$num_inventario]['movimientos']);
				}
			}

			if ($valor_diferente == '') {
				$total_devolucion = $inventario[$num_inventario]['valor_venta'] * $cant_producto;
				$valor_1 = $inventario[$num_inventario]['valor_venta'];
			} else {
				$total_devolucion = $valor_diferente * $cant_producto;
				$valor_1 = $valor_diferente;
			}

			if ($tipo_devolucion == 'Devolución') {
				if ($caja == 1)
					$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `egresos`, `creador`, `cajero`, `estado` FROM `caja` WHERE estado = 'ABIERTA'";
				else if ($caja == 2)
					$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `egresos`, `creador`, `cajero`, `estado` FROM `caja2` WHERE estado = 'ABIERTA'";
				else
					$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `egresos`, `creador`, `cajero`, `estado` FROM `caja3` WHERE estado = 'ABIERTA'";
				$result = mysqli_query($conexion, $sql);
				$mostrar = mysqli_fetch_row($result);

				if ($mostrar != NULL) {
					$producto_devolucion = array(
						'codigo' => $mostrar_producto[0],
						'descripcion' => $mostrar_producto[1],
						'num_inv' => $num_inventario,
						'valor_unitario' => $valor_1,
						'cant' => $cant_producto
					);

					if ($sumar_inventario == 1)
						$inventario[$num_inventario]['stock'] += $cant_producto;

					$producto_devolucion = json_encode($producto_devolucion, JSON_UNESCAPED_UNICODE);

					$sql = "INSERT INTO `devolucion`(`descripcion`, `producto`, `creador`, `fecha_registro`) VALUES (
						'$descripcion_devolucion',
						'$producto_devolucion',
						'$usuario',
						'$fecha_h')";

					$verificacion = mysqli_query($conexion, $sql);

					if ($verificacion == 1) {
						$sql = "SELECT MAX(codigo) from devolucion";
						$result = mysqli_query($conexion, $sql);
						$ver = mysqli_fetch_row($result);

						$cod_devolucion = $ver[0];

						$cod_caja = $mostrar[0];

						$egresos = array();
						$pos = 1;
						if ($mostrar[10] != '') {
							$egresos = json_decode($mostrar[10], true);
							$pos += count($egresos);
						}

						$egresos[$pos]["concepto"] = 'Devolución de dinero (Devolución # ' . $cod_devolucion . ')';
						$egresos[$pos]["valor"] = $total_devolucion;
						$egresos[$pos]["fecha"] = $fecha_h;
						$egresos[$pos]["creador"] = $usuario;

						$egresos = json_encode($egresos, JSON_UNESCAPED_UNICODE);

						if ($caja == 1) {
							$sql = "UPDATE `caja` SET 
							`egresos`='$egresos'
							WHERE codigo='$cod_caja'";
						} else if ($caja == 2) {
							$sql = "UPDATE `caja2` SET 
							`egresos`='$egresos'
							WHERE codigo='$cod_caja'";
						} else {
							$sql = "UPDATE `caja3` SET 
							`egresos`='$egresos'
							WHERE codigo='$cod_caja'";
						}

						$verificacion = mysqli_query($conexion, $sql);

						if ($verificacion == 1) {

							$inventario[$num_inventario]['movimientos'][$pos] = array(
								'Tipo' => 'Retorno por Devolución (Caja ' . $caja . ') [' . $local . ']',
								'Cant' => '+' . $producto['cant'],
								'creador' => $usuario,
								'Observaciones' => '',
								'fecha' => $fecha_h
							);

							$inventario = json_encode($inventario, JSON_UNESCAPED_UNICODE);
							$sql = "UPDATE `productos` SET 
							`$bodega_inventario`='$inventario'
							WHERE codigo='$cod_producto'";
							$verificacion = mysqli_query($conexion, $sql);
						}
					}
				} else
					$verificacion = 'No se pueden procesar la devolución porque la caja NO se encuentra abierta';
			} else {
				$producto_cambio = explode('/', $producto_cambio);
				$cod_producto_2 = $producto_cambio[0];
				$num_inventario_2 = $producto_cambio[1];

				$sql_producto_2 = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo='$cod_producto_2'";
				$result_producto_2 = mysqli_query($conexion, $sql_producto_2);
				$mostrar_producto_2 = mysqli_fetch_row($result_producto_2);

				$inventario_2 = array();
				$pos_2 = 1;
				if ($bodega == 'PDV_1') {
					$bodega_inventario_2 = 'inventario_1';
					if ($mostrar_producto[6] != '') {
						$inventario_2 = json_decode($mostrar_producto_2[6], true);
						$pos_2 += count($inventario_2[$num_inventario_2]['movimientos']);
					}
				} else if ($bodega == 'PDV_2') {
					$bodega_inventario_2 = 'inventario_2';
					if ($mostrar_producto[7] != '') {
						$inventario_2 = json_decode($mostrar_producto_2[7], true);
						$pos_2 += count($inventario_2[$num_inventario_2]['movimientos']);
					}
				}

				if ($inventario_2[$num_inventario_2]['stock'] < $cant_cambio)
					$verificacion = 'EL inventario maximo para el producto ' . $mostrar_producto_2[1] . ' es:' . $inventario_2[$num_inventario_2]['stock'];
				else {
					if ($caja == 1)
						$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `egresos` FROM `caja` WHERE estado = 'ABIERTA'";
					else if ($caja == 2)
						$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `egresos` FROM `caja2` WHERE estado = 'ABIERTA'";
					else
						$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `egresos` FROM `caja3` WHERE estado = 'ABIERTA'";

					$result = mysqli_query($conexion, $sql);
					$mostrar = mysqli_fetch_row($result);

					if ($mostrar != NULL) {
						if ($sumar_inventario == 1)
							$inventario[$num_inventario]['stock'] += $cant_producto;

						if ($valor_diferente_cambio == '') {
							$total_cambio = $inventario_2[$num_inventario_2]['valor_venta'] * $cant_cambio;
							$valor_2 = $inventario_2[$num_inventario_2]['valor_venta'];
						} else {
							$total_cambio = $valor_diferente_cambio * $cant_cambio;
							$valor_2 = $valor_diferente_cambio;
						}

						$cod_caja = $mostrar[0];
						$producto_devolucion = array(
							'codigo' => $mostrar_producto[0],
							'descripcion' => $mostrar_producto[1],
							'num_inv' => $num_inventario,
							'valor_unitario' => $valor_1,
							'cant' => $cant_producto
						);

						$producto_cambio = array(
							'codigo' => $mostrar_producto_2[0],
							'descripcion' => $mostrar_producto_2[1],
							'num_inv' => $num_inventario_2,
							'valor_unitario' => $valor_2,
							'cant' => $cant_cambio
						);

						$producto_devolucion = json_encode($producto_devolucion, JSON_UNESCAPED_UNICODE);
						$producto_cambio = json_encode($producto_cambio, JSON_UNESCAPED_UNICODE);

						$sql = "INSERT INTO `devolucion`(`descripcion`, `producto`, `cambio`, `creador`, `fecha_registro`) VALUES (
							'$descripcion_devolucion',
							'$producto_devolucion',
							'$producto_cambio',
							'$usuario',
							'$fecha_h')";

						$verificacion = mysqli_query($conexion, $sql);

						if ($verificacion == 1) {

							$sql = "SELECT MAX(codigo) from devolucion";
							$result = mysqli_query($conexion, $sql);
							$ver = mysqli_fetch_row($result);

							$cod_devolucion = $ver[0];

							if ($total_cambio > $total_devolucion) {
								$restante = $total_cambio - $total_devolucion;

								$ingresos = array();
								$pos = 1;
								if ($mostrar[9] != NULL)
									$ingresos = json_decode($mostrar[9], true);
								$pos += count($ingresos);

								$ingresos[$pos]['descripcion'] = 'Pago excedente (Devolución/Cambio #' . $cod_devolucion . ')';
								$ingresos[$pos]['valor'] = $restante;
								$ingresos[$pos]['fecha'] = $fecha_h;

								$ingresos = json_encode($ingresos, JSON_UNESCAPED_UNICODE);
								if ($caja == 1) {
									$sql = "UPDATE `caja` SET 
									`ingresos`='$ingresos'
									WHERE codigo='$cod_caja'";
								} else if ($caja == 2) {
									$sql = "UPDATE `caja2` SET 
									`ingresos`='$ingresos'
									WHERE codigo='$cod_caja'";
								} else {
									$sql = "UPDATE `caja3` SET 
									`ingresos`='$ingresos'
									WHERE codigo='$cod_caja'";
								}

								$verificacion = mysqli_query($conexion, $sql);
							} else {
								$restante = $total_devolucion - $total_cambio;

								$egresos = array();
								$pos = 1;
								if ($mostrar[13] != '') {
									$egresos = json_decode($mostrar[13], true);
									$pos += count($egresos);
								}

								$egresos[$pos]['concepto'] = 'Devolución excedente (Devolución/Cambio #' . $cod_devolucion . ')';
								$egresos[$pos]['valor'] = $restante;
								$egresos[$pos]['fecha'] = $fecha_h;

								$egresos = json_encode($egresos, JSON_UNESCAPED_UNICODE);
								if ($caja == 1) {
									$sql = "UPDATE `caja` SET 
									`egresos`='$egresos'
									WHERE codigo='$cod_caja'";
								} else if ($caja == 2) {
									$sql = "UPDATE `caja2` SET 
									`egresos`='$egresos'
									WHERE codigo='$cod_caja'";
								} else {
									$sql = "UPDATE `caja3` SET 
									`egresos`='$egresos'
									WHERE codigo='$cod_caja'";
								}

								$verificacion = mysqli_query($conexion, $sql);
							}

							$inventario[$num_inventario]['movimientos'][$pos_1] = array(
								'Tipo' => 'Retorno por Devolución (Caja ' . $caja . ') [' . $local . ']',
								'Cant' => '+' . $cant_producto,
								'creador' => $usuario,
								'Observaciones' => 'Devolución # ' . $cod_devolucion . ' - V/u: ' . number_format($valor_1, 2, '.', ','),
								'fecha' => $fecha_h
							);

							$inventario_2[$num_inventario_2]['movimientos'][$pos_2] = array(
								'Tipo' => 'Salida por Devolución (Caja ' . $caja . ') [' . $local . ']',
								'Cant' => '-' . $cant_cambio,
								'creador' => $usuario,
								'Observaciones' => 'Devolución # ' . $cod_devolucion . ' - V/u: ' . number_format($valor_2, 2, '.', ','),
								'fecha' => $fecha_h
							);

							if ($verificacion == 1) {
								$inventario = json_encode($inventario, JSON_UNESCAPED_UNICODE);
								$sql = "UPDATE `productos` SET 
								`$bodega_inventario`='$inventario'
								WHERE codigo='$cod_producto'";
								$verificacion = mysqli_query($conexion, $sql);
							}

							if ($verificacion == 1) {
								$inventario_2[$num_inventario_2]['stock'] -= $cant_cambio;
								$inventario_2 = json_encode($inventario_2, JSON_UNESCAPED_UNICODE);
								$sql = "UPDATE `productos` SET 
								`$bodega_inventario_2`='$inventario_2'
								WHERE codigo='$cod_producto_2'";
								$verificacion = mysqli_query($conexion, $sql);
							}
						}
					} else
						$verificacion = 'No se pueden procesar la devolución/cambio porque la caja NO se encuentra abierta';
				}
			}
		}
	} else
		$verificacion = 'No tiene permisos para generar devoluciones';
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion
);

echo json_encode($datos);
