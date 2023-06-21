<?php
date_default_timezone_set('America/Bogota');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();

$fecha_h = date('Y-m-d G:i:s');

$conexion = $obj_2->conexion();
$conexion = $obj_2->conexion();

if (isset($_SESSION['usuario_restaurante'])) {
    $usuario = $_SESSION['usuario_restaurante'];
    $verificacion = 1;

    $cod_caja = $_POST['cod_caja'];
    $observaciones = $_POST['observaciones'];

    $sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$usuario'";
    $result_e = mysqli_query($conexion, $sql_e);
    $ver_e = mysqli_fetch_row($result_e);

    $rol = $ver_e[1];

    $sql_caja = "SELECT * FROM `caja` WHERE codigo = '$cod_caja'";
    $result_caja = mysqli_query($conexion, $sql_caja);
    $ver_caja = mysqli_fetch_assoc($result_caja);

    if ($ver_caja != null) {
        $info = array();
        if ($ver_caja['info'] != '')
            $info = json_decode($ver_caja['info'], true);

        $info['observaciones'] = $observaciones;

        $info = json_encode($info, JSON_UNESCAPED_UNICODE);

        $sql = "UPDATE `caja` SET 
		`info`='$info'
		WHERE codigo='$cod_caja'";
        $verificacion = mysqli_query($conexion, $sql);
    } else
        $verificacion = 'No se encontró la caja seleccionada';
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion
);

echo json_encode($datos);
