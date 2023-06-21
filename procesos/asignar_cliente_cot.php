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

	$cod_cliente = $_POST['cod_cliente'];

	if($cod_cliente != '')
	{
		$sql_c = "SELECT `codigo` FROM `clientes` WHERE codigo='$cod_cliente'";
		$result_c=mysqli_query($conexion,$sql_c);
		$ver_c=mysqli_fetch_row($result_c);

		$_SESSION['cliente_cot'] = $ver_c[0];
	}
	else
		$verificacion = 'Por favor seleccione un cliente válido';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
