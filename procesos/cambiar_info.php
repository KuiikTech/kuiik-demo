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

	if ($_POST['input_telefono'] == '')
		$verificacion = 'Ingrese su número de teléfono';	
	if ($_POST['input_apellido'] == '')
		$verificacion = 'Ingrese su apellido';
	if ($_POST['input_nombre'] == '')
		$verificacion = 'Ingrese su nombre';

	if($verificacion == 1)
	{
		$datos=array(
			$_POST['cod_usuario'],
			ucwords($_POST['input_nombre']),
			ucwords($_POST['input_apellido']),
			$_POST['input_telefono'],
			$_POST['input_color']
		);

		$verificacion = $obj->cambiar_info_usuario($datos);
	}
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);
?>