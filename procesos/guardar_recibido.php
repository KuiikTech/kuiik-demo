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

    $cod_mesa = $_POST['cod_mesa'];
    $recibido = str_replace('.', '', $_POST['recibido']);
    $item = $_POST['item'];

    $sql_mesa = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura`, `pagos` FROM `mesas` WHERE cod_mesa = '$cod_mesa'";
    $result_mesa = mysqli_query($conexion, $sql_mesa);
    $mostrar_mesa = mysqli_fetch_row($result_mesa);

    $pagos = array();
    $pagos_nuevos = array();
    $pos = 1;
    if ($mostrar_mesa[5] != '')
        $pagos = json_decode($mostrar_mesa[5], true);

    foreach ($pagos as $j => $pago) {
        if ($j == $item) {
            $pagos_nuevos[$pos] = array(
                'tipo' => $pago['tipo'],
                'valor' => $pago['valor'],
                'fecha' => $pago['fecha'],
                'recibido' => $recibido
            );
        } else
            $pagos_nuevos[$pos] = $pago;

        $pos++;
    }

    $pagos_nuevos = json_encode($pagos_nuevos, JSON_UNESCAPED_UNICODE);

    $sql = "UPDATE `mesas` SET `pagos`='$pagos_nuevos' WHERE cod_mesa='$cod_mesa'";
    $verificacion = mysqli_query($conexion, $sql);
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion
);

echo json_encode($datos);
