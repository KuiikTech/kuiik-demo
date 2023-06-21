<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();
$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();
$fecha_h=date('Y-m-d G:i:s');

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];
	$verificacion = 'No se encontró la operación solicitada';

	if (isset($_POST['cod_cliente']))
		$verificacion = $obj->eliminar_cliente($_POST['cod_cliente'],$usuario);

	if (isset($_POST['cod_proveedor']))
		$verificacion = $obj->eliminar_proveedor($_POST['cod_proveedor'],$usuario);

	if (isset($_POST['cod_producto']))
		$verificacion = $obj->eliminar_producto($_POST['cod_producto'],$usuario);

	if (isset($_POST['cod_insumo']))
		$verificacion = $obj->eliminar_insumo($_POST['cod_insumo'],$usuario);

	if (isset($_POST['cod_mesa']))
		$verificacion = $obj->eliminar_mesa($_POST['cod_mesa'],$usuario);

	if (isset($_POST['cod_usuario']))
		$verificacion = $obj->eliminar_usuario($_POST['cod_usuario'],$usuario);

}
else
	$verificacion = 'Reload';

if(!isset($datos))
{
	$datos=array(
		'consulta' => $verificacion
	);
}

echo json_encode($datos);
?>