<?php 
date_default_timezone_set('America/Bogota');

session_set_cookie_params(7*24*60*60);
session_start();

require_once "../clases/conexion.php";

$obj= new conectar();
$conexion=$obj->conexion();

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$info_registro = array(
		'Ubicación' => $_SERVER['HTTP_USER_AGENT']
	);

	$info_registro = array(
		'Tipo' => 'Cierre de Sesión',
		'Información' => $info_registro
	);

	$info_registro = json_encode($info_registro,JSON_UNESCAPED_UNICODE);

	$sql="INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
	'$info_registro',
	'$usuario',
	'".date('Y-m-d G:i:s')."')";
	$verificacion = mysqli_query($conexion,$sql);

	session_destroy();
}

header("Location:../login.php");
?>