<?php 
date_default_timezone_set('America/Bogota');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();

$conexion=$obj_2->conexion();
$conexion_bodega=$obj_2->conexion_bodega();

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];
	$verificacion = 1;

	$cod_usuario = $_POST['cod_usuario'];
	$tipo = $_POST['tipo'];

	$sql="SELECT `codigo`, `posicion` FROM `usuarios` WHERE codigo='$cod_usuario'";
	$result=mysqli_query($conexion,$sql);
	$ver=mysqli_fetch_row($result);

	if($ver != null)
	{
		if ($tipo == 'Subir')
		{
			$pos_1 = $ver[1];
			if($pos_1 > 1)
			{
				$pos_2 = $pos_1-1;
				$sql_2="SELECT `codigo`, `posicion` FROM `usuarios` WHERE posicion='$pos_2'";
				$result_2=mysqli_query($conexion,$sql_2);
				$ver_2=mysqli_fetch_row($result_2);

				if($ver_2 != null)
				{
					$cod_usuario_2 = $ver_2[0];

					$sql="UPDATE usuarios set 
					posicion='$pos_2'
					where codigo='$cod_usuario'";
					$verificacion = mysqli_query($conexion,$sql);

					if($verificacion == 1)
					{
						$sql="UPDATE usuarios set 
						posicion='$pos_1'
						where codigo='$cod_usuario_2'";
						$verificacion = mysqli_query($conexion,$sql);
					}
				}
				else
					$verificacion = 'No se encontró el usuario anterior al seleccionado';
			}
			else
				$verificacion = 'El usuario ya se encuentra en la primera posición';
		}
		else
		{
			$sql_mayor="SELECT MAX(`posicion`) FROM `usuarios`";
			$result_mayor=mysqli_query($conexion,$sql_mayor);
			$ver_mayor=mysqli_fetch_row($result_mayor);

			$pos_1 = $ver[1];
			$pos_mayor = $ver_mayor[0];
			if($pos_1 < $pos_mayor)
			{
				$pos_2 = $pos_1+1;
				$sql_2="SELECT `codigo`, `posicion` FROM `usuarios` WHERE posicion='$pos_2'";
				$result_2=mysqli_query($conexion,$sql_2);
				$ver_2=mysqli_fetch_row($result_2);

				if($ver_2 != null)
				{
					$cod_usuario_2 = $ver_2[0];

					$sql="UPDATE usuarios set 
					posicion='$pos_2'
					where codigo='$cod_usuario'";
					$verificacion = mysqli_query($conexion,$sql);

					if($verificacion == 1)
					{
						$sql="UPDATE usuarios set 
						posicion='$pos_1'
						where codigo='$cod_usuario_2'";
						$verificacion = mysqli_query($conexion,$sql);
					}
				}
				else
					$verificacion = 'No se encontró el usuario anterior al seleccionado';
			}
			else
				$verificacion = 'El usuario ya se encuentra en la ultima posición';
		}
	}
	else
		$verificacion = 'No se encontró el usuario seleccionado';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);
?>