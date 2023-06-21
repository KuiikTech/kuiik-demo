<?php 
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();

$obj= new conectar();
$conexion=$obj->conexion();

$fecha_h = date('Y-m-d G:i:s');

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$cod_mesa = $_POST['cod_mesa'];
	$cod_cliente = $_POST['cod_cliente'];

	if($cod_cliente == '')
		$sql="UPDATE `mesas` SET `cod_cliente`= NULL WHERE cod_mesa='$cod_mesa'";
	else
		$sql="UPDATE `mesas` SET `cod_cliente`='$cod_cliente' WHERE cod_mesa='$cod_mesa'";

	$verificacion = mysqli_query($conexion,$sql);
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>