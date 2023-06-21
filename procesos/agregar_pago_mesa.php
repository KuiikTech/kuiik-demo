<?php
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();

$obj = new conectar();
$conexion = $obj->conexion();

$fecha_h = date('Y-m-d G:i:s');

if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];
	$verificacion = 1;

	$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
	$result_e = mysqli_query($conexion, $sql_e);
	$ver_e = mysqli_fetch_row($result_e);

	$rol = $ver_e[5];

	$sql_acceso = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Acceso a mesas'";
	$result_acceso = mysqli_query($conexion, $sql_acceso);
	$mostrar_acceso = mysqli_fetch_row($result_acceso);

	$acceso_mesas = $mostrar_acceso[2];

	$cod_mesa = $_POST['cod_mesa'];
	$metodo_pago = $_POST['metodo_pago'];
	$valor_pago = str_replace('.', '', $_POST['valor_pago']);

	if ($valor_pago == '')
		$verificacion = 'Ingrese el valor del pago';
	if ($_POST['metodo_pago'] == '')
		$verificacion = 'Seleccione un método de pago';

	if ($verificacion == 1) {
		$sql_mesa = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura`, `pagos`, `mesero` FROM `mesas` WHERE cod_mesa = '$cod_mesa'";
		$result_mesa = mysqli_query($conexion, $sql_mesa);
		$mostrar_mesa = mysqli_fetch_row($result_mesa);

		$mesero = $mostrar_mesa[6];

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
			$pagos = array();
			$pos = 1;
			if ($mostrar_mesa[5] != '') {
				$pagos = json_decode($mostrar_mesa[5], true);
				$pos += count($pagos);
			}

			if ($metodo_pago == 'Efectivo') {
				$pagos[$pos] = array(
					'tipo' => $metodo_pago,
					'valor' => $valor_pago,
					'fecha' => $fecha_h,
					'recibido' => 0
				);
			} else {
				$pagos[$pos] = array(
					'tipo' => $metodo_pago,
					'valor' => $valor_pago,
					'fecha' => $fecha_h
				);
			}

			$pagos = json_encode($pagos, JSON_UNESCAPED_UNICODE);

			$sql = "UPDATE `mesas` SET `pagos`='$pagos' WHERE cod_mesa='$cod_mesa'";

			$verificacion = mysqli_query($conexion, $sql);
		}
	}
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion
);

echo json_encode($datos);
