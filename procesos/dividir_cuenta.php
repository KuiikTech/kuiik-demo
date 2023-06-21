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

if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];

	$sql_sistema = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Tipo Sistema'";
	$result_sistema = mysqli_query($conexion, $sql_sistema);
	$mostrar_sistema = mysqli_fetch_row($result_sistema);

	$tipo_sistema = $mostrar_sistema[2];

	require_once "../clases/permisos.php";
	$obj_permisos = new permisos();
	$acceso = $obj_permisos->buscar_permiso($usuario, 'PDV', 'PROCESAR');

	if ($acceso == 'SI') {
		$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
		$result_e = mysqli_query($conexion, $sql_e);
		$ver_e = mysqli_fetch_row($result_e);

		$nombre_usuario = ' ' .  $ver_e[2];
		$rol = $ver_e[4];

		$cod_mesa = $_POST['cod_mesa'];
		$cod_cliente = $_POST['cod_cliente'];
		$metodo_pago = $_POST['metodo_pago'];
		$observaciones = $_POST['observaciones'];
		$total = 0;
		$total_descuento = 0;
		$verificacion = 1;
		$cod_venta = 0;
		$config_imp = '';
		$config_cajon = '';
		$tipo = "";

		$sql_mesa = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura` FROM `mesas` WHERE cod_mesa = '$cod_mesa'";
		$result_mesa = mysqli_query($conexion, $sql_mesa);
		$mostrar_mesa = mysqli_fetch_row($result_mesa);

		if ($mostrar_mesa[2] != '') {
			if ($cod_cliente == '' && $metodo_pago == 'Crédito')
				$verificacion = 'Para pago a crédito debe seleccionar un cliente';

			if ($metodo_pago == '')
				$verificacion = 'Debe seleccionar un método de pago';

			if ($verificacion == 1) {
				$productos_mesa = json_decode($mostrar_mesa[2], true);
				$productos_mesa_nuevos = array();
				$productos_select = array();
				$pos = 1;
				$pos_2 = 1;
				foreach ($productos_mesa as $i => $producto) {
					if (isset($_POST['check_' . $i])) {
						$total += $producto['valor_unitario'] * $producto['cant'];
						$cod_producto = $producto['codigo'];
						$sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode`, `movimientos` FROM `productos` WHERE codigo='$cod_producto'";
						$result_producto = mysqli_query($conexion, $sql_producto);
						$mostrar_producto = mysqli_fetch_row($result_producto);

						$movimientos = array();
						if ($mostrar_producto[12] != '')
							$movimientos =  json_decode($mostrar_producto[12], true);

						$estado_producto = $producto['estado'];

						if ($tipo_sistema == 'Pedidos') {
							if ($estado_producto == 'EN ESPERA')
								$verificacion = 'Existen un pedido sin enviar';
							else {
								$cod_pedido = $producto['cod_pedido'];
								$sql_pedido = "SELECT `codigo`, `productos`, `mesa`, `solicitante`, `fecha_registro`, `fecha_envio`, `fecha_entrega`, `estado`, `area` FROM `pedidos` WHERE codigo = '$cod_pedido'";
								$result_pedido = mysqli_query($conexion, $sql_pedido);
								$mostrar_pedido = mysqli_fetch_row($result_pedido);
	
								$productos_pedido = array();
								if ($mostrar_pedido[1] != '')
									$productos_pedido = json_decode($mostrar_pedido[1], true);
	
								foreach ($productos_pedido as $j => $producto_pedido) {
									$estado_pedido = $producto_pedido['estado'];
									if ($estado_pedido != 'DESPACHADO' && $estado_pedido != 'CANCELADO')
										$verificacion = 'Existen productos sin salir de inventario';
								}
							}
						} else if ($tipo_sistema == 'Buffet') {
							if ($estado_producto == 'EN ESPERA') {
								$area = $producto['area'];
	
								if (!isset($cambios_areas[$area]))
									$cambios_areas[$area] = 1;
								$notas = '';
								if (isset($producto['notas']))
									$notas = $producto['notas'];
	
								if (!isset($productos_areas[$area])) {
									$codigo = uniqid();
									$productos_areas[$area]['codigo'] = $codigo;
								} else
									$codigo = $productos_areas[$area]['codigo'];
	
								$code = uniqid('P');
	
								$sql_control = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Control $area'";
								$result_control = mysqli_query($conexion, $sql_control);
								$mostrar_control = mysqli_fetch_row($result_control);
	
								$control_area = $mostrar_control[2];
								if ($control_area == 'Automatico') {
									$estado_pedido = 'DESPACHADO';
									$fecha_preparando = $fecha_h;
									$fecha_despachado = $fecha_h;
								} else {
									$estado_pedido = 'PENDIENTE';
									$fecha_preparando = '';
									$fecha_despachado = '';
								}
	
								$productos_areas[$area]['productos'][$pos]['codigo'] = $producto['codigo'];
								$productos_areas[$area]['productos'][$pos]['cant'] = $producto['cant'];
								$productos_areas[$area]['productos'][$pos]['descripcion'] = $producto['descripcion'];
								$productos_areas[$area]['productos'][$pos]['valor_unitario'] = $producto['valor_unitario'];
								$productos_areas[$area]['productos'][$pos]['estado'] = $estado_pedido;
								$productos_areas[$area]['productos'][$pos]['notas'] = $notas;
								$productos_areas[$area]['productos'][$pos]['fecha_registro'] = $producto['fecha_registro'];
								$productos_areas[$area]['productos'][$pos]['creador'] = $producto['creador'];
								$productos_areas[$area]['productos'][$pos]['area'] = $producto['area'];
								$productos_areas[$area]['productos'][$pos]['code'] = $code;
								$productos_areas[$area]['productos'][$pos]['fecha_preparando'] = $fecha_preparando;
								$productos_areas[$area]['productos'][$pos]['fecha_despachado'] = $fecha_despachado;
								$productos_areas[$area]['productos'][$pos]['fecha_cancelado'] = '';
	
								$pos++;
								$productos_mesa[$i]['estado'] = $estado_pedido;
								$productos_mesa[$i]['cod_pedido'] = $codigo;
								$productos_mesa[$i]['code'] = $code;
							}
						}

						$codigos_productos[$pos]['cod_producto'] = $cod_producto;
						$codigos_productos[$pos]['cant'] = $producto['cant'];
						$codigos_productos[$pos]['movimientos'] = $movimientos;

						$productos_select[$pos] = $producto;
						$pos++;
					} else {
						$productos_mesa_nuevos[$pos_2] = $producto;
						$pos_2++;
					}
				}

				if ($pos == 1)
					$verificacion = 'Debe seleccionar al menos un producto';

				if ($verificacion == 1) {
					$productos_select = json_encode($productos_select, JSON_UNESCAPED_UNICODE);
					if ($cod_cliente != '') {
						$sql_cliente = "SELECT `codigo`, `id`, `nombre`, `telefono`, `tipo`, `info` FROM `clientes` WHERE codigo = '$cod_cliente'";
						$result_cliente = mysqli_query($conexion, $sql_cliente);
						$ver_cliente = mysqli_fetch_row($result_cliente);

						$cliente = array(
							'codigo' => $cod_cliente,
							'id' => $ver_cliente[1],
							'nombre' => $ver_cliente[2],
							'telefono' => $ver_cliente[3],
						);
						$tipo = $ver_cliente[4];
						if ($tipo == '')
							$tipo = "Regular";

						$info = array(
							'user_creditos' => 'Administrador',
						);

						if ($ver_cliente[5] != '') {
							$info = json_decode($ver_cliente[5], true);
						}
					} else {
						$cliente = array(
							'codigo' => 0,
							'id' => 0,
							'nombre' => 'Ventas Diarias',
							'telefono' => 0,
						);
					}

					$cliente = json_encode($cliente, JSON_UNESCAPED_UNICODE);

					$pagos[1] = array(
						'tipo' => $metodo_pago,
						'valor' => $total,
						'fecha' => $fecha_h
					);

					$total_pagos = 0;
					foreach ($pagos as $j => $pago) {
						$valor_pago = $pago['valor'];
						$total_pagos += $valor_pago;

						if ($pago['tipo'] == 'Descuento')
							$pagos[$j]['valor'] *= (-1);

						if ($pago['tipo'] == 'Crédito') {
							if ($cod_cliente == '')
								$verificacion = 'Para pago a crédito debe seleccionar un cliente';
							else {
								if ($tipo == 'Especial') {
									if ($info['user_creditos'] != 'Todos') {
										if ($rol != $info['user_creditos'])
											$verificacion = 'No tiene permiso para realizar pagos a crédito a este cliente';
									}
								} else
									$verificacion = 'Al cliente seleccionado no se le permite pago a crédito';
							}
						}

						if ($cod_cliente == '' && $pago['tipo'] == 'Descuento')
							$verificacion = 'Las ventas con descuento deben seleccionar un cliente';
					}
					$saldo = $total - $total_pagos;

					if ($saldo != 0)
						$verificacion = 'Verifique los pagos y vuelva a intentarlo';
				}

				if ($verificacion == 1) {
					$pagos_2 = json_encode($pagos, JSON_UNESCAPED_UNICODE);

					$info = array(
						'observaciones' => $observaciones,
					);
					$info = json_encode($info, JSON_UNESCAPED_UNICODE);

					$sql = "INSERT INTO `ventas`(`cliente`, `productos`, `pago`, `fecha`, `cobrador`, `caja`, `info`) VALUES (
							'$cliente',
							'$productos_select',
							'$pagos_2',
							'$fecha_h',
							'$usuario',
							'1',
							'$info'
						)";

					$verificacion = mysqli_query($conexion, $sql);
				}

				if ($verificacion == 1) {
					$sql = "SELECT MAX(codigo)
						as codigo  from ventas";
					$result = mysqli_query($conexion, $sql);
					$ver = mysqli_fetch_row($result);
					$cod_venta = $ver[0];
				}

				if ($verificacion == 1) {
					foreach ($pagos as $j => $pago) {
						$valor_pago = $pago['valor'];
						$total_pagos += $valor_pago;

						if ($pago['tipo'] == 'Crédito') {
							$descripcion = 'Venta N° ' . str_pad($cod_venta, 3, "0", STR_PAD_LEFT);
							$sql = "INSERT INTO `cuentas_por_cobrar`(`cod_cliente`, `cliente`, `descripcion`, `valor`, `fecha_registro`, `creador`, `estado`, `local_recepcion`) VALUES (
								'$cod_cliente',
								'$cliente',
								'$descripcion',
								'$valor_pago',
								'$fecha_h',
								'$usuario',
								'EN MORA',
								'')";

							$verificacion = mysqli_query($conexion, $sql);
						}
					}
				}

				if ($verificacion == 1) {
					foreach ($codigos_productos as $j => $producto) {
						$movimientos = $producto['movimientos'];
						$pos = count($movimientos) + 1;

						$movimientos[$pos] = array(
							'Tipo' => 'Salida por Venta',
							'Cant' => '-' . $producto['cant'],
							'creador' => $usuario,
							'Observaciones' => 'Venta N° ' . str_pad($cod_venta, 3, "0", STR_PAD_LEFT),
							'fecha' => $fecha_h
						);

						$movimientos = json_encode($movimientos, JSON_UNESCAPED_UNICODE);
						$cod_producto_v = $producto['cod_producto'];
						$sql = "UPDATE `productos` SET 
							`movimientos`='$movimientos'
							WHERE codigo='$cod_producto_v'";

						$verificacion_2 = mysqli_query($conexion, $sql);

						if ($verificacion_2 != 1)
							$verificacion .= $verificacion_2;
					}
				}

				if ($verificacion == 1) {
					$productos_mesa_nuevos = json_encode($productos_mesa_nuevos, JSON_UNESCAPED_UNICODE);
					$sql = "UPDATE `mesas` SET 
							`productos`='$productos_mesa_nuevos'
							WHERE cod_mesa='$cod_mesa'";

					$verificacion = mysqli_query($conexion, $sql);

					$sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Imprimir Facturas'";
					$result = mysqli_query($conexion, $sql);
					$ver = mysqli_fetch_row($result);

					$config_imp = $ver[2];

					$sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Cajon'";
					$result = mysqli_query($conexion, $sql);
					$ver = mysqli_fetch_row($result);

					$config_cajon = $ver[2];
				}
			}
		} else
			$verificacion = 'No existen productos agregados';
	} else
		$verificacion = 'No tienes permisos para procesar ventas';
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion,
	'cod_venta' => $cod_venta,
	'config_imp' => $config_imp,
	'config_cajon' => $config_cajon

);

echo json_encode($datos);
