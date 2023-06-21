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

	$verificacion = 'No se ha selecionado ninuna opción';

	if (isset($_POST['hora_inicial']))
	{
		$valor = $_POST['hora_inicial'];
		$sql="UPDATE `configuraciones` SET 
		`valor`='$valor'
		WHERE descripcion='Hora Inicial'";

		$verificacion = mysqli_query($conexion,$sql);
	}

	if (isset($_POST['hora_final']))
	{
		$valor = $_POST['hora_final'];
		$sql="UPDATE `configuraciones` SET 
		`valor`='$valor'
		WHERE descripcion='Hora Final'";

		$verificacion = mysqli_query($conexion,$sql);
	}

	if (isset($_POST['cliente_agenda']))
	{
		$valor = $_POST['cliente_agenda'];
		$sql="UPDATE `configuraciones` SET 
		`valor`='$valor'
		WHERE descripcion='Cliente Agenda'";

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
