<?php
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();

$obj = new conectar();
$conexion = $obj->conexion();

$fecha_h = date('Y-m-d G:i:s');

if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];

	$sql_acceso = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Acceso a mesas'";
	$result_acceso = mysqli_query($conexion, $sql_acceso);
	$mostrar_acceso = mysqli_fetch_row($result_acceso);

	$acceso_mesas = $mostrar_acceso[2];

	$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
	$result_e = mysqli_query($conexion, $sql_e);
	$ver_e = mysqli_fetch_row($result_e);

	$rol = $ver_e[5];

	$cod_mesa = $_POST['cod_mesa'];

	$sql = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura`, `mesero` FROM `mesas` WHERE cod_mesa='$cod_mesa'";
	$result = mysqli_query($conexion, $sql);
	$ver = mysqli_fetch_row($result);

	$estado = $ver[3];

	if ($estado == 'LIBRE') {
		$sql = "UPDATE `mesas` SET 
				`estado`='OCUPADA',
				`cod_cliente`= NULL,
				`mesero`='$usuario',
				`fecha_apertura`='$fecha_h',
				`pagos`= ''
				WHERE cod_mesa='$cod_mesa'";

		$verificacion = mysqli_query($conexion, $sql);
	} else if ($estado == 'OCUPADA') {
		if ($rol != 'Administrador' && $acceso_mesas != 'Todos' && $acceso_mesas != 'CreadorVer') {
			$mesero = $ver[5];
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
	} else
		$verificacion = 'Reload';
} else
	$verificacion = 'Reload';
$datos = array(
	'consulta' => $verificacion
);

echo json_encode($datos);
