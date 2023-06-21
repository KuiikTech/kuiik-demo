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
    $cod_mesa = $_POST['cod_mesa'];

    if ($valor != '') {

        $sql_mesa = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura` FROM `mesas` WHERE cod_mesa = '$cod_mesa'";
        $result_mesa = mysqli_query($conexion, $sql_mesa);
        $mostrar_mesa = mysqli_fetch_row($result_mesa);

        $productos_mesa = json_decode($mostrar_mesa[2], true);

        if (isset($productos_mesa[$pos])) {
            $productos_mesa[$pos]['valor_unitario'] = $valor;
            $productos_mesa = json_encode($productos_mesa, JSON_UNESCAPED_UNICODE);

            $sql = "UPDATE `mesas` SET 
        `productos`='$productos_mesa'
        WHERE cod_mesa='$cod_mesa'";

            $verificacion = mysqli_query($conexion, $sql);
        } else
            $verificacion = 'No se encontró el producto en la mesa';
    } else
        $verificacion = 'Ingrese un valor válido';
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion
);

echo json_encode($datos);
