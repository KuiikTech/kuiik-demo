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
	$verificacion = 'No se encontró la operación solicitada';

	if (isset($_POST['identificacion_usuario'])) {
		$verificacion = 1;
		if ($_POST['rol_usuario'] == '')
			$verificacion = 'Seleccione el rol del usuario';
		if ($_POST['telefono_usuario'] == '')
			$verificacion = 'Ingrese el número de telefono del usuario';
		if ($_POST['apellido_usuario'] == '')
			$verificacion = 'Ingrese el  apellido del usuario';
		if ($_POST['nombre_usuario'] == '')
			$verificacion = 'Ingrese el nombre del usuario';
		if ($_POST['identificacion_usuario'] == '')
			$verificacion = 'Ingrese la identificación del usuario';

		if ($verificacion == 1) {
			$datos_send = array(
				$_POST['identificacion_usuario'],
				ucwords($_POST['nombre_usuario']),
				ucwords($_POST['apellido_usuario']),
				$_POST['telefono_usuario'],
				$_POST['rol_usuario']
			);

			$verificacion = $obj->agregar_usuario($datos_send);

			$sql = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `foto`, `telefono`, `rol`, `fecha_registro`, `estado`, `permisos`, `comisiones` FROM `usuarios` WHERE codigo=(SELECT MAX(codigo) from `usuarios`)";
			$result = mysqli_query($conexion, $sql);
			$datos = $result->fetch_object();

			$datos = json_encode($datos, JSON_UNESCAPED_UNICODE);
			$datos = json_decode($datos, true);
			$datos['consulta'] = $verificacion;
		}
	}

	if (isset($_POST['nombre_proveedor'])) {
		$verificacion = 1;
		if ($_POST['ciudad_proveedor'] == '')
			$verificacion = 'Ingrese la ciudad del proveedor';
		if ($_POST['telefono_proveedor'] == '')
			$verificacion = 'Ingrese el número de telefono del proveedor';
		if ($_POST['nombre_proveedor'] == '')
			$verificacion = 'Ingrese el nombre del proveedor';

		if ($verificacion == 1) {
			$datos_send = array(
				ucwords(strtolower($_POST['nombre_proveedor'])),
				$_POST['telefono_proveedor'],
				ucwords(strtolower($_POST['ciudad_proveedor']))
			);

			$verificacion = $obj->agregar_proveedor($datos_send);

			$sql = "SELECT `codigo`, `nombre`, `telefono`, `ciudad`, `fecha_registro` FROM `proveedores` WHERE codigo=(SELECT MAX(codigo) from `proveedores`)";
			$result = mysqli_query($conexion, $sql);
			$datos = $result->fetch_object();

			$datos = json_encode($datos, JSON_UNESCAPED_UNICODE);
			$datos = json_decode($datos, true);
			$datos['consulta'] = $verificacion;
		}
	}

	if (isset($_POST['input_identificacion_cliente'])) {
		$verificacion = 1;
		if ($_POST['input_correo'] == '')
			$verificacion = 'Ingrese el correo del cliente';
		if ($_POST['input_telefono'] == '')
			$verificacion = 'Ingrese el número de teléfono del cliente';
		if ($_POST['input_nombre'] == '')
			$verificacion = 'Ingrese el nombre del cliente';
		if ($_POST['input_identificacion_cliente'] == '')
			$verificacion = 'Ingrese la identificación del cliente';

		if ($verificacion == 1) {
			$datos_send = array(
				trim($_POST['input_identificacion_cliente']),
				ucwords(trim($_POST['input_nombre'])),
				trim($_POST['input_telefono']),
				trim(strtolower($_POST['input_correo'])),
				trim($_POST['input_direccion'])
			);

			$verificacion = $obj->agregar_cliente($datos_send);

			if ($verificacion == 1) {
				$sql = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro` FROM `clientes` WHERE codigo=(SELECT MAX(codigo) from clientes)";
				$result = mysqli_query($conexion, $sql);
				$datos = $result->fetch_object();

				$datos = json_encode($datos, JSON_UNESCAPED_UNICODE);
				$datos = json_decode($datos, true);
				$datos['consulta'] = $verificacion;
			}
		}
	}

	if (isset($_POST['nombre_categoria'])) {
		$verificacion = 1;
		if ($_POST['nombre_categoria'] == '')
			$verificacion = 'Escriba un nombre para la categoría';

		if ($verificacion == 1) {
			$verificacion = $obj->agregar_categoria($_POST['nombre_categoria']);

			$sql = "SELECT `cod_categoria`, `nombre` FROM `categorias_productos` WHERE cod_categoria=(SELECT MAX(cod_categoria) from categorias_productos)";
			$result = mysqli_query($conexion, $sql);
			$ver = mysqli_fetch_row($result);
			if ($ver != NULL) {
				$datos = array(
					'consulta' => $verificacion,
					'cod_categoria' => $ver[0],
					'nombre' => $ver[1]
				);
			} else
				$verificacion = 'Error al encontrar la categoría creada';
		}
	}

	if (isset($_POST['nombre_mesa'])) {
		$verificacion = 1;
		if ($_POST['nombre_mesa'] == '')
			$verificacion = 'Escriba un nombre para la mesa';

		if ($verificacion == 1) {
			$verificacion = $obj->agregar_mesa($_POST['nombre_mesa'], $_POST['descripcion_mesa']);

			$sql = "SELECT `cod_mesa`, `nombre` FROM `mesas` WHERE cod_mesa=(SELECT MAX(cod_mesa) from mesas)";
			$result = mysqli_query($conexion, $sql);
			$ver = mysqli_fetch_row($result);
			if ($ver != NULL) {
				$datos = array(
					'consulta' => $verificacion,
					'cod_mesa' => $ver[0],
					'nombre' => $ver[1]
				);
			} else
				$verificacion = 'Error al encontrar la mesa creada';
		}
	}

	if (isset($_POST['nombre_salon'])) {
		$verificacion = 1;
		if ($_POST['nombre_salon'] == '')
			$verificacion = 'Escriba un nombre para el salon';

		if ($verificacion == 1) {
			$verificacion = $obj->agregar_salon($_POST['nombre_salon'], $_POST['color_salon']);

			$sql = "SELECT `codigo`, `nombre`, `color` FROM `salones` WHERE codigo=(SELECT MAX(codigo) from salones)";
			$result = mysqli_query($conexion, $sql);
			$ver = mysqli_fetch_row($result);
			if ($ver != NULL) {
				$datos = array(
					'consulta' => $verificacion,
					'codigo' => $ver[0],
					'nombre' => $ver[1],
					'color' => $ver[2]
				);
			} else
				$verificacion = 'Error al encontrar el salon creado';
		}
	}

	if (isset($_POST['descripcion_movimiento_ant'])) {
		$verificacion = 1;
		if ($_POST['local_movimiento'] == '')
			$verificacion = 'Seleccione el local';
		if ($_POST['tipo_movimiento'] == '')
			$verificacion = 'Seleccione el tipo de movimiento';
		if ($_POST['valor_movimiento'] == '')
			$verificacion = 'Ingrese el valor del movimiento';
		if ($_POST['descripcion_movimiento'] == '')
			$verificacion = 'Escriba una descripción del movimiento';

		if ($verificacion == 1) {
			$valor_movimiento = str_replace('.', '', $_POST['valor_movimiento']);

			if ($_POST['tipo_movimiento'] == 'Retiro') {
				if ($_POST['cat_movimiento'] == '')
					$verificacion = 'Seleccione la categoría del movimiento';

				$valor_movimiento *= (-1);
			}

			if ($verificacion == 1) {
				$datos_send = array(
					$_POST['descripcion_movimiento'],
					$valor_movimiento,
					$usuario,
					$_POST['local_movimiento'],
					$_POST['tipo_movimiento'],
					$_POST['cat_movimiento'],
					$_POST['local_movimiento']
				);

				$verificacion = $obj->agregar_movimiento_caja_mayor($datos_send);


				$sql = "SELECT `cod_categoria`, `nombre` FROM `categorias_productos` WHERE cod_categoria=(SELECT MAX(cod_categoria) from categorias_productos)";
				$result = mysqli_query($conexion, $sql);
				$ver = mysqli_fetch_row($result);
				if ($ver != NULL) {
					$datos = array(
						'consulta' => $verificacion,
						'cod_categoria' => $ver[0],
						'nombre' => $ver[1]
					);
				} else
					$verificacion = 'Error al encontrar la categoría creada';
			}
		}
	}

	if (isset($_POST['descripcion_movimiento'])) {
		$verificacion = 1;
		$valor_movimiento = str_replace('.', '', $_POST['valor_movimiento']);

		if ($_POST['tipo_movimiento'] == '')
			$verificacion = 'Seleccione el tipo de movimiento';
		if ($_POST['valor_movimiento'] == '')
			$verificacion = 'Ingrese el valor';
		if ($_POST['descripcion_movimiento'] == '')
			$verificacion = 'Escriba una descripción del movimiento';

		if ($_POST['tipo_movimiento'] == 'Egreso')
			$valor_movimiento *= (-1);

		if ($verificacion == 1) {
			$datos = array(
				$_POST['descripcion_movimiento'],
				$valor_movimiento,
				$_POST['metodo_pago'],
				$usuario
			);

			$verificacion = $obj->agregar_movimiento_caja_mayor($datos);

			$datos = array(
				'consulta' => $verificacion
			);
		}
	}
} else
	$verificacion = 'Reload';

if (!isset($datos)) {
	$datos = array(
		'consulta' => $verificacion
	);
}

echo json_encode($datos);
