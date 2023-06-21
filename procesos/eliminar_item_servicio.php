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
	$cod_espacio = $_POST['cod_espacio'];

	$sql_espacio = "SELECT `codigo`, `nombre`, `items`, `fecha_creacion`, `cod_cliente`, `pagos`, `informacion`, `caja` FROM `espacios` WHERE codigo = '$cod_espacio'";
	$result_espacio=mysqli_query($conexion,$sql_espacio);
	$mostrar_espacio=mysqli_fetch_row($result_espacio);

	$items_servicio = array();
	$items_servicio_nuevos = array();
	if($mostrar_espacio[2] != '')
	{
		$items_servicio = json_decode($mostrar_espacio[2],true);

		$pos = 1;
		foreach ($items_servicio as $i => $item)
		{
			if($i != $num_item)
			{
				$items_servicio_nuevos[$pos] = $item;
				$pos ++;
			}
		}

		if($pos == 1)
			$items_servicio_nuevos = '';
		else
			$items_servicio_nuevos = json_encode($items_servicio_nuevos,JSON_UNESCAPED_UNICODE);

		$sql="UPDATE `espacios` SET `items`='$items_servicio_nuevos' WHERE codigo='$cod_espacio'";

		$verificacion = mysqli_query($conexion,$sql);
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
