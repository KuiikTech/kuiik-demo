<?php 
date_default_timezone_set('America/Bogota');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();

$fecha_h=date('Y-m-d G:i:s');

$conexion=$obj_2->conexion();
$conexion_bodega=$obj_2->conexion_bodega();

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$verificacion = 1;

	$cod_bono = $_POST['cod_bono'];
	$input_dias = $_POST['input_dias'];

	if ($input_dias=='' || $input_dias==0)
	{
		if ($input_dias=='')
			$verificacion = 'Ingrese el numero de días';
		if ($input_dias==0)
			$verificacion = 'El numero de días debe ser mayor a 0';
	}
	else
	{
		$sql="SELECT `codigo`, `cliente`, `beneficiario`, `valor`, `informacion`, `fecha_vencimiento`, `estado`, `fecha_registro` FROM `bonos` WHERE codigo = '$cod_bono'";
		$result=mysqli_query($conexion,$sql);
		$ver=mysqli_fetch_row($result);

		$fecha_ven = $ver[5];

		$fecha_ven_nueva = date("Y-m-d H:i:s",strtotime($fecha_ven." + ".$input_dias." days"));

		if ($fecha_ven_nueva>$fecha_h)
		{
			$sql="UPDATE `bonos` SET 
			`fecha_vencimiento`='$fecha_ven_nueva',
			`estado`='VIGENTE'
			WHERE codigo = '$cod_bono'";
		}
		else
		{
			$sql="UPDATE `bonos` SET 
			`fecha_ven`='$fecha_ven_nueva'
			WHERE codigo = '$cod_bono'";
		}

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