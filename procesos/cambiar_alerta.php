<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();
$conexion = $obj_2->conexion();
$conexion = $obj_2->conexion();

if (isset($_SESSION['usuario_restaurante'])) {
    $usuario = $_SESSION['usuario_restaurante'];

    $verificacion = 'Ha ocurrido un error actualice y vuelva a intentarlo.';
    $estado = '';

    $cod_producto = $_POST['cod_producto'];
    $alerta = $_POST['alerta'];

    if($alerta < 0){
        $alerta = 0;
    } 

    $sql = "UPDATE `productos` SET 
	`alerta`='$alerta'
	WHERE codigo='$cod_producto'";

    $verificacion = mysqli_query($conexion, $sql);
} else {
    $verificacion = 'Reload';
}

$datos = array(
    'consulta' => $verificacion,
    'alerta' => $alerta
);

echo json_encode($datos);
