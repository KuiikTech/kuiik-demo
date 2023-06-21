<?php
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();

$obj = new conectar();
$conexion = $obj->conexion();

$fecha_h = date('Y-m-d G:i:s');

if (isset($_SESSION['usuario_rancho'])) {
    $usuario = $_SESSION['usuario_rancho'];

    require_once "../clases/permisos.php";
    $obj_permisos = new permisos();
    $acceso = $obj_permisos->buscar_permiso($usuario, 'Config PDV', 'GENERAL');

    if ($acceso == 'SI') {
        $tiempo_alarma = $_POST['tiempo_alarma'];

        if ($tiempo_alarma != '') {
            $sql = "UPDATE `configuraciones` SET 
            `valor`='$tiempo_alarma'
            WHERE descripcion='Alerta Pedido'";

            $verificacion = mysqli_query($conexion, $sql);
        } else
            $verificacion = 'Ingrese el tiempo de alarme en minutos';
    } else
        $verificacion = 'Usted no tiene permisos para cambiar esta configuración';
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion
);

echo json_encode($datos);
