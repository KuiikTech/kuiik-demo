<?php 
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();

$obj= new conectar();
$conexion=$obj->conexion();

$fecha_h = date('Y-m-d G:i:s');

if(isset($_SESSION['usuario_rancho']))
{
	$usuario = $_SESSION['usuario_rancho'];

	require_once "../clases/permisos.php";
	$obj_permisos = new permisos();
	$acceso = $obj_permisos->buscar_permiso($usuario,'Config PDV','GENERAL');

	if($acceso == 'SI')
	{
		$facturacion_creditos = $_POST['facturacion_creditos'];

		$sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Facturación Créditos'";
		$result=mysqli_query($conexion,$sql);
		$ver=mysqli_fetch_row($result);

		$sql="UPDATE `configuraciones` SET 
		`valor`='$facturacion_creditos'
		WHERE descripcion='Facturación Créditos'";

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