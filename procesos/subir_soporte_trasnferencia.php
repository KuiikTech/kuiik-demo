<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();
$conexion = $obj_2->conexion();
$conexion = $obj_2->conexion();
$fecha_h = date('Y-m-d G:i:s');

require_once "../clases/permisos.php";
$obj_permisos = new permisos();

if (isset($_SESSION['usuario_restaurante'])) {
    $usuario = $_SESSION['usuario_restaurante'];

    $cod_servicio_upload = $_POST['cod_servicio_upload_transferencia'];
    $item_upload = $_POST['item_subir'];

    $imagen_soporte = $_FILES['archivo_soporte']['tmp_name'];

    if ($_FILES['archivo_soporte']['name'] != '') {
        if (is_uploaded_file($imagen_soporte)) {
            if ($_FILES['archivo_soporte']['type'] == "image/jpeg" or $_FILES['archivo_soporte']['type'] == "image/png") {
                if ($_FILES['archivo_soporte']['size'] < 5000000) {
                    $codigo = uniqid();
                    $destino = __DIR__ . '/../../rancho1.Kuiik.co/paginas/soportes_transferencias/' . $codigo . '.jpg';
                    if (!file_exists($destino))
                        $destino = __DIR__ . '/../paginas/soportes_transferencias/' . $codigo . '.jpg';
                    $nombre_imagen = $codigo . '.jpg';
                    if (move_uploaded_file($imagen_soporte, $destino)) {
                        $cod_servicio = $cod_servicio_upload;

                        $sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro`, `local` FROM `servicios` WHERE codigo = '$cod_servicio'";
                        $result = mysqli_query($conexion, $sql);
                        $mostrar = mysqli_fetch_row($result);

                        $pagos = array();
                        if ($mostrar[3] != '')
                            $pagos = json_decode($mostrar[3], true);

                        $pagos[$item_upload]['nombre_imagen'] = $nombre_imagen;
                        $pagos[$item_upload]['fecha_subida'] = $fecha_h;
                        $pagos[$item_upload]['usuario_subida'] = $usuario;

                        $pagos = json_encode($pagos, JSON_UNESCAPED_UNICODE);

                        $sql = "UPDATE `servicios` SET 
						`pagos`='$pagos'
						WHERE codigo='$cod_servicio_upload'";

                        $verificacion = mysqli_query($conexion, $sql);
                    } else
                        $verificacion = 'Error al subir la Imagen';
                } else
                    $verificacion = 'Peso maximo de la imagen 5MB';
            } else
                $verificacion = 'Seleccione una imagen valida';
        } else
            $verificacion = 'Seleccione una imagen valida';
    } else
        $verificacion = 'Seleccione la imagen del soporte de pago';
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion
);
echo json_encode($datos);
