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

	$descripcion_venta = $_POST['input_descripcion_venta'];
	$metodo = substr($_POST['input_metodo'], 0,-2);
	$tipo_venta = $_POST['input_tipo_venta'];
	$valor_venta=str_replace('.', '', $_POST['input_valor_venta']);

	if ($valor_venta == '')
		$verificacion = 'Ingrese el valor de la venta';

	if ($verificacion == 1)
	{
		$descripcion = $tipo_venta;
		if($descripcion_venta != '')
			$descripcion .= ': '.$descripcion_venta;

		$sql="INSERT INTO `ventas_directas`(`descripcion`, `valor`, `estado`, `creador`, `metodo`, `fecha_registro`) VALUES (
		'$descripcion',
		'$valor_venta',
		'PENDIENTE',
		'$usuario',
		'$metodo',
		'".date('Y-m-d G:i:s')."')";

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
