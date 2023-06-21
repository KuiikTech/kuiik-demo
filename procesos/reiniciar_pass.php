<?php 
date_default_timezone_set('America/Bogota');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();

$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];
	$verificacion = 1;

	$usuario_pass = $_POST['cod_usuario'];
	$contraseña_1 = '12345';

	$contraseña_md5_nueva = md5($contraseña_1);

	$sql="UPDATE `usuarios` set 
	contraseña='$contraseña_md5_nueva'
	where codigo='$usuario_pass'";
	$verificacion = mysqli_query($conexion,$sql);

}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);
?>