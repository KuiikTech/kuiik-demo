<?php
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();

$obj = new conectar();
$conexion = $obj->conexion();

$fecha_h = date('Y-m-d G:i:s');

if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];

	$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
	$result_e = mysqli_query($conexion, $sql_e);
	$ver_e = mysqli_fetch_row($result_e);

	$rol = $ver_e[5];
	$cod_unico = uniqid();

	$verificacion = 1;

	$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado` FROM `caja` WHERE estado = 'ABIERTA'";

	if ($verificacion == 1) {
		$result = mysqli_query($conexion, $sql);
		$mostrar = mysqli_fetch_row($result);

		if ($mostrar != NULL) {
			$cod_caja = $mostrar[0];
			$verificacion = 1;

			$cod_cuenta = $_POST['cod_cuenta'];
			$metodo_pago = $_POST['input_metodo_pago'];
			$valor_pago = str_replace('.', '', $_POST['input_valor_pago']);

			if ($valor_pago == '')
				$verificacion = 'Ingrese el valor del pago';
			if ($metodo_pago == '')
				$verificacion = 'Seleccione un método de pago';

			if ($verificacion == 1) {
				$sql = "SELECT `codigo`, `cod_cliente`, `cliente`, `descripcion`, `valor`, `fecha_registro`, `fecha_pago`, `fecha_ingreso`, `creador`, `cobrador`, `cajero`, `estado`, `pagos` FROM `cuentas_por_cobrar` WHERE `codigo` = '$cod_cuenta'";
				$result = mysqli_query($conexion, $sql);
				$mostrar = mysqli_fetch_row($result);

				$valor_credito = $mostrar[4];

				$pagos = array();
				$pos = 1;
				if ($mostrar[12] != '') {
					$pagos = json_decode($mostrar[12], true);
					$pos += count($pagos);
				}
				$total_pagos = 0;
				foreach ($pagos as $i => $pago) {
					$total_pagos += $pago['valor'];
				}

				$total_pagos += $valor_pago;

				if ($valor_credito >= $total_pagos) {
					$pagos[$pos] = array(
						'tipo' => $metodo_pago,
						'valor' => $valor_pago,
						'creador' => $usuario,
						'fecha' => $fecha_h,
						'cod_unico' => $cod_unico
					);

					$item_serv = $pos;

					$pagos = json_encode($pagos, JSON_UNESCAPED_UNICODE);
					if ($valor_credito == $total_pagos)
						$sql = "UPDATE `cuentas_por_cobrar` SET `pagos`='$pagos', `estado`='COBRADO' WHERE codigo='$cod_cuenta'";
					else
						$sql = "UPDATE `cuentas_por_cobrar` SET `pagos`='$pagos' WHERE codigo='$cod_cuenta'";

					$verificacion = mysqli_query($conexion, $sql);
				} else
					$verificacion = 'El valor total de los pagos supera el valor del crédito';
			}

			if ($verificacion == 1) {
				$descripcion_ingreso = 'Pago de crédito (Cuenta por cobrar N° ' . str_pad($cod_cuenta, 3, "0", STR_PAD_LEFT) . ') [' . $mostrar[3] . ']';

				$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `finalizador`, `egresos`, `kilos_fin` FROM `caja` WHERE codigo = '$cod_caja'";
				$result = mysqli_query($conexion, $sql);
				$mostrar = mysqli_fetch_row($result);

				$ingresos = array();
				$pos = 1;
				if ($mostrar[9] != NULL) {
					$ingresos = json_decode($mostrar[9], true);
					$pos += count($ingresos);
				}

				$ingresos[$pos]['descripcion'] = $descripcion_ingreso;
				$ingresos[$pos]['valor'] = $valor_pago;
				$ingresos[$pos]['metodo'] = $metodo_pago;
				$ingresos[$pos]['fecha'] = $fecha_h;
				$ingresos[$pos]['eliminable'] = 'SI';
				$ingresos[$pos]['cod_unico'] = $cod_unico;

				$ingresos = json_encode($ingresos, JSON_UNESCAPED_UNICODE);
				$sql = "UPDATE `caja` SET `ingresos`='$ingresos' WHERE codigo='$cod_caja'";

				$verificacion = mysqli_query($conexion, $sql);
			}
		} else
			$verificacion = 'No existe una caja ABIERTA';
	}
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion
);

echo json_encode($datos);
