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

$usuario = $_SESSION['usuario_restaurante'];

$verificacion = 'Consulta no detectada';

if (isset($_POST['cod_empleado']))
	$verificacion = $obj->bloquear_empleado($_POST['cod_empleado']);

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);

?>