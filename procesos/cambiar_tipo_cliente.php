<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();
$conexion = $obj_2->conexion();
$fecha_h = date('Y-m-d G:i:s');

require_once "../clases/permisos.php";
$obj_permisos = new permisos();

if (isset($_SESSION['usuario_restaurante'])) {
    $usuario = $_SESSION['usuario_restaurante'];

    $sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
    $result_e = mysqli_query($conexion, $sql_e);
    $ver_e = mysqli_fetch_row($result_e);

    $cedula = $ver_e[1];

    $nombre_usuario = $ver_e[2] . ' ' . $ver_e[3];
    $rol = $ver_e[5];

    if ($rol == 'Administrador') {

        $cod_cliente =   $_POST['cod_cliente'];

        $sql = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro`, `tipo` FROM `clientes` WHERE codigo='$cod_cliente'";
        $result = mysqli_query($conexion, $sql);
        $ver = mysqli_fetch_row($result);

        if ($ver != null) {
            $tipo = $ver[8];

            if ($tipo == '')
                $tipo_nuevo = 'Especial';
            else
                $tipo_nuevo = '';

            $sql = "UPDATE `clientes` SET `tipo`='$tipo_nuevo' WHERE codigo='$cod_cliente'";
            $verificacion = mysqli_query($conexion, $sql);
        } else
            $verificacion = 'El cliente no existe';
    } else
        $verificacion = 'Usted no tiene permisos para cambiar el tipo de cliente';
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion
);
echo json_encode($datos);
