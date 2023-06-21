<?php
require_once "../clases/conexion.php";

$obj = new conectar();
$conexion = $obj->conexion();
session_set_cookie_params(7 * 24 * 60 * 60);
session_start();

$reservas = array();
if (isset($_SESSION['usuario_restaurante'])) {
    $usuario = $_SESSION['usuario_restaurante'];
    $tipo = $_POST['tipo'];

    $verificacion = 1;

    $sql_caja = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `egresos`, `creador`, `cajero`, `finalizador`, `estado`, `info`, `kilos_inicio`, `kilos_fin` FROM `caja` WHERE estado = 'ABIERTA'";
    $result_caja = mysqli_query($conexion, $sql_caja);
    $mostrar_caja = mysqli_fetch_row($result_caja);

    if ($mostrar_caja != null) {
        $fecha_inicio = $mostrar_caja[2];


        $sql = "SELECT `codigo`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_registro`, `cod_cliente`, `pagos`, `fecha_llegada`, `descuentos`, `code`, `creador` FROM `reservas` WHERE estado = '$tipo' ORDER BY `fecha_llegada` ASC";
        $result = mysqli_query($conexion, $sql);

        $orden = 1;

        while ($mostrar = mysqli_fetch_row($result)) {
            $cod_reserva = $mostrar[0];

            $reservas[$orden] = $cod_reserva;

            $orden++;
        }
    }
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion,
    'reservas' => $reservas,
    'cant' => $orden
);
echo json_encode($datos);
