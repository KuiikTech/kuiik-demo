<?php
date_default_timezone_set('America/Bogota');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();

$fecha_h = date('Y-m-d G:i:s');

$conexion = $obj_2->conexion();
$conexion = $obj_2->conexion();

if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];

	$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto`, `color` FROM `usuarios` WHERE codigo='$usuario'";
	$result_e = mysqli_query($conexion, $sql_e);
	$ver_e = mysqli_fetch_row($result_e);

	$cedula = $ver_e[1];

	$nombre_usuario = $ver_e[2] . ' ' . $ver_e[3];
	$rol = $ver_e[5];

	$verificacion = 'No se encontró la operación solicitada';

	$cod_servicio = $_POST['cod_servicio'];

	if (isset($_POST['observaciones'])) {
		$operacion = 'Ingreso de Observaciones';

		$observaciones = $_POST['observaciones'];

		$sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro` FROM `servicios` WHERE `codigo` = '$cod_servicio'";
		$result = mysqli_query($conexion, $sql);
		$mostrar = mysqli_fetch_row($result);

		$result_2 = mysqli_query($conexion, $sql);
		$info_respaldo = $result_2->fetch_object();

		$informacion = array();
		if ($mostrar[4] != '') {
			$informacion = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[4]);
			$informacion = str_replace('	', ' ', $informacion);
			$informacion = json_decode($informacion, true);
		}

		$pos = 1;
		if (!isset($informacion['observaciones']))
			$informacion['observaciones'] = array();
		$pos += count($informacion['observaciones']);

		if (isset($_SESSION['usuario_restaurante2']))
			$local = 'Restaurante 2';
		else
			$local = 'Restaurante 1';

		$informacion['observaciones'][$pos] = array(
			'obs' => $observaciones,
			'local' => $local,
			'creador' => $usuario,
			'fecha' => $fecha_h
		);

		$tecnico = $mostrar[8];
		if ($tecnico != null && $tecnico != '') {
			$informacion['notificaciones'] = array();
			$informacion['notificaciones']['estado'] = 'PENDIENTE';
			$informacion['notificaciones']['receptor'] = $tecnico;
			$informacion['notificaciones']['descripcion'] = 'Cambios en la observaciones iniciales';
			$informacion['notificaciones']['fecha'] = $fecha_h;
			$informacion['notificaciones']['creador'] = $usuario;
		}

		$informacion = json_encode($informacion, JSON_UNESCAPED_UNICODE);

		$sql = "UPDATE `servicios` SET 
		`informacion`='$informacion'
		WHERE codigo='$cod_servicio'";

		$verificacion = mysqli_query($conexion, $sql);
	}

	if (isset($_POST['observaciones_tecnico'])) {
		$operacion = 'Ingreso de Observaciones de técnico';

		$observaciones = $_POST['observaciones_tecnico'];

		$sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro` FROM `servicios` WHERE `codigo` = '$cod_servicio'";
		$result = mysqli_query($conexion, $sql);
		$mostrar = mysqli_fetch_row($result);

		$result_2 = mysqli_query($conexion, $sql);
		$info_respaldo = $result_2->fetch_object();

		$informacion = array();
		if ($mostrar[4] != '') {
			$informacion = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[4]);
			$informacion = str_replace('	', ' ', $informacion);
			$informacion = json_decode($informacion, true);
		}

		$pos = 1;
		if (!isset($informacion['observaciones_tecnico']))
			$informacion['observaciones_tecnico'] = array();
		$pos += count($informacion['observaciones_tecnico']);

		if (isset($_SESSION['usuario_restaurante2']))
			$local = 'Restaurante 2';
		else
			$local = 'Restaurante 1';

		$informacion['observaciones_tecnico'][$pos] = array(
			'obs' => $observaciones,
			'local' => $local,
			'creador' => $usuario,
			'fecha' => $fecha_h
		);

		$creador = $mostrar[7];
		if ($creador != null && $creador != '') {
			$informacion['notificaciones'] = array();
			$informacion['notificaciones']['estado'] = 'PENDIENTE';
			$informacion['notificaciones']['receptor'] = $creador;
			$informacion['notificaciones']['descripcion'] = 'Cambios en la observaciones del técnico';
			$informacion['notificaciones']['fecha'] = $fecha_h;
			$informacion['notificaciones']['creador'] = $usuario;
		}

		$informacion = json_encode($informacion, JSON_UNESCAPED_UNICODE);

		$sql = "UPDATE `servicios` SET 
		`informacion`='$informacion'
		WHERE codigo='$cod_servicio'";

		$verificacion = mysqli_query($conexion, $sql);
	}

	if (isset($_POST['observaciones_ticket'])) {
		$operacion = 'Ingreso de Observaciones para ticket';

		$observaciones = $_POST['observaciones_ticket'];

		$sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro` FROM `servicios` WHERE `codigo` = '$cod_servicio'";
		$result = mysqli_query($conexion, $sql);
		$mostrar = mysqli_fetch_row($result);

		$result_2 = mysqli_query($conexion, $sql);
		$info_respaldo = $result_2->fetch_object();

		$informacion = array();
		if ($mostrar[4] != '') {
			$informacion = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[4]);
			$informacion = str_replace('	', ' ', $informacion);
			$informacion = json_decode($informacion, true);
		}

		$pos = 1;
		if (!isset($informacion['observaciones_ticket']))
			$informacion['observaciones_ticket'] = array();
		$pos += count($informacion['observaciones_ticket']);

		if (isset($_SESSION['usuario_restaurante2']))
			$local = 'Restaurante 2';
		else
			$local = 'Restaurante 1';

		$informacion['observaciones_ticket'][$pos] = array(
			'obs' => $observaciones,
			'local' => $local,
			'creador' => $usuario,
			'fecha' => $fecha_h
		);

		$creador = $mostrar[8];
		if ($creador != null && $creador != '') {
			$informacion['notificaciones'] = array();
			$informacion['notificaciones']['estado'] = 'PENDIENTE';
			$informacion['notificaciones']['receptor'] = $creador;
			$informacion['notificaciones']['descripcion'] = 'Cambios en la observaciones del ticket';
			$informacion['notificaciones']['fecha'] = $fecha_h;
			$informacion['notificaciones']['creador'] = $usuario;
		}

		$informacion = json_encode($informacion, JSON_UNESCAPED_UNICODE);

		$sql = "UPDATE `servicios` SET 
		`informacion`='$informacion'
		WHERE codigo='$cod_servicio'";

		$verificacion = mysqli_query($conexion, $sql);
	}

	if (isset($_POST['estado'])) {
		$estado = $_POST['estado'];

		$operacion = 'Cambio de estado [' . $estado . ']';

		$verificacion = 1;

		if ($estado == 'TERMINADO' || $estado == 'ENTREGADO') {
			$sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro` FROM `servicios` WHERE `codigo` = '$cod_servicio'";
			$result = mysqli_query($conexion, $sql);
			$mostrar = mysqli_fetch_row($result);

			$result_2 = mysqli_query($conexion, $sql);
			$info_respaldo = $result_2->fetch_object();

			if ($mostrar[4] != '') {
				$informacion = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[4]);
				$informacion = str_replace('	', ' ', $informacion);
				$informacion = json_decode($informacion, true);

				if (!isset($informacion['solucion']))
					$verificacion =  'Seleccione una solución para el servicio';
				else {
					if ($informacion['solucion'] == '')
						$verificacion =  'Seleccione una solución para el servicio';
				}

				if ($mostrar[10] == null)
					$verificacion = 'No se puede cambiar el estado a <b>' . $estado . '</b>. No se ha ingresado la fecha de entrega';

				if ($estado == 'ENTREGADO') {
					if ($mostrar[5] == '') {
						if (!isset($informacion['sin_repuestos']))
							$verificacion = 'No se puede cambiar el estado a <b>ENTREGADO</b>. No existen repuestos agregados';
						else {
							if ($informacion['sin_repuestos'] == 'false')
								$verificacion = 'No se puede cambiar el estado a <b>ENTREGADO</b>. No existen repuestos agregados';
						}
					}

					if ($verificacion == 1) {
						if ($mostrar[3] != '')
							$pagos = json_decode($mostrar[3], true);
						$accesorios = array();
						if ($mostrar[6] != '') {
							$accesorios = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[6]);
							$accesorios = str_replace('	', ' ', $accesorios);
							$accesorios = json_decode($accesorios, true);
						}

						$total_pagos = 0;

						foreach ($pagos as $i => $pago)
							$total_pagos += $pago['valor'];

						if (isset($informacion['total_servicios']))
							$total_servicios = $informacion['total_servicios'];
						else
							$total_servicios = 0;

						$total_accesorios = 0;
						foreach ($accesorios as $i => $accesorio) {
							$cant = $accesorio['cant'];
							$valor_unitario = $accesorio['valor_unitario'];

							$descuento_acc = 0;

							if (isset($accesorio['decuento']))
								$descuento_acc = $accesorio['decuento'];

							$valor_total = $cant * ($valor_unitario - $descuento_acc);
							$total_accesorios += $valor_total;
						}

						$saldo = $total_servicios + $total_accesorios - $total_pagos;

						if ($saldo != 0) {
							if($rol != 'Administrador'){
							$verificacion = 'Para cambiar el estado a <b>ENTREGADO</b> este servicio, el saldo debe ser cero.';
							}
						}
					}
				}
			} else
				$verificacion = 'No se encontró información del servicio. Por favor intentelo de nuevo.';
		} else if ($estado == 'PENDIENTE' || $estado == 'EN ESPERA') {
			$sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro` FROM `servicios` WHERE `codigo` = '$cod_servicio'";
			$result = mysqli_query($conexion, $sql);
			$mostrar = mysqli_fetch_row($result);

			$result_2 = mysqli_query($conexion, $sql);
			$info_respaldo = $result_2->fetch_object();

			if ($mostrar[4] != '') {
				$informacion = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[4]);
				$informacion = str_replace('	', ' ', $informacion);
				$informacion = json_decode($informacion, true);
			} else
				$verificacion = 'No se encontró información del servicio. Por favor intentelo de nuevo.';
		}

		if ($verificacion == 1) {
			if ($estado == 'ENTREGADO') {
				$informacion['fecha_entrega'] = $fecha_h;
				$informacion['entregó'] = $usuario;

				$info_registro = array(
					'cod_servicio' => $cod_servicio,
					'fecha_entrega' => $fecha_h,
					'entrega' => $usuario
				);

				$info_registro = array(
					'Tipo' => 'Entrega de servicio',
					'Información' => $info_registro
				);

				$info_registro = json_encode($info_registro, JSON_UNESCAPED_UNICODE);

				$sql = "INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
					'$info_registro',
					'$usuario',
					'$fecha_h')";
				$verificacion = mysqli_query($conexion, $sql);
			}

			$informacion = json_encode($informacion, JSON_UNESCAPED_UNICODE);
			if ($estado == 'ENTREGADO') {
				$sql = "UPDATE `servicios` SET 
				`estado`='$estado',
				`informacion`='$informacion',
				`fecha_entrega_real`='$fecha_h'
				WHERE codigo='$cod_servicio'";
			} else {
				$sql = "UPDATE `servicios` SET 
				`estado`='$estado',
				`informacion`='$informacion'
				WHERE codigo='$cod_servicio'";
			}

			$verificacion = mysqli_query($conexion, $sql);
		}
	}

	if (isset($_POST['solucion'])) {

		$solucion = $_POST['solucion'];
		$operacion = 'Asignación de solución [' . $solucion . ']';

		$sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro` FROM `servicios` WHERE `codigo` = '$cod_servicio'";
		$result = mysqli_query($conexion, $sql);
		$mostrar = mysqli_fetch_row($result);

		$result_2 = mysqli_query($conexion, $sql);
		$info_respaldo = $result_2->fetch_object();

		$informacion = array();
		if ($mostrar[4] != '') {
			$informacion = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[4]);
			$informacion = str_replace('	', ' ', $informacion);
			$informacion = json_decode($informacion, true);

			$informacion['solucion'] = $solucion;

			$informacion = json_encode($informacion, JSON_UNESCAPED_UNICODE);

			$sql = "UPDATE `servicios` SET 
			`informacion`='$informacion'
			WHERE codigo='$cod_servicio'";

			$verificacion = mysqli_query($conexion, $sql);
		} else
			$verificacion = '';
	}

	if (isset($_POST['sin_repuestos'])) {
		$operacion = 'Selección sin repuestos';

		$sin_repuestos = $_POST['sin_repuestos'];

		$sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro` FROM `servicios` WHERE `codigo` = '$cod_servicio'";
		$result = mysqli_query($conexion, $sql);
		$mostrar = mysqli_fetch_row($result);

		$result_2 = mysqli_query($conexion, $sql);
		$info_respaldo = $result_2->fetch_object();

		$informacion = array();
		if ($mostrar[4] != '') {
			$informacion = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[4]);
			$informacion = str_replace('	', ' ', $informacion);
			$informacion = json_decode($informacion, true);
		}

		if ($mostrar[9]  != 'ENTREGADO') {
			$informacion['sin_repuestos'] = $sin_repuestos;

			$informacion = json_encode($informacion, JSON_UNESCAPED_UNICODE);

			$sql = "UPDATE `servicios` SET 
			`informacion`='$informacion'
			WHERE codigo='$cod_servicio'";

			$verificacion = mysqli_query($conexion, $sql);
		} else
			$verificacion = 'No se puede cambiar este item. Este servicio ya está <b>TERMINADO</b>';
	}

	if (isset($_POST['total_servicios'])) {
		$total_servicios = str_replace('.', '', $_POST['total_servicios']);

		$sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro` FROM `servicios` WHERE `codigo` = '$cod_servicio'";
		$result = mysqli_query($conexion, $sql);
		$mostrar = mysqli_fetch_row($result);

		$result_2 = mysqli_query($conexion, $sql);
		$info_respaldo = $result_2->fetch_object();

		$informacion = array();
		if ($mostrar[4] != '') {
			$informacion = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[4]);
			$informacion = str_replace('	', ' ', $informacion);
			$informacion = json_decode($informacion, true);
		}

		$operacion = 'Cambio de valor total servicios -> $' . $_POST['total_servicios'] . ' (Ant. $' . number_format($informacion['total_servicios'], 0, '.', '.') . ')';

		$informacion['total_servicios'] = $total_servicios;

		$informacion = json_encode($informacion, JSON_UNESCAPED_UNICODE);

		$sql = "UPDATE `servicios` SET 
		`informacion`='$informacion'
		WHERE codigo='$cod_servicio'";

		$verificacion = mysqli_query($conexion, $sql);
	}

	if (isset($_POST['fecha'])) {
		$operacion = 'Cambio de fecha';

		$fecha = $_POST['fecha'];

		if ($fecha != '') {
			$sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro` FROM `servicios` WHERE `codigo` = '$cod_servicio'";
			$result = mysqli_query($conexion, $sql);
			$mostrar = mysqli_fetch_row($result);

			$result_2 = mysqli_query($conexion, $sql);
			$info_respaldo = $result_2->fetch_object();

			if ($mostrar[10] != null)
				$fecha .= date(' G:i:s', strtotime($mostrar[10]));
			else
				$fecha .= date(' 00:00:00');

			$sql = "UPDATE `servicios` SET 
			`fecha_entrega`='$fecha'
			WHERE `codigo`='$cod_servicio'";

			$verificacion = mysqli_query($conexion, $sql);
		} else
			$verificacion = 'Seleccione una fecha de entrega para el trabajo';
	}

	if (isset($_POST['hora'])) {
		$operacion = 'Cambio de hora';

		$hora = $_POST['hora'];

		if ($hora != '') {
			$sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro` FROM `servicios` WHERE `codigo` = '$cod_servicio'";
			$result = mysqli_query($conexion, $sql);
			$mostrar = mysqli_fetch_row($result);

			$result_2 = mysqli_query($conexion, $sql);
			$info_respaldo = $result_2->fetch_object();

			if ($mostrar[10] != null)
				$fecha = date('Y-m-d ', strtotime($mostrar[10])) . $hora;
			else
				$fecha = date('Y-m-d ') . $hora;

			$sql = "UPDATE `servicios` SET 
			`fecha_entrega`='$fecha'
			WHERE `codigo`='$cod_servicio'";

			$verificacion = mysqli_query($conexion, $sql);
		} else
			$verificacion = 'Seleccione una fecha de entrega para el trabajo';
	}

	if (isset($_POST['tecnico'])) {
		$operacion = 'Asignación  de técnico';

		$tecnico = $_POST['tecnico'];

		$sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro` FROM `servicios` WHERE `codigo` = '$cod_servicio'";
		$result = mysqli_query($conexion, $sql);
		$mostrar = mysqli_fetch_row($result);

		$result_2 = mysqli_query($conexion, $sql);
		$info_respaldo = $result_2->fetch_object();

		$sql = "UPDATE `servicios` SET 
		`tecnico`='$tecnico'
		WHERE `codigo`='$cod_servicio'";

		$verificacion = mysqli_query($conexion, $sql);
	}

	if ($verificacion == 1) {
		$info_respaldo = json_encode($info_respaldo, JSON_UNESCAPED_UNICODE);
		$sql = "INSERT INTO `respaldo_info`(`cod_servicio`, `informacion`, `operacion`, `usuario`, `fecha_registro`) VALUES (
			'$cod_servicio',
			'$info_respaldo',
			'$operacion',
			'$usuario',
			'$fecha_h')";

		mysqli_query($conexion, $sql);
	}
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion
);
echo json_encode($datos);
