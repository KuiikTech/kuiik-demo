<?php 
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();

$obj= new conectar();
$conexion=$obj->conexion();

$fecha_h = date('Y-m-d G:i:s');

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$tipo = $_POST['tipo'];
    $valor = $_POST['valor'];

	$_SESSION['view'.$tipo] = $valor;

	$verificacion = 1;
}
else
	$verificacion = 'Reload';
$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>