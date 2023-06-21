<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();
$conexion=$obj_2->conexion();
$conexion_bodega=$obj_2->conexion_bodega();

$usuario = $_SESSION['usuario_restaurante'];

$verificacion = 'Ha ocurrido un error actualice y vuelva a intentarlo.';
$comensal = '';

if(isset($_POST['cod_cliente']))
{
	$cod_cliente = $_POST['cod_cliente'];

	$sql_cliente = "SELECT `cod_cliente`, `cedula`, `nombre`, `apellido`, `telefono`, `puntos_actuales`, `puntos_totales`, `fecha_registro`, `comensal` FROM `clientes` WHERE cod_cliente='$cod_cliente'";
	$result_cliente=mysqli_query($conexion,$sql_cliente);
	$mostrar_cliente=mysqli_fetch_row($result_cliente);

	$comensal = $mostrar_cliente[8];

	if($comensal == 'SI')
		$comensal = 'NO';
	else
		$comensal = 'SI';

	$sql="UPDATE `clientes` SET 
	`comensal`='$comensal'
	WHERE cod_cliente='$cod_cliente'";

	$verificacion = mysqli_query($conexion,$sql);
}

$datos=array(
	'consulta' => $verificacion,
	'comensal' => $comensal
);

echo json_encode($datos);

?>