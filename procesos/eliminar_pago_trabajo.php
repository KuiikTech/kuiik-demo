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

	if (isset($_SESSION['pagos_trabajo']))
	{
		unset($_SESSION['pagos_trabajo'][$num_item]);
		$pagos_trabajo = $_SESSION['pagos_trabajo'];
		$pos = 1;
		foreach ($pagos_trabajo as $i => $item)
		{
			$pagos_trabajo_nuevos[$pos] = $item;
			$pos ++;
		}
		if($pos == 1)
			unset($_SESSION['pagos_trabajo']);
		else
			$_SESSION['pagos_trabajo'] = $pagos_trabajo_nuevos;
	}
	else
		$verificacion = 'No existen items agregados';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
