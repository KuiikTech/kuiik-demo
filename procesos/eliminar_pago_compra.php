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
    $item = $_POST['item'];

    if (isset($_SESSION['usuario_restaurante2']))
        $local = 'Restaurante 2';
    else
        $local = 'Restaurante 1';

    $sql = "SELECT `codigo`, `productos`, `proveedor`, `creador`, `fecha_registro`, `observaciones`, `estado`, `pagos` FROM `compras` WHERE codigo = '$cod_compra'";
    $result = mysqli_query($conexion, $sql);
    $mostrar = mysqli_fetch_row($result);

    $pagos = array();
    $pagos_nuevos = array();
    if ($mostrar[7] != '')
        $pagos = json_decode($mostrar[7], true);
    $pos = 1;
    foreach ($pagos as $p => $pago) {
        if ($p != $item) {
            $pagos_nuevos[$pos] = $pago;
            $pos++;
        }
    }
    if ($pos > 1)
        $pagos_nuevos = json_encode($pagos_nuevos, JSON_UNESCAPED_UNICODE);
    else
        $pagos_nuevos = '';

    $sql = "UPDATE `compras` SET `pagos`='$pagos_nuevos' WHERE codigo='$cod_compra'";

    $verificacion = mysqli_query($conexion, $sql);
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion
);

echo json_encode($datos);
