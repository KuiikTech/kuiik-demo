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

	$num_resolucion = $_POST['num_resolucion'];
	$prefijo = $_POST['prefijo'];
	$sufijo = $_POST['sufijo'];
	$inicio = $_POST['inicio'];
	$fin = $_POST['fin'];
	$fecha_resolucion = $_POST['fecha_resolucion'];
	$vigencia = $_POST['vigencia'];

	$sql="INSERT INTO `resoluciones`(`prefijo`, `sufijo`, `inicio`, `fin`, `actual`, `fecha_resolucion`, `estado`, `numero`, `fecha_registro`, `vigencia`) VALUES (
		'$prefijo',
		'$sufijo',
		'$inicio',
		'$fin',
		'$inicio',
		'$fecha_resolucion',
		'INACTIVO',
		'$num_resolucion',
		'$fecha_h',
		'$vigencia')";

	$verificacion = mysqli_query($conexion,$sql);
}

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
