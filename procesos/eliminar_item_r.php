<?php
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();

$obj = new conectar();
$conexion = $obj->conexion();
$conexion = $obj->conexion();

$fecha_h = date('Y-m-d G:i:s');

$usuario = $_SESSION['usuario_restaurante'];
$bodega = '';
$cod_categoria = 0;

$sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$usuario'";
$result_e = mysqli_query($conexion, $sql_e);
$ver_e = mysqli_fetch_row($result_e);

$rol = $ver_e[1];

$verificacion = 1;

$pos = $_POST['num_item'];
$cod_reserva = $_POST['cod_reserva'];

$productos_nuevos = array();

$cantidad_descontar = 0;

$sql_reserva = "SELECT `codigo`, `nombre`, `productos`, `estado`, `fecha_registro` FROM `reservas` WHERE codigo = '$cod_reserva'";
$result_reserva = mysqli_query($conexion, $sql_reserva);
$mostrar_producto_reserva = mysqli_fetch_row($result_reserva);

$productos_reserva = json_decode($mostrar_producto_reserva[2], true);
$cod_producto = $productos_reserva[$pos]['codigo'];

$sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo='$cod_producto'";
$result_producto = mysqli_query($conexion, $sql_producto);
$mostrar_producto = mysqli_fetch_row($result_producto);

$cant = $productos_reserva[$pos]['cant'];
$cod_categoria = $mostrar_producto[5];

unset($productos_reserva[$pos]);

$i = count($productos_reserva);
if (!isset($productos_reserva[$pos]) && $i == 0)
    $productos_reserva = '';
else {
    $j = 1;
    foreach ($productos_reserva as $i => $producto) {
        $productos_nuevos[$j] = $producto;
        $j++;
    }
}

if (count($productos_nuevos) == 0)
    $productos_reserva = '';
else
    $productos_reserva = json_encode($productos_nuevos, JSON_UNESCAPED_UNICODE);

$sql = "UPDATE `reservas` SET 
`productos`='$productos_reserva'
WHERE codigo='$cod_reserva'";

$verificacion = mysqli_query($conexion, $sql);

if ($verificacion == 1) {
    if ($mostrar_producto[9] == 'Producto') {
        $inventario = $mostrar_producto[4];
        $inventario += $cant;

        $sql = "UPDATE `productos` SET 
            `inventario`='$inventario'
            WHERE codigo='$cod_producto'";
        $verificacion = mysqli_query($conexion, $sql);
    }
}

$datos = array(
    'consulta' => $verificacion,
    'cod_categoria' => $cod_categoria
);

echo json_encode($datos);
