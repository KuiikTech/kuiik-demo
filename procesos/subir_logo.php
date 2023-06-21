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

    $imagen_logo = $_FILES['archivo_logo']['tmp_name'];

    if ($_FILES['archivo_logo']['name'] != '') {
        if (is_uploaded_file($imagen_logo)) {
            if ($_FILES['archivo_logo']['type'] == "image/jpeg" or $_FILES['archivo_logo']['type'] == "image/png") {
                if ($_FILES['archivo_logo']['size'] < 5000000) {
                    $destino = __DIR__ . '/../recursos/logo_restaurante.png';
                    if (move_uploaded_file($imagen_logo, $destino)) {
                        $verificacion = 1;
                    } else
                        $verificacion = 'Error al subir la Imagen';
                } else
                    $verificacion = 'Peso maximo de la imagen 5MB';
            } else
                $verificacion = 'Seleccione una imagen valida';
        } else
            $verificacion = 'Seleccione una imagen valida';
    } else
        $verificacion = 'Seleccione la imagen del logo';
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion
);
echo json_encode($datos);
