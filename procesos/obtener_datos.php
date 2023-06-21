<?php 

require_once "../clases/conexion.php";
require_once "../clases/crud.php";

$obj= new crud();

if (isset($_POST['cod_cliente']))
	echo json_encode($obj->obten_datos_cliente($_POST['cod_cliente']));

if (isset($_POST['cod_proveedor']))
	echo json_encode($obj->obten_datos_proveedor($_POST['cod_proveedor']));

if (isset($_POST['cod_usuario']))
	echo json_encode($obj->obten_datos_usuario($_POST['cod_usuario']));

if (isset($_POST['cod_producto']))
	echo json_encode($obj->obten_datos_producto($_POST['cod_producto']));

if (isset($_POST['cod_salon']))
	echo json_encode($obj->obten_datos_salon($_POST['cod_salon']));

if (isset($_POST['cod_mesa']))
	echo json_encode($obj->obten_datos_mesa($_POST['cod_mesa']));

if (isset($_POST['reservas']))
	echo json_encode($obj->obten_num_reservas($_POST['reservas']));

?>
