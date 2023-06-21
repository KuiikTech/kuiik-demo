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

	$cod_servicio = $_POST['cod_servicio'];

	if($cod_servicio != '')
	{
		$sql = "SELECT `codigo` FROM `servicios` WHERE codigo = '$cod_servicio'";
		$result=mysqli_query($conexion,$sql);
		$mostrar=mysqli_fetch_row($result);

		if ($mostrar == NULL)
			$verificacion = 'No se encontró el servicio solicitado';
	}
	else
		$verificacion = 'Ingrese el código del servicio';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>