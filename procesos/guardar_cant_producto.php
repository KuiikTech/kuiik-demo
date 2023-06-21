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
    $cod_mesa = $_POST['cod_mesa'];

    if ($cant_new != '') {

        $sql_mesa = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura` FROM `mesas` WHERE cod_mesa = '$cod_mesa'";
        $result_mesa = mysqli_query($conexion, $sql_mesa);
        $mostrar_mesa = mysqli_fetch_row($result_mesa);

        $productos_mesa = json_decode($mostrar_mesa[2], true);

        if (isset($productos_mesa[$pos])) {
            $cant_old = $productos_mesa[$pos]['cant'];
            $productos_mesa[$pos]['cant'] = $cant_new;
            $cant = $cant_new - $cant_old;
            $cod_producto = $productos_mesa[$pos]['codigo'];

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
                $productos_mesa = json_encode($productos_mesa, JSON_UNESCAPED_UNICODE);

                $sql = "UPDATE `mesas` SET
                `productos`='$productos_mesa'
                WHERE cod_mesa='$cod_mesa'";

                $verificacion = mysqli_query($conexion, $sql);
            }
        } else
            $verificacion = 'No se encontró el producto en la mesa';
    } else
        $verificacion = 'Ingrese una cantidad válida';
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion,
	'cod_categoria' => $cod_categoria
);

echo json_encode($datos);
