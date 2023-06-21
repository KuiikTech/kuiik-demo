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

	$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
	$result_e=mysqli_query($conexion,$sql_e);
	$ver_e=mysqli_fetch_row($result_e);

	$rol = $ver_e[5];

	if($rol == 'Administrador')
	{
		$verificacion = 1;

		$cod_caja = $_POST['cod_caja'];
		$caja=$_POST['caja'];
		$item = $_POST['item'];
		$cod_servicio = $_POST['cod_servicio'];
		$cod_unico = $_POST['cod_unico'];
		if($caja == 1)
		{
			$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `info`, `creador`, `cajero`, `finalizador`, `estado`, `kilos_fin` FROM `caja` WHERE codigo = '$cod_caja'";
		}
		else if($caja == 2)
		{
			$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `info`, `creador`, `cajero`, `finalizador`, `estado`, `kilos_fin` FROM `caja2` WHERE codigo = '$cod_caja'";
		}
		else
		{
			$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `info`, `creador`, `cajero`, `finalizador`, `estado`, `kilos_fin` FROM `caja3` WHERE codigo = '$cod_caja'";
		}
		$result=mysqli_query($conexion,$sql);
		$mostrar_caja=mysqli_fetch_row($result);

		$estado = $mostrar_caja[14];

		if($estado == 'ABIERTA')
		{
			$pagos = array();
			$nuevos_pagos = array();
			$pos = 1;
			if ($mostrar_caja[15] != '')
				$pagos = json_decode($mostrar_caja[15],true);

			foreach ($pagos as $j => $pago)
			{
				if($j != $item)
				{
					$nuevos_pagos[$pos] = $pago;
					$pos ++;
				}
			}

			if($pos >1)
				$nuevos_pagos = json_encode($nuevos_pagos,JSON_UNESCAPED_UNICODE);
			else
				$nuevos_pagos = '';

			if($caja == 1)
			{
				$sql="UPDATE `caja` SET 
				`servicios`='$nuevos_pagos'
				WHERE codigo='$cod_caja'";
			}
			else if($caja == 2)
			{
				$sql="UPDATE `caja2` SET 
				`servicios`='$nuevos_pagos'
				WHERE codigo='$cod_caja'";
			}
			else
			{
				$sql="UPDATE `caja3` SET 
				`servicios`='$nuevos_pagos'
				WHERE codigo='$cod_caja'";
			}

			$verificacion = mysqli_query($conexion,$sql);

			if($verificacion == 1)
			{
				$sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro` FROM `servicios` WHERE `codigo` = '$cod_servicio'";
				$result=mysqli_query($conexion,$sql);
				$mostrar=mysqli_fetch_row($result);

				$pagos_servicios = array();
				$nuevos_pagos = array();
				$pos = 1;
				if ($mostrar[3] != '')
					$pagos_servicios = json_decode($mostrar[3],true);

				foreach ($pagos_servicios as $j => $pago)
				{
					if(isset($pago['cod_unico']))
					{
						if($pago['cod_unico'] != $cod_unico)
						{
							$nuevos_pagos[$pos] = $pago;
							$pos ++;
						}
					}
					else
					{
						$nuevos_pagos[$pos] = $pago;
						$pos ++;
					}
				}

				if($pos >1)
					$nuevos_pagos = json_encode($nuevos_pagos,JSON_UNESCAPED_UNICODE);
				else
					$nuevos_pagos = '';

				$sql="UPDATE `servicios` SET `pagos`='$nuevos_pagos' WHERE codigo='$cod_servicio'";

				$verificacion = mysqli_query($conexion,$sql);
			}
		}
		else
			$verificacion = 'No se eliminó el pago. La caja NO se encuentra abierta';
	}
	else
		$verificacion = 'Solo los administradores pueden borrar pago de servicios';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
