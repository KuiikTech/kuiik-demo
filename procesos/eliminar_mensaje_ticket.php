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

        $item = $_POST['item'];

        $sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Mensaje Ticket'";
        $result = mysqli_query($conexion, $sql);
        $ver = mysqli_fetch_row($result);

        $nuevos_mensajes = array();
        if ($ver != null) {
            if ($ver[2] != '') {
                $mensaje = preg_replace("/[\r\n|\n|\r]+/", " ", $ver[2]);
                $mensaje = str_replace('  ', ' ', $mensaje);
                $mensaje = json_decode($mensaje, true);
                $pos = 1;
                foreach ($mensaje as $key => $value) {
                    if ($key != $item) {
                        $nuevos_mensajes[$pos] = $value;
                        $pos++;
                    }
                }
                if ($pos == 1)
                    $nuevos_mensajes = '';
                else
                    $nuevos_mensajes = json_encode($nuevos_mensajes, JSON_UNESCAPED_UNICODE);
                $sql = "UPDATE `configuraciones` SET 
                    `valor`='$nuevos_mensajes'
                    WHERE descripcion='Mensaje Ticket'";

                $verificacion = mysqli_query($conexion, $sql);
            }
        } else
            $verificacion = 'No se encontró el mensaje seleccionado';
    } else
        $verificacion = 'Usted no tiene permisos para cambiar esta configuración';
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion
);

echo json_encode($datos);
