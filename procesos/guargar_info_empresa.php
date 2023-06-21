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
		$tipo = $_POST['tipo'];
		$valor = $_POST['valor'];

		$sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Empresa'";
		$result=mysqli_query($conexion,$sql);
		$ver=mysqli_fetch_row($result);

		$empresa = json_decode($ver[2],true);

		$empresa[$tipo] = $valor;

		$empresa = json_encode($empresa,JSON_UNESCAPED_UNICODE);

		$sql="UPDATE `configuraciones` SET 
		`valor`='$empresa'
		WHERE descripcion='Empresa'";

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