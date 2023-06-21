<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();
$conexion = $obj_2->conexion();
$conexion = $obj_2->conexion();

$usuario = $_SESSION['usuario_restaurante'];

$verificacion = 1;

$num_item = $_POST['num_item'];
$cod_mesa = $_POST['cod_mesa'];
$valor_nuevo = str_replace('.', '', $_POST['valor_nuevo']);

if ($valor_nuevo != '') {

	$sql_mesa = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura` FROM `mesas` WHERE cod_mesa = '$cod_mesa'";
	$result_mesa = mysqli_query($conexion, $sql_mesa);
	$mostrar_mesa = mysqli_fetch_row($result_mesa);

	$productos_mesa = json_decode($mostrar_mesa[2], true);
	$productos_mesa[$num_item]['valor_unitario'] = $valor_nuevo;

	$cod_pedido = $productos_mesa[$num_item]['cod_pedido'];

	$sql_pedido = "SELECT `codigo`, `producto`, `cantidad`, `valor`, `mesa`, `solicitante`, `fecha_registro`, `fecha_entrega`, `estado` FROM `pedidos_mesas` WHERE codigo = '$cod_pedido'";
	$result_pedido = mysqli_query($conexion, $sql_pedido);
	$mostrar_pedido = mysqli_fetch_row($result_pedido);

	if ($mostrar_pedido[8] == 'PENDIENTE')
		$productos_mesa[$num_item]['cambio'] = 1;

	$productos_mesa = json_encode($productos_mesa, JSON_UNESCAPED_UNICODE);
	$sql = "UPDATE `mesas` SET 
`productos`='$productos_mesa'
WHERE cod_mesa='$cod_mesa'";

	$verificacion = mysqli_query($conexion, $sql);

	$sql = "UPDATE `pedidos_mesas` SET 
`valor`='$valor_nuevo'
WHERE codigo='$cod_pedido'";

	$verificacion = mysqli_query($conexion, $sql);
} else
	$verificacion = 'Ingrese un valor válido';

$datos = array(
	'consulta' => $verificacion
);

echo json_encode($datos);
