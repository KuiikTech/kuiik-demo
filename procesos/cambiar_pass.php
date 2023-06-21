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

	$contraseña = $_POST['input_pass_old'];
	$contraseña_1 = $_POST['input_pass_new'];
	$contraseña_2 = $_POST['input_pass_new_2'];

	if ($contraseña == '')
		$verificacion = 'Ingrese su contraseña actual';
	else
	{
		if ($contraseña_1 == '')
			$verificacion = 'Ingrese una contraseña nueva';
		else if ($contraseña_2 == '')
			$verificacion = 'Repita la contraseña nueva';
		else if ($contraseña_1 != $contraseña_2)
			$verificacion = 'Las contraseñas nuevas no coinciden';
		else
		{
			$sql="SELECT `contraseña` FROM `usuarios` WHERE codigo='$usuario'";
			$result=mysqli_query($conexion,$sql);
			$ver=mysqli_fetch_row($result);

			$contraseña_md5 = md5($contraseña);

			$contraseña_md5_nueva = md5($contraseña_1);

			if ($ver[0] == $contraseña_md5)
			{
				$sql="UPDATE `usuarios` set 
				contraseña='$contraseña_md5_nueva'
				where codigo='$usuario'";
				$verificacion = mysqli_query($conexion,$sql);
			}
			else
				$verificacion =  'Contraseña Actual INCORRECTA';
		}
	}
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);
?>