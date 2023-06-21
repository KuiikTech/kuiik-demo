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

	$cod_espacio = $_POST['cod_espacio'];
	$input_daño = $_POST['input_daño'];
	$input_observacion = $_POST['input_observacion'];

	if ($input_daño == '')
		$verificacion = 'Seleccione el tipo de daño del equipo';

	if ($verificacion == 1)
	{
		$sql_espacio = "SELECT `codigo`, `nombre`, `items`, `fecha_creacion`, `cod_cliente`, `pagos`, `informacion`, `caja` FROM `espacios` WHERE codigo = '$cod_espacio'";
		$result_espacio=mysqli_query($conexion,$sql_espacio);
		$mostrar_espacio=mysqli_fetch_row($result_espacio);

		$items_servicio = array();
		if($mostrar_espacio[2] != '')
			$items_servicio = json_decode($mostrar_espacio[2],true);

		$pos = count($items_servicio)+1;

		$items_servicio[$pos]['codigo'] = NULL;
		$items_servicio[$pos]['daño'] = $input_daño;
		$items_servicio[$pos]['observaciones'] = $input_observacion;

		$items_servicio = json_encode($items_servicio,JSON_UNESCAPED_UNICODE);

		$sql="UPDATE `espacios` SET `items`='$items_servicio' WHERE codigo='$cod_espacio'";

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
