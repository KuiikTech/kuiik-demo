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

    $pos = $_POST['item'];
    $valor = str_replace('.', '', $_POST['valor']);
    $cod_reserva = $_POST['cod_reserva'];

    if ($valor != '') {

        $sql_reserva = "SELECT `codigo`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_registro`, `cod_cliente`, `pagos`, `fecha_llegada`, `descuentos`, `code`, `creador` FROM `reservas` WHERE codigo = '$cod_reserva'";
        $result_reserva = mysqli_query($conexion, $sql_reserva);
        $mostrar_reserva = mysqli_fetch_row($result_reserva);

        $productos_reserva = array();
        if ($mostrar_reserva[3] != '')
            $productos_reserva = json_decode($mostrar_reserva[3], true);

        if (isset($productos_reserva[$pos])) {
            $productos_reserva[$pos]['valor_unitario'] = $valor;
            $productos_reserva = json_encode($productos_reserva, JSON_UNESCAPED_UNICODE);

            $sql = "UPDATE `reservas` SET 
        `productos`='$productos_reserva'
        WHERE codigo='$cod_reserva'";

            $verificacion = mysqli_query($conexion, $sql);
        } else
            $verificacion = 'No se encontró el producto en la reserva';
    } else
        $verificacion = 'Ingrese un valor válido';
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion
);

echo json_encode($datos);
