<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();
$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$verificacion = 'Sin Cambios';

	$sql_espacio = "SELECT `codigo`, `nombre`, `items`, `fecha_creacion`, `cod_cliente`, `pagos`, `informacion`, `cambios`, `caja` FROM `espacios` WHERE `cambios` = '1'";
	$result_espacio=mysqli_query($conexion,$sql_espacio);

	while ($mostrar_espacio=mysqli_fetch_row($result_espacio)) 
	{ 
		$cod_espacio = $mostrar_espacio[0];

		$sql="UPDATE `espacios` SET 
		`cambios`='0'
		WHERE codigo='$cod_espacio'";

		$verificacion = mysqli_query($conexion,$sql);
	}
}
else
	$verificacion = 2;

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>