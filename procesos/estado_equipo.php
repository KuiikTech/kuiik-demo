<?php 
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();

$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();

$fecha_h = date('Y-m-d G:i:s');

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$cod_equipo = $_POST['cod_equipo'];
	$estado = $_POST['estado'];

	$sql="UPDATE `tipo_equipos` SET 
	`estado`='$estado'
	WHERE `codigo`='$cod_equipo'";

	$verificacion = mysqli_query($conexion,$sql);
}
else
	$verificacion = 'Reload';
$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>