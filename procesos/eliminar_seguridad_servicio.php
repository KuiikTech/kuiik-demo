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

	$informacion = array();
	if($mostrar_espacio[6] != '')
		$informacion = json_decode($mostrar_espacio[6],true);

	$items = array();
	$items_nuevos = array();
	if(isset($informacion['seguridad']))
	{
		$items = $informacion['seguridad'];
		$pos = 1;
		foreach ($items as $i => $item)
		{
			if($i != $num_item)
			{
				$items_nuevos[$pos] = $item;
				$pos ++;
			}
		}

		if($pos == 1)
			unset($informacion['seguridad']);
		else
			$informacion['seguridad'] = $items_nuevos;

		$informacion = json_encode($informacion,JSON_UNESCAPED_UNICODE);

		$sql="UPDATE `espacios` SET `informacion`='$informacion' WHERE codigo='$cod_espacio'";

		$verificacion = mysqli_query($conexion,$sql);
	}
	else
		$verificacion = 'No existen items de seruidad agregados';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
