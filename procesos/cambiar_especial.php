<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();
$conexion = $obj_2->conexion();

$especial = '';

if (isset($_SESSION['usuario_restaurante'])) {
    $usuario = $_SESSION['usuario_restaurante'];

    $verificacion = 'Reload';

    $cod_producto = $_POST['cod_producto'];

    $sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode`, `movimientos`, `especial` FROM `productos` WHERE codigo='$cod_producto'";
    $result_producto = mysqli_query($conexion, $sql_producto);
    $mostrar_producto = mysqli_fetch_row($result_producto);

    $especial = $mostrar_producto[13];

    if ($especial == 'SI')
        $especial = 'NO';
    else
        $especial = 'SI';

    $sql = "UPDATE `productos` SET 
	`especial`='$especial'
	WHERE codigo='$cod_producto'";

    $verificacion = mysqli_query($conexion, $sql);
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion,
    'especial' => $especial
);

echo json_encode($datos);
