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

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	require_once "../clases/permisos.php";
	$obj_permisos = new permisos();
	$acceso = $obj_permisos->buscar_permiso($usuario,'Por Cobrar','COBRAR');

	if($acceso == 'SI')
	{
		$cod_cuenta = $_POST['cod_cuenta'];

		$sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$usuario'";
		$result_e=mysqli_query($conexion,$sql_e);
		$ver_e=mysqli_fetch_row($result_e);

		$rol = $ver_e[1];

		$sql="UPDATE `cuentas_por_cobrar` SET 
		`fecha_pago`='$fecha_h',
		`cobrador`='$usuario',
		`estado`='COBRADO'
		WHERE codigo='$cod_cuenta'";

		$verificacion = mysqli_query($conexion,$sql);
	}
	else
		$verificacion = 'Usted no tiene permisos para cobrar cuentas';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>