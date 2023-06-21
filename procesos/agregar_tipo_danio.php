<?php 
date_default_timezone_set('America/Bogota');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();

$fecha_h=date('Y-m-d G:i:s');

$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$verificacion = 1;

	$nombre = $_POST['nombre'];

	if ($nombre == '')
		$verificacion = 'Ingrese el nombre del equipo';

	if ($verificacion == 1)
	{
		$sql="INSERT INTO `tipo_daños`(`nombre`, `estado`, `creador`, `fecha_creacion`) VALUES (
		'$nombre',
		'ACTIVO',
		'$usuario',
		'$fecha_h')";

		$verificacion = mysqli_query($conexion,$sql);
	}
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
