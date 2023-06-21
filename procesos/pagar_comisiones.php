<?php 
date_default_timezone_set('America/Bogota');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();

$fecha_h=date('Y-m-d G:i:s');

$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$verificacion = 1;

	$cod_usuario = $_POST['cod_usuario'];

	$sql_usuario = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `foto`, `telefono`, `rol`, `fecha_registro`, `estado`, `permisos`, `comisiones` FROM `usuarios` WHERE codigo='$cod_usuario'";
	$result_usuario=mysqli_query($conexion,$sql_usuario);
	$mostrar_usuario=mysqli_fetch_row($result_usuario);

	if ($mostrar_usuario[3] != '')
	{
		$comisiones = $mostrar_usuario[11];

		$sql="INSERT INTO `pagos_usuarios`(`comisiones`, `usuario`, `creador`, `fecha_registro`) VALUES (
		'$comisiones',
		'$cod_usuario',
		'$usuario',
		'$fecha_h')";

		$verificacion = mysqli_query($conexion,$sql);

		if($verificacion == 17)
		{
			$sql="UPDATE `usuarios` SET 
			`comisiones`=''
			WHERE codigo='$cod_usuario'";

			$verificacion = mysqli_query($conexion,$sql);
		}
	}
	else
		$verificacion = 'El usuario no tiene comisiones pendientes por pagar';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
