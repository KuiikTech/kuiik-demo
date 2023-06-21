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
    $cod_compra = $_POST['cod_compra'];

    $sql = "UPDATE `compras` SET `factura`='' WHERE codigo='$cod_compra'";

    $verificacion = mysqli_query($conexion, $sql);
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion
);

echo json_encode($datos);
