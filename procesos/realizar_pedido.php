<?php
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();

$obj = new conectar();
$conexion = $obj->conexion();

$fecha_h = date('Y-m-d G:i:s');
$textos = array();
$cambios_areas = array();

if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];
	$productos_areas = array();
	$pos = 1;

	$mesero = $usuario;

	$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$usuario'";
	$result_e = mysqli_query($conexion, $sql_e);
	$ver_e = mysqli_fetch_row($result_e);
	if ($ver_e != null)
		$mesero = $ver_e[0]; //. ' ' . $ver_e[1];

	$verificacion = 1;
	$pos_pedido = 1;

	$cod_mesa = $_POST['cod_mesa'];

	$sql_mesa = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura` FROM `mesas` WHERE cod_mesa = '$cod_mesa'";
	$result_mesa = mysqli_query($conexion, $sql_mesa);
	$mostrar_mesa = mysqli_fetch_row($result_mesa);

	if ($mostrar_mesa[2] != '') {
		$productos_mesa = json_decode($mostrar_mesa[2], true);

		$textos[1] = array(
			"text" => 'MESA: ' . $mostrar_mesa[1],
			"font_weight" => 'bold',
			"font_size" => 2,
			"justify" => 'center',
			"divider" => 'true'
		);
		$textos[2] = array(
			"text" => $fecha_h,
			"font_weight" => 'normal',
			"font_size" => 1,
			"justify" => 'center',
			"divider" => 'false'
		);
		$pos_text = 4;
		foreach ($productos_mesa as $i => $producto) {
			if ($producto['estado'] == 'EN ESPERA') {
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

				$textos[$pos_text] = array(
					"text" => $producto['cant'] . " -- " . $producto['descripcion'],
					"font_weight" => 'normal',
					"font_size" => 1,
					"justify" => 'left',
					"divider" => 'true',
					"notes" => $notas
				);
				$pos_text++;
			}
		}

		foreach ($productos_areas as $p => $datos_area) {
			$codigo = $datos_area['codigo'];
			$area = $p;

			$sql_control = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Control $area'";
			$result_control = mysqli_query($conexion, $sql_control);
			$mostrar_control = mysqli_fetch_row($result_control);

			$control_area = $mostrar_control[2];
			if ($control_area == 'Automatico')
				$estado_pedido = 'TERMINADO';
			else
				$estado_pedido = 'PENDIENTE';

			$productos_pedido = json_encode($datos_area['productos'], JSON_UNESCAPED_UNICODE);

			$sql_pedido = "INSERT INTO `pedidos`(`codigo`, `productos`, `mesa`, `solicitante`, `fecha_registro`, `fecha_envio`, `estado`, `area`, `fecha_entrega`) VALUES (
			'$codigo',
			'$productos_pedido',
			'$cod_mesa',
			'$usuario',
			'$fecha_h',
			'$fecha_h',
			'$estado_pedido',
			'$area',
			'$fecha_h')";

			$verificacion_2 = mysqli_query($conexion, $sql_pedido);

			if ($verificacion_2 != 1)
				$verificacion .= $verificacion_2;
		}

		if ($verificacion == 1) {
			$textos[3] = array(
				"text" => "",
				"font_weight" => 'normal',
				"font_size" => 1,
				"justify" => 'center',
				"divider" => 'true'
			);

			$textos[$pos_text] = array(
				"text" => "MESERO -- " . $mesero,
				"font_weight" => 'normal',
				"font_size" => 1,
				"justify" => 'left',
				"divider" => 'true'
			);

			$productos_mesa = json_encode($productos_mesa, JSON_UNESCAPED_UNICODE);
			$sql = "UPDATE `mesas` SET 
					`productos`='$productos_mesa'
					WHERE cod_mesa='$cod_mesa'";

			$verificacion = mysqli_query($conexion, $sql);
		}

		if ($verificacion == 1) {
			foreach ($cambios_areas as $area => $valor) {
				$sql = "UPDATE `configuraciones` SET 
					`valor`='1'
					WHERE descripcion='Cambios $area'";

				$verificacion = mysqli_query($conexion, $sql);
			}
		}
	}
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion,
	'textos' => $textos
);

echo json_encode($datos);
