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

    $input_fecha = $_POST['input_fecha'];
    $cod_reserva = $_POST['cod_reserva'];

    $sql = "UPDATE `reservas` SET 
        `fecha_llegada`='$input_fecha'
        WHERE codigo='$cod_reserva'";

    $verificacion = mysqli_query($conexion, $sql);
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion
);

echo json_encode($datos);
