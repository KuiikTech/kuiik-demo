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
    $item = $_POST['item'];

    if (isset($_SESSION['usuario_restaurante2']))
        $local = 'Restaurante 2';
    else
        $local = 'Restaurante 1';

    $sql = "SELECT `codigo`, `productos`, `proveedor`, `creador`, `fecha_registro`, `observaciones`, `estado`, `pagos`, `notas` FROM `compras` WHERE codigo = '$cod_compra'";
    $result = mysqli_query($conexion, $sql);
    $mostrar = mysqli_fetch_row($result);

    $notas = array();
    $notas_nuevas = array();
    if ($mostrar[8] != '')
        $notas = json_decode($mostrar[8], true);

    $pos = 1;
    foreach ($notas as $n => $nota) {
        if ($n != $item) {
            $notas_nuevas[$pos] = $nota;
            $pos++;
        }
    }
    if ($pos > 1)
        $notas_nuevas = json_encode($notas_nuevas, JSON_UNESCAPED_UNICODE);
    else
        $notas_nuevas = '';

    $sql = "UPDATE `compras` SET `notas`='$notas_nuevas' WHERE codigo='$cod_compra'";

    $verificacion = mysqli_query($conexion, $sql);
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion
);

echo json_encode($datos);
?>