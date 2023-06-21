<?php
date_default_timezone_set('America/Bogota');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();

$fecha_h = date('Y-m-d G:i:s');

$conexion = $obj_2->conexion();

if (isset($_SESSION['usuario_restaurante'])) {
    $usuario = $_SESSION['usuario_restaurante'];

    $code = rand(100000, 999999);

    $sql = "SELECT `codigo`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_registro`, `cod_cliente`, `pagos`, `fecha_llegada`, `descuentos`, `code` FROM `reservas` WHERE code = '$code'";
    $result = mysqli_query($conexion, $sql);
    $mostrar_code = mysqli_fetch_row($result);

    while ($mostrar_code != null) {
        $code = rand(100000, 999999);
        $sql = "SELECT `codigo`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_registro`, `cod_cliente`, `pagos`, `fecha_llegada`, `descuentos`, `code` FROM `reservas` WHERE code = '$code'";
        $result = mysqli_query($conexion, $sql);
        $mostrar_code = mysqli_fetch_row($result);
    }

    $sql = "INSERT INTO `reservas`(`code`, `creador`, `estado`, `fecha_registro`) VALUES (
		'$code',
		'$usuario',
		'PENDIENTE',
		'$fecha_h')";

    $verificacion = mysqli_query($conexion, $sql);
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion
);
echo json_encode($datos);
