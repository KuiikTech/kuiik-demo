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

$verificacion = 1;
$cod_categoria = 0;
$bodega = '';

if (isset($_SESSION['usuario_restaurante'])) {
    $usuario = $_SESSION['usuario_restaurante'];

    $sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado` FROM `caja` WHERE estado = 'ABIERTA'";
    $result = mysqli_query($conexion, $sql);
    $mostrar = mysqli_fetch_row($result);

    if ($mostrar != NULL) {
        $cod_producto = $_POST['cod_producto'];
        $cod_reserva = $_POST['cod_reserva'];
        $cant = $_POST['cant'];

        $sql_stock = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Validar Stock'";
        $result_stock = mysqli_query($conexion, $sql_stock);
        $mostrar_stock = mysqli_fetch_row($result_stock);

        $validar_stock = $mostrar_stock[2];

        $sql_reserva = "SELECT `codigo`, `nombre`, `productos`, `estado`, `fecha_registro` FROM `reservas` WHERE codigo = '$cod_reserva'";
        $result_reserva = mysqli_query($conexion, $sql_reserva);
        $mostrar_reserva = mysqli_fetch_row($result_reserva);

        $sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo='$cod_producto'";
        $result_producto = mysqli_query($conexion, $sql_producto);
        $mostrar_producto = mysqli_fetch_row($result_producto);

        $nombre_producto = $mostrar_producto[1];
        $cod_categoria = $mostrar_producto[5];
        $inventario = $mostrar_producto[4];
        $stock = $inventario;
        $valor_unitario = $mostrar_producto[3];
        $area = $mostrar_producto[8];

        $pos = 1;
        $productos_reserva = array();

        if ($mostrar_reserva[2] != '') {
            $productos_reserva = json_decode($mostrar_reserva[2], true);
            $pos += count($productos_reserva);
        }

        foreach ($productos_reserva as $a => $producto) {
            if ($cod_producto == $producto['codigo']) {
                if ($producto['estado'] == 'EN ESPERA') {
                    $productos_reserva[$a]['cant'] += $cant;
                    $encontrado = 1;
                    $inventario -= $cant;
                }
            }
        }

        if (!isset($encontrado)) {
            $productos_reserva[$pos]['codigo'] = $cod_producto;
            $productos_reserva[$pos]['cant'] = $cant;
            $productos_reserva[$pos]['descripcion'] = $mostrar_producto[1];
            $productos_reserva[$pos]['valor_unitario'] = $valor_unitario;
            $productos_reserva[$pos]['estado'] = 'EN ESPERA';
            $productos_reserva[$pos]['area'] = $area;
            $productos_reserva[$pos]['fecha_registro'] = $fecha_h;
            $productos_reserva[$pos]['creador'] = $usuario;
            $inventario -= $cant;
        }

        if ($mostrar_producto[9] == 'Producto') {
            if ($validar_stock == 'SI') {
                if ($cant > $stock)
                    $verificacion = 'El inventario para ' . $mostrar_producto[1] . ' es: ' . $stock;
            }

            if ($verificacion == 1) {
                $inventario = json_encode($inventario, JSON_UNESCAPED_UNICODE);
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
        $verificacion = 'No se pueden agregar productos porque la caja NO se encuentra abierta';
} else
    $verificacion = 'Reload';


$datos = array(
    'consulta' => $verificacion,
    'cod_categoria' => $cod_categoria
);

echo json_encode($datos);
