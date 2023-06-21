<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();
$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();

$fecha_h = date('Y-m-d G:i:s');
if(isset($_SESSION['usuario_restaurante']))
{

	$usuario = $_SESSION['usuario_restaurante'];

	$verificacion = 1;

	$clase = $_POST['clase'];
	$valor = $_POST['valor'];

	$sql="UPDATE `configuraciones` SET 
	`valor`='$valor'
	WHERE descripcion='$clase'";

	$verificacion = mysqli_query($conexion,$sql);
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>