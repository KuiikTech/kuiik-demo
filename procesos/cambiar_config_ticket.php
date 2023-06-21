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
        $estado = $_POST['estado'];

        $sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Ticket'";
        $result = mysqli_query($conexion, $sql);
        $ver = mysqli_fetch_row($result);

        if ($ver != null) {
            $ticket = preg_replace("/[\r\n|\n|\r]+/", " ", $ver[2]);
            $ticket = str_replace('  ', ' ', $ticket);
            $ticket = json_decode($ticket, true);

            $ticket[$item]['estado'] = $estado;
            $ticket[$item]['fecha_modificacion'] = $fecha_h;
            $ticket[$item]['modificador'] = $usuario;

            $ticket = json_encode($ticket,JSON_UNESCAPED_UNICODE);

            $sql = "UPDATE `configuraciones` SET 
                `valor`='$ticket'
                WHERE descripcion='Ticket'";

            $verificacion = mysqli_query($conexion, $sql);
        }
        else
            $verificacion = 'No se encontró la configuración de ticket';
    } else
        $verificacion = 'Usted no tiene permisos para cambiar esta configuración';
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion
);

echo json_encode($datos);
