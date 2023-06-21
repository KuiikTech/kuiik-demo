<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();
$conexion = $obj_2->conexion();

$fecha_t = date('Y-m-d G:i:s');

require_once "../clases/permisos.php";
$obj_permisos = new permisos();

if (isset($_SESSION['usuario_restaurante'])) {
    $usuario = $_SESSION['usuario_restaurante'];

    $imagen_logo = $_FILES['archivo_logo_t']['tmp_name'];

    if ($_FILES['archivo_logo_t']['name'] != '') {
        if (is_uploaded_file($imagen_logo)) {
            if ($_FILES['archivo_logo_t']['type'] == "image/jpeg" or $_FILES['archivo_logo_t']['type'] == "image/png") {
                if ($_FILES['archivo_logo_t']['size'] < 5000000) {
                    if ($_FILES['archivo_logo_t']['type'] == "image/png") {
                        $img = imagecreatefrompng($imagen_logo);
                        $destino = __DIR__ . '/../recursos/logo_empresa.png';
                        $file = __DIR__ . '/../recursos/logo_empresa.jpg';
                        if (is_file($file))
                            unlink($file);
                    } else {
                        $img = imagecreatefromjpeg($imagen_logo);
                        $destino = __DIR__ . '/../recursos/logo_empresa.jpg';
                        $file = __DIR__ . '/../recursos/logo_empresa.png';
                        if (is_file($file))
                            unlink($file);
                    }

                    imagefilter($img, IMG_FILTER_GRAYSCALE);
                    imagefilter($img, IMG_FILTER_CONTRAST, -100);

                    if ($_FILES['archivo_logo_t']['type'] == "image/png") {
                        if (imagepng($img, $destino))
                            $verificacion = 1;
                        else
                            $verificacion = 'Error al subir la Imagen';
                    } else {
                        if (imagejpeg($img, $destino))
                            $verificacion = 1;
                        else
                            $verificacion = 'Error al subir la Imagen';
                    }
                    imagedestroy($img);
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
