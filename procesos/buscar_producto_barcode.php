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

$usuario = $_SESSION['usuario_restaurante'];

$verificacion = 1;
$cod_producto = '';
$inventario_nuevo = '';

if ($_POST['busqueda_barcode'] != '') {
    $busqueda_barcode = $_POST['busqueda_barcode'];

    $sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode`, `movimientos`, `especial` FROM `productos` WHERE barcode='$busqueda_barcode'";
    $result_producto = mysqli_query($conexion, $sql_producto);
    $mostrar_producto = mysqli_fetch_row($result_producto);

    if ($mostrar_producto != null) {
        $cod_producto = $mostrar_producto[0];
    } else
        $verificacion = 'No existe producto con el codigo ingresado';
} else
    $verificacion = 'Ingrese un codigo';

$datos = array(
    'consulta' => $verificacion,
    'cod_producto' => $cod_producto
);

echo json_encode($datos);
