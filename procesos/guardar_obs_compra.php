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

	$observaciones = $_POST['observaciones'];

	if($observaciones != '')
	{
		$sql = "SELECT `codigo`, `productos`, `proveedor`, `creador`, `estado`, `fecha_registro` FROM `compras` WHERE estado = 'EN PROCESO' order by fecha_registro DESC";
		$result=mysqli_query($conexion,$sql);
		$mostrar=mysqli_fetch_row($result);

		if($mostrar != NULL)
		{
			$cod_compra = $mostrar[0];

			$sql="UPDATE `compras` SET 
			`observaciones`='$observaciones'
			WHERE codigo='$cod_compra'";

			$verificacion = mysqli_query($conexion,$sql);
		}
		else
			$verificacion = 'No se encontró una compra en proceso';
	}
	else
		$verificacion = 'Ingrese las observaciones';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
