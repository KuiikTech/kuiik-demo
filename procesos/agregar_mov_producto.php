<?php
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();

$fecha = date('Y-m-d');
$fecha_h = date('Y-m-d G:i:s');

$conexion = $obj_2->conexion();
$inventario = 0;
if (isset($_SESSION['usuario_restaurante'])) {
    $usuario = $_SESSION['usuario_restaurante'];

    $verificacion = 1;

    $cod_producto = $_POST['cod_producto'];
    $tipo = $_POST['tipo_mov'];
    $cant =     $_POST['cant_mov'];
    $observaciones = $_POST['obs_mov'];

    $sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode`, `movimientos` FROM `productos` WHERE codigo='$cod_producto'";
    $result_producto = mysqli_query($conexion, $sql_producto);
    $mostrar_producto = mysqli_fetch_row($result_producto);

    if ($mostrar_producto != NULL) {
        $movimientos = array();
        $pos = 1;
        if ($mostrar_producto[12] != '') {
            $movimientos = json_decode($mostrar_producto[12], true);
            $pos += count($movimientos);
        }
        if($tipo == 'Salida')
            $cant = $cant * -1;

        $inventario = $mostrar_producto[4] + $cant;

        if($tipo == 'Ingreso')
            $cant = '+'.$cant;

        $movimientos[$pos] = array(
            'Tipo' => $tipo,
            'Cant' => $cant,
            'creador' => $usuario,
            'Observaciones' => $observaciones,
            'fecha' => $fecha_h
        );

        $movimientos = json_encode($movimientos, JSON_UNESCAPED_UNICODE);

        $sql = "UPDATE `productos` SET 
			`movimientos`='$movimientos',
            `inventario` = $inventario
			WHERE codigo='$cod_producto'";
        $verificacion = mysqli_query($conexion, $sql);
    } else
        $verificacion = 'No se pudo agregar el movimiento. No se encontró el producto seleccionado.(cod: ' . $cod_producto . ')';
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion,
    'inventario' => $inventario,
);
echo json_encode($datos);
