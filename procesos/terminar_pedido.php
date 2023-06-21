<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();
$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();

$fecha_h = date('Y-m-d G:i:s');

$usuario = $_SESSION['usuario_restaurante'];

$verificacion = 1;

$cod_pedido = $_POST['cod_pedido'];

$sql = "SELECT `codigo`, `productos`, `mesa`, `solicitante`, `fecha_registro`, `fecha_envio`, `fecha_entrega`, `estado`, `area` FROM `pedidos` WHERE codigo = '$cod_pedido' ";
$result=mysqli_query($conexion,$sql);
$mostrar=mysqli_fetch_row($result);


$productos_pedido = array();
if($mostrar[1] != '')
$productos_pedido = json_decode($mostrar[1],true);

foreach ($productos_pedido as $i => $producto)
{
	if($producto['estado'] == 'PENDIENTE' || $producto['estado'] == 'PREPARANDO')
		$verificacion = 'Despache todos los productos para terminar el pedido';
}

if($verificacion == 1)
{
	$sql="UPDATE `pedidos` SET 
	`estado` = 'TERMINADO',
	`fecha_entrega` = '$fecha_h'
	WHERE codigo='$cod_pedido'";

	$verificacion = mysqli_query($conexion,$sql);
}

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);
