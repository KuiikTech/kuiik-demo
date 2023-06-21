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
    $cod_compra = $_POST['cod_compra'];

    if (isset($_SESSION['usuario_restaurante2']))
        $local = 'Restaurante 2';
    else
        $local = 'Restaurante 1';

    $tipo_nota = $_POST['input_tipo_nota'];
    $valor_nota = str_replace('.', '', $_POST['input_valor_nota']);
    $observacion = $_POST['input_observacion'];

    if ($observacion == '')
        $verificacion = 'Escriba la observación para la nota';
    if ($valor_nota == '')
        $verificacion = 'Ingrese el valor de la nota';
    if ($tipo_nota == '')
        $verificacion = 'Seleccione un tipo de nota';

    if ($verificacion == 1) {
        $sql = "SELECT `codigo`, `productos`, `proveedor`, `creador`, `fecha_registro`, `observaciones`, `estado`, `pagos`, `notas` FROM `compras` WHERE codigo = '$cod_compra'";
        $result = mysqli_query($conexion, $sql);
        $mostrar = mysqli_fetch_row($result);

        $notas = array();
        $pos = 1;
        if ($mostrar[8] != '') {
            $notas = json_decode($mostrar[8], true);
            $pos += count($notas);
        }

        $notas[$pos] = array(
            'tipo' => $tipo_nota,
            'valor' => $valor_nota,
            'observacion' => $observacion,
            'local' => $local,
            'creador' => $usuario,
            'fecha_creacion' => $fecha_h,
        );

        $notas = json_encode($notas, JSON_UNESCAPED_UNICODE);

        $sql = "UPDATE `compras` SET `notas`='$notas' WHERE codigo='$cod_compra'";

        $verificacion = mysqli_query($conexion, $sql);
    }
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion
);

echo json_encode($datos);
