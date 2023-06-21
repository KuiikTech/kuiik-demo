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

	$cod_reserva = $_POST['cod_reserva'];
	$cod_cliente = $_POST['cod_cliente'];

	if($cod_cliente == '')
		$sql="UPDATE `reservas` SET `cod_cliente`= NULL WHERE codigo='$cod_reserva'";
	else
		$sql="UPDATE `reservas` SET `cod_cliente`='$cod_cliente' WHERE codigo='$cod_reserva'";

	$verificacion = mysqli_query($conexion,$sql);
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>