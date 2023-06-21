<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$sql_f = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Fecha Ranking Ventas'";
$result_f = mysqli_query($conexion, $sql_f);
$ver_f = mysqli_fetch_row($result_f);

$fecha_especiales = $ver_f[2];

$fecha_inicial = date("Y-m-d 00:00:00", strtotime($fecha_especiales));
$fecha_final = date("Y-m-d 23:59:59", strtotime($fecha));

$totales_especiales = array();

$sql_pedido = "SELECT `codigo`, `productos`, `mesa`, `solicitante`, `fecha_registro`, `fecha_envio`, `fecha_entrega`, `estado`, `area` FROM `pedidos` WHERE estado = 'TERMINADO' AND fecha_registro BETWEEN '$fecha_inicial' AND '$fecha_final' ORDER BY solicitante ASC";
$result_pedido = mysqli_query($conexion, $sql_pedido);

while ($mostrar_pedido = mysqli_fetch_row($result_pedido)) {
    $cod_producto = $mostrar_pedido[1];

    $productos = array();
    if ($mostrar_pedido[1] != null)
        $productos = json_decode($mostrar_pedido[1], true);

    foreach ($productos as $i => $producto) {
        $cod_producto = $producto['codigo'];
        $cant = $producto['cant'];
        $estado_producto = $producto['estado'];

        if ($estado_producto == 'DESPACHADO') {

            if (isset($producto['creador']))
                $solicitante = $producto['creador'];
            else
                $solicitante = $mostrar_pedido[3];

            if ($solicitante != 0 && $solicitante != 1) {
                $sql_e = "SELECT nombre, apellido, rol, foto FROM usuarios WHERE codigo = '$solicitante'";
                $result_e = mysqli_query($conexion, $sql_e);
                $ver_e = mysqli_fetch_row($result_e);

                if (!isset($totales_especiales[$solicitante]['cant']))
                    $totales_especiales[$solicitante]['cant'] = 0;

                if ($ver_e != null)
                    $totales_especiales[$solicitante]['usuario'] = $ver_e[0] . ' ' . $ver_e[1];
                else
                    $totales_especiales[$solicitante]['usuario'] = '---';

                $sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode`, `movimientos`, `especial` FROM `productos` WHERE codigo='$cod_producto'";
                $result_producto = mysqli_query($conexion, $sql_producto);
                $mostrar_producto = mysqli_fetch_row($result_producto);
                if ($mostrar_producto != null) {
                    $tipo_especial = $mostrar_producto[13];
                    if ($tipo_especial == 'SI')
                        $totales_especiales[$solicitante]['cant'] += $cant;
                }
            }
        }
    }
}
arsort($totales_especiales);
?>

<ul class="list-group bg-transparent p-0">
    <?php
    $num_user = 1;
    foreach ($totales_especiales as $i => $usuario) {
        $nombre_usuario = substr($usuario['usuario'], 0, 15);
        $icono = '<span></span>';
        if ($num_user == 1)
            $icono = '<span class="fa fa-star text-warning"></span>';
    ?>
        <li class="list-group-item d-flex justify-content-between align-items-center p-1 bg-transparent border-0">
            <?php echo $icono ?>
            <strong class="mr-1"><?php echo $nombre_usuario ?></strong>
            <span class="badge badge-soft-warning rounded-pill"><?php echo $usuario['cant'] ?></span>
        </li>
    <?php
        $num_user++;
    }
    ?>
</ul>