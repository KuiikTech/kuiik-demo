<?php
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();

$obj = new conectar();
$conexion = $obj->conexion();

$fecha_h = date('Y-m-d G:i:s');

if (isset($_SESSION['usuario_restaurante'])) {

    $usuario = $_SESSION['usuario_restaurante'];
    $verificacion = 1;
    $cod_servicio = $_POST['cod_servicio'];
    $item = $_POST['item'];

    if (isset($_SESSION['usuario_restaurante2']))
        $local = 'Restaurante 2';
    else
        $local = 'Restaurante 1';

    $sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro`, `local` FROM `servicios` WHERE codigo = '$cod_servicio'";
    $result = mysqli_query($conexion, $sql);
    $mostrar = mysqli_fetch_row($result);

    $informacion = array();

    if ($mostrar[4] != '') {
        $informacion = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[4]);
        $informacion = str_replace('	', ' ', $informacion);
        $informacion = json_decode($informacion, true);
    }

    $fotos = array();
    if (isset($informacion['fotos']))
        $fotos = $informacion['fotos'];

    $pos = 1;
    $fotos_nuevas = array();
    foreach ($fotos as $f => $foto) {
        if ($f != $item) {
            $fotos_nuevas[$pos] = $foto;
            $pos++;
        }
    }

    if ($pos > 1) {
        $informacion['fotos'] = $fotos_nuevas;
        $informacion = json_encode($informacion, JSON_UNESCAPED_UNICODE);
    } else {
        unset($informacion['fotos']);
        $informacion = json_encode($informacion, JSON_UNESCAPED_UNICODE);
    }

    $sql = "UPDATE `servicios` SET `informacion`='$informacion' WHERE codigo='$cod_servicio'";

    $verificacion = mysqli_query($conexion, $sql);
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion
);

echo json_encode($datos);
