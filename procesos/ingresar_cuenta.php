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
	$caja = $_SESSION['caja_restaurante'];

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
			$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado` FROM `caja` WHERE estado = 'ABIERTA'";
		}
		else if($caja[1] == 2)
		{
			$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado` FROM `caja2` WHERE estado = 'ABIERTA'";
		}
		else if($caja[1] == 3)
		{
			$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado` FROM `caja3` WHERE estado = 'ABIERTA'";
		}
		else
			$verificacion = 'Error: Recarque la pagina y vuelva a intentar';

		if($verificacion == 1)
		{
			$cod_cuenta = $_POST['cod_cuenta'];
			$metodo = $_POST['metodo'];

			$caja = $caja[1];

			$result=mysqli_query($conexion,$sql);
			$mostrar=mysqli_fetch_row($result);

			if($mostrar != NULL)
			{
				$sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$usuario'";
				$result_e=mysqli_query($conexion,$sql_e);
				$ver_e=mysqli_fetch_row($result_e);

				$rol = $ver_e[1];

				if($mostrar[11] == $usuario)
				{
					$cod_caja = $mostrar[0];
					$sql="UPDATE `cuentas_por_cobrar` SET 
					`fecha_ingreso`='$fecha_h',
					`cajero`='$usuario',
					`estado`='INGRESADO'
					WHERE codigo='$cod_cuenta'";

					$verificacion = mysqli_query($conexion,$sql);

					if($verificacion == 1)
					{
						$sql_cuenta = "SELECT `codigo`, `cod_cliente`, `cliente`, `descripcion`, `valor`, `fecha_registro`, `fecha_pago`, `fecha_ingreso`, `creador`, `cobrador`, `cajero`, `estado` FROM `cuentas_por_cobrar` WHERE codigo = '$cod_cuenta'";
						$result_cuenta=mysqli_query($conexion,$sql_cuenta);
						$mostrar_cuenta=mysqli_fetch_row($result_cuenta);

						$descripcion_ingreso = 'Pago de crédito (Cuenta por cobrar N° '.str_pad($cod_cuenta,3,"0",STR_PAD_LEFT).') ['.$mostrar_cuenta[3].']';
						$valor_ingreso = $mostrar_cuenta[4];

						$ingresos = array();
						$pos = 1;
						if($mostrar[9]!= NULL)
							$ingresos = json_decode($mostrar[9],true);
						$pos += count($ingresos);

						$ingresos[$pos]['descripcion'] = $descripcion_ingreso;
						$ingresos[$pos]['valor'] = $valor_ingreso;
						$ingresos[$pos]['metodo'] = $metodo;
						$ingresos[$pos]['fecha'] = $fecha_h;
						$ingresos[$pos]['eliminable'] = 'NO';

						$ingresos = json_encode($ingresos,JSON_UNESCAPED_UNICODE);
						if($caja == 1)
							$sql="UPDATE `caja` SET `ingresos`='$ingresos' WHERE codigo='$cod_caja'";
						else if($caja == 2)
							$sql="UPDATE `caja2` SET `ingresos`='$ingresos' WHERE codigo='$cod_caja'";
						else
							$sql="UPDATE `caja3` SET `ingresos`='$ingresos' WHERE codigo='$cod_caja'";

						$verificacion = mysqli_query($conexion,$sql);
					}
				}
				else
					$verificacion = 'Solo el encargado de caja puede ingresar el pago de la cuenta a caja';
			}
			else
				$verificacion = 'No se puede ingresar porque la caja NO se encuentra abierta';
		}
	}
	else
		$verificacion = 'Solo los cajeros pueden ingresar el pago de la cuenta a caja';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>