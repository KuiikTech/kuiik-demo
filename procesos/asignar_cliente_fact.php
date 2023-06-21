<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();
$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();
$fecha_h = date('Y-m-d G:i:s');

$usuario = $_SESSION['usuario_restaurante'];
$verificacion = 1;

$cod_cliente = $_POST['cod_cliente'];
if($cod_cliente != '')
{
	$_SESSION['cod_cliente_fact'] = $cod_cliente;

	$sql_cliente = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro`, `tipo`, `info` FROM `clientes` WHERE codigo = '$cod_cliente'";
	$result_cliente=mysqli_query($conexion,$sql_cliente);
	$ver_cliente=mysqli_fetch_row($result_cliente);

	if (!isset($_SESSION['items_factura']))
		$btn_generar = true;
	else
		$btn_generar = false;

	$datos=array(
		'consulta' => $verificacion,
		'btn_generar' => $btn_generar,
		'cedula' => $ver_cliente[1],
		'nombre' => $ver_cliente[2]. ' '.$ver_cliente[3],
		'telefono' => $ver_cliente[4]
	);
}
else
{
	unset($_SESSION['cod_cliente_fact']);
	$datos=array(
		'consulta' => $verificacion,
		'btn_generar' => true,
		'cedula' => '---',
		'nombre' => '---',
		'telefono' => '---'
	);
}
echo json_encode($datos);

?>