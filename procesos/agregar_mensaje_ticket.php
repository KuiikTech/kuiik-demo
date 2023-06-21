<?php
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();

$obj = new conectar();
$conexion = $obj->conexion();

$fecha_h = date('Y-m-d G:i:s');

if (isset($_SESSION['usuario_restaurante'])) {
    $usuario = $_SESSION['usuario_restaurante'];

    require_once "../clases/permisos.php";
    $obj_permisos = new permisos();
    $acceso = $obj_permisos->buscar_permiso($usuario, 'Config PDV', 'GENERAL');

    if ($acceso == 'SI') {

        $text = $_POST['mensaje'];

        if ($text != '') {

            $sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Mensaje Ticket'";
            $result = mysqli_query($conexion, $sql);
            $ver = mysqli_fetch_row($result);
            $pos = 1;
            $mensaje = array();
            if ($ver != null) {
                if ($ver[2] != '') {
                    $mensaje = preg_replace("/[\r\n|\n|\r]+/", " ", $ver[2]);
                    $mensaje = str_replace('  ', ' ', $mensaje);
                    $mensaje = json_decode($mensaje, true);

                    $pos += count($mensaje);
                }
            } else {
                $sql = "INSERT INTO `configuraciones`(`descripcion`, `valor`) VALUES ('Mensaje Ticket','')";
                $result = mysqli_query($conexion, $sql);
            }

            $mensaje[$pos]['text'] = $text;
            $mensaje[$pos]['fecha_registro'] = $fecha_h;
            $mensaje[$pos]['creador'] = $usuario;

            $mensaje = json_encode($mensaje, JSON_UNESCAPED_UNICODE);
            $sql = "UPDATE `configuraciones` SET 
            `valor`='$mensaje'
            WHERE descripcion='Mensaje Ticket'";

            $verificacion = mysqli_query($conexion, $sql);
        } else
            $verificacion = 'Ingrese un mensaje';
    } else
        $verificacion = 'Usted no tiene permisos para cambiar esta configuración';
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion
);

echo json_encode($datos);
