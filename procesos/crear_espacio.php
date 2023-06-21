<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();

$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();
$cod_espacio = 0;

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
	$result_e=mysqli_query($conexion,$sql_e);
	$ver_e=mysqli_fetch_row($result_e);

	$rol = $ver_e[5];

	if($rol == 'Cajero 1' || $rol == 'Cajero 2' || $rol == 'Cajero 3')
	{
		$caja = explode(' ', $rol);
		$verificacion = 1;
		if($caja[1] == 1)
		{
			$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `kilos_inicio` FROM `caja` WHERE estado = 'ABIERTA'";
		}
		else if($caja[1] == 2)
		{
			$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `kilos_inicio` FROM `caja2` WHERE estado = 'ABIERTA'";
		}
		else if($caja[1] == 3)
		{
			$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `kilos_inicio` FROM `caja3` WHERE estado = 'ABIERTA'";
		}
		else
			$verificacion = 'Error: Recarque la pagina y vuelva a intentar';

		if($verificacion == 1)
		{
			$result=mysqli_query($conexion,$sql);
			$mostrar=mysqli_fetch_row($result);

			if($mostrar != NULL)
			{
				$cod_caja = $mostrar[0];
				if($mostrar[13] == NULL)
					$count = 1;
				else
					$count = 1 + $mostrar[13];

				$datos=array(
					uniqid(),
					$count,
					$caja[1]
				);

				$verificacion = $obj->agregar_espacio($datos);

				if($verificacion == 1)
				{
					$sql="SELECT MAX(codigo)
					as codigo  from espacios";
					$result=mysqli_query($conexion,$sql);
					$ver=mysqli_fetch_row($result);
					$cod_espacio = $ver[0];
					if($caja[1] == 1)
					{
						$sql="UPDATE `caja` SET 
						`kilos_inicio`='$count'
						WHERE codigo='$cod_caja'";
					}
					else if($caja[1] == 2)
					{
						$sql="UPDATE `caja2` SET 
						`kilos_inicio`='$count'
						WHERE codigo='$cod_caja'";
					}
					else if($caja[1] == 3)
					{
						$sql="UPDATE `caja3` SET 
						`kilos_inicio`='$count'
						WHERE codigo='$cod_caja'";
					}

					$verificacion = mysqli_query($conexion,$sql);
				}
			}
			else
				$verificacion = 'No existe una caja ABIERTA';
		}
	}
	else
		$verificacion = 'Solo los cajeros pueden crear ventas';

}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion,
	'cod_espacio' => $cod_espacio
);
echo json_encode($datos);

?>