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

	$cod_caja = $_POST['cod_caja'];
	$caja = $_POST['caja'];
	$cantidad_inicial = $_POST['cantidad_inicial'];

	$sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$usuario'";
	$result_e=mysqli_query($conexion,$sql_e);
	$ver_e=mysqli_fetch_row($result_e);

	$rol = $ver_e[1];
	if($caja == 1)
	{
		$sql="UPDATE `caja` SET 
		`kilos_inicio`='$cantidad_inicial'
		WHERE codigo='$cod_caja'";
		$verificacion = mysqli_query($conexion,$sql);
	}
	else if($caja == 2)
	{
		$sql="UPDATE `caja2` SET 
		`kilos_inicio`='$cantidad_inicial'
		WHERE codigo='$cod_caja'";
		$verificacion = mysqli_query($conexion,$sql);
	}
	else if($caja == 3)
	{
		$sql="UPDATE `caja3` SET 
		`kilos_inicio`='$cantidad_inicial'
		WHERE codigo='$cod_caja'";
		$verificacion = mysqli_query($conexion,$sql);
	}
	else
		$verificacion = 'No se encontró la caja seleccionada';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>