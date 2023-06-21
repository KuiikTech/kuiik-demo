<?php 
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();

$obj= new conectar();
$conexion=$obj->conexion();

$fecha_h = date('Y-m-d G:i:s');

$usuario = $_SESSION['usuario_restaurante'];

$sql_mesa = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura` FROM `mesas` WHERE estado = 'OCUPADA'";
$result_mesa=mysqli_query($conexion,$sql_mesa);
$mostrar_mesa=mysqli_fetch_row($result_mesa);

if($mostrar_mesa == NULL)
{
	// Configuracion descontar
	$sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Descontar de inventario'";
	$result=mysqli_query($conexion,$sql);
	$ver=mysqli_fetch_row($result);

	if($ver[2] == 'Si')
		$descontar = 'No';
	else
		$descontar = 'Si';

	$sql="UPDATE `configuraciones` SET 
	`valor`='$descontar'
	WHERE descripcion='Descontar de inventario'";

	$verificacion = mysqli_query($conexion,$sql);
}
else
	$verificacion = 'Existen mesas ocupadas.<br>Libere las mesas para continuar';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);
?>