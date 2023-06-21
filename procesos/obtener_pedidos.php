<?php
require_once "../clases/conexion.php";

$obj = new conectar();
$conexion = $obj->conexion();
session_set_cookie_params(7 * 24 * 60 * 60);
session_start();

$pedidos = array();
if (isset($_SESSION['usuario_restaurante'])) {
    $usuario = $_SESSION['usuario_restaurante'];
    $tipo = $_POST['tipo'];
    $area = $_POST['area'];
    
    if($tipo == 'PENDIENTE')
        $order = 'ASC';
    else
        $order = 'DESC';

    $area = "AND area = '$area'";
    $area = '';

    $verificacion = 1;

    $sql_caja = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `egresos`, `creador`, `cajero`, `finalizador`, `estado`, `info`, `kilos_inicio`, `kilos_fin` FROM `caja` WHERE estado = 'ABIERTA'";
    $result_caja = mysqli_query($conexion, $sql_caja);
    $mostrar_caja = mysqli_fetch_row($result_caja);

    if ($mostrar_caja != null) {
        $fecha_inicio = $mostrar_caja[2];
        $sql = "SELECT `codigo`, `productos`, `mesa`, `solicitante`, `fecha_registro`, `fecha_envio`, `fecha_entrega`, `estado`, `area` FROM `pedidos` WHERE fecha_registro > '$fecha_inicio' AND estado = '$tipo' $area ORDER BY `fecha_entrega` $order";
        $result = mysqli_query($conexion, $sql);

        $orden = 1;

        while ($mostrar = mysqli_fetch_row($result)) {
            $cod_pedido = $mostrar[0];

            $pedidos[$orden] = $cod_pedido;

            $orden++;
        }
    }
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion,
    'pedidos' => $pedidos,
    'cant' => $orden
);
echo json_encode($datos);
