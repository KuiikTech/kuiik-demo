<?php 
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();

$obj= new conectar();
$conexion=$obj->conexion();

$fecha_h = date('Y-m-d G:i:s');

$usuario = $_SESSION['usuario_restaurante'];

	// Configuracion descontar
$sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Imprimir Facturas'";
$result=mysqli_query($conexion,$sql);
$ver=mysqli_fetch_row($result);

if($ver[2] == 'Si')
	$descontar = 'No';
else
	$descontar = 'Si';

$sql="UPDATE `configuraciones` SET 
`valor`='$descontar'
WHERE descripcion='Imprimir Facturas'";

$verificacion = mysqli_query($conexion,$sql);

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);
?>