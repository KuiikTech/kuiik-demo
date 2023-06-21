<?php 
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();

$obj_2= new conectar();
$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();

$fecha_h = date('Y-m-d G:i:s');

$usuario = $_SESSION['usuario_restaurante'];

$cod_espacio = $_POST['cod_espacio'];

$sql="DELETE from `espacios` 
WHERE codigo='$cod_espacio'";

$verificacion = mysqli_query($conexion,$sql);

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);
?>