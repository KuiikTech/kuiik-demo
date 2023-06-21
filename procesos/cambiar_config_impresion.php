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

	require_once "../clases/permisos.php";
	$obj_permisos = new permisos();
	$acceso = $obj_permisos->buscar_permiso($usuario,'Config PDV','GENERAL');

	if($acceso == 'SI')
	{
		$impresion_tickets = $_POST['impresion_tickets'];

		$sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Imprimir Facturas'";
		$result=mysqli_query($conexion,$sql);
		$ver=mysqli_fetch_row($result);

		$sql="UPDATE `configuraciones` SET 
		`valor`='$impresion_tickets'
		WHERE descripcion='Imprimir Facturas'";

		$verificacion = mysqli_query($conexion,$sql);
	}
	else
		$verificacion = 'Usted no tiene permisos para cambiar esta configuración';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);
?>