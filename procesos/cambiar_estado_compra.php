<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();
$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();

$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');

$estado = '';

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$cod_compra = $_POST['cod_compra'];
	$estado = $_POST['estado'];

	$sql="UPDATE `compras` SET 
	`estado`='$estado'
	WHERE codigo='$cod_compra'";

	$verificacion = mysqli_query($conexion,$sql);

	if($verificacion == 1)
	{
		$info_registro = array(
			'Ubicación' => $_SERVER['HTTP_USER_AGENT']
		);

		$info_registro = array(
			'Tipo' => 'Compra ('.$estado.')',
			'usuario' => $usuario
		);

		$info_registro = json_encode($info_registro,JSON_UNESCAPED_UNICODE);

		$sql="INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
			'$info_registro',
			'$usuario',
			'$fecha_h')";
		$verificacion = mysqli_query($conexion,$sql);
	}
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion,
	'estado' => $estado
);

echo json_encode($datos);

?>