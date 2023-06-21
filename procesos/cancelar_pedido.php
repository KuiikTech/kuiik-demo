<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();
$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();

$usuario = $_SESSION['usuario_restaurante'];

$verificacion = 1;

$cod_pedido = $_POST['cod_pedido'];

$sql="UPDATE `pedidos_mesas` SET 
`estado` = 'CANCELADO'
WHERE codigo='$cod_pedido'";

$verificacion = mysqli_query($conexion,$sql);

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>