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

	$num_item = $_POST['num_item'];

	if (isset($_SESSION['lista_servicios']))
	{
		unset($_SESSION['lista_servicios'][$num_item]);
		$lista_servicios = $_SESSION['lista_servicios'];
		$pos = 1;
		foreach ($lista_servicios as $i => $item)
		{
			$lista_servicios_nuevos[$pos] = $item;
			$pos ++;
		}
		if($pos == 1)
			unset($_SESSION['lista_servicios']);
		else
			$_SESSION['lista_servicios'] = $lista_servicios_nuevos;
	}
	else
		$verificacion = 'No existen servicios agregados';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
