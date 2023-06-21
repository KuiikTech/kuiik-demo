<?php
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();

$obj = new conectar();
$conexion = $obj->conexion();

$cod_categoria = 0;

$fecha_h = date('Y-m-d G:i:s');
if (isset($_SESSION['usuario_restaurante'])) {
    $usuario = $_SESSION['usuario_restaurante'];

    $sql_stock = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Validar Stock'";
    $result_stock = mysqli_query($conexion, $sql_stock);
    $mostrar_stock = mysqli_fetch_row($result_stock);

    $validar_stock = $mostrar_stock[2];

    $verificacion = 1;

    $pos = $_POST['item'];
    $cant_new = $_POST['cant'];
    $cod_reserva = $_POST['cod_reserva'];

    if ($cant_new != '') {
        $sql_reserva = "SELECT `codigo`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_registro`, `cod_cliente`, `pagos`, `fecha_llegada`, `descuentos`, `code`, `creador` FROM `reservas` WHERE codigo = '$cod_reserva'";
        $result_reserva = mysqli_query($conexion, $sql_reserva);
        $mostrar_reserva = mysqli_fetch_row($result_reserva);

        $productos_reserva = array();
        if ($mostrar_reserva[3] != '')
            $productos_reserva = json_decode($mostrar_reserva[3], true);

        if (isset($productos_reserva[$pos])) {
            $cant_old = $productos_reserva[$pos]['cant'];
            $productos_reserva[$pos]['cant'] = $cant_new;
            $cant = $cant_new - $cant_old;
            $cod_producto = $productos_reserva[$pos]['codigo'];

            $sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo='$cod_producto'";
            $result_producto = mysqli_query($conexion, $sql_producto);
            $mostrar_producto = mysqli_fetch_row($result_producto);

            $stock = $mostrar_producto[4];
            $cod_categoria = $mostrar_producto[5];

            if ($mostrar_producto[9] == 'Producto') {
                if ($validar_stock == 'SI') {
                    if ($cant > $stock)
                        $verificacion = 'El inventario para ' . $mostrar_producto[1] . ' es: ' . $stock + $cant_old;
                }

                if ($verificacion == 1) {
                    $inventario = $stock - $cant;
                    $sql = "UPDATE `productos` SET 
                        `inventario`='$inventario'
                        WHERE codigo='$cod_producto'";
                    $verificacion = mysqli_query($conexion, $sql);
                }
            }
            if ($verificacion == 1) {
                $productos_reserva = json_encode($productos_reserva, JSON_UNESCAPED_UNICODE);

                $sql = "UPDATE `reservas` SET
                `productos`='$productos_reserva'
                WHERE codigo='$cod_reserva'";

                $verificacion = mysqli_query($conexion, $sql);
            }
        } else
            $verificacion = 'No se encontró el producto en la reserva';
    } else
        $verificacion = 'Ingrese una cantidad válida';
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion
);

echo json_encode($datos);
