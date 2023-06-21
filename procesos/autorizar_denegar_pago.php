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

	$num_item = $_POST['num_item'];
	$estado = $_POST['estado'];

	if($num_item == 'V')
	{
		$cod_venta = $_POST['cod_trabajo'];

		$sql = "SELECT `codigo`, `descripcion`, `valor`, `estado`, `creador`, `metodo`, `fecha_registro` FROM `ventas_directas` WHERE codigo = '$cod_venta'";
		$result=mysqli_query($conexion,$sql);
		$mostrar=mysqli_fetch_row($result);

		if ($mostrar != NULL)
		{
			$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `ingresos`, `egresos`, `base`, `creador`, `cajero`, `finalizador`, `estado`, `base_sig` FROM `caja` WHERE estado = 'ABIERTA'";
			$result=mysqli_query($conexion,$sql);
			$mostrar_caja=mysqli_fetch_row($result);

			if($mostrar_caja != NULL)
			{
				if($estado == 'AUTORIZADO')
				{
					$cod_caja = $mostrar_caja[0];
					$ingresos = array();
					$pos = 1;
					if ($mostrar_caja[4] != '')
					{
						$ingresos = json_decode($mostrar_caja[4],true);
						$pos += count($ingresos);
					}

					$info = 'Pago venta directa #';

					$ingresos[$pos]["concepto"] = $info.str_pad($cod_venta,5,"0",STR_PAD_LEFT);
					$ingresos[$pos]["valor"] = $mostrar[2];
					$ingresos[$pos]["metodo"] = $mostrar[5];
					$ingresos[$pos]["fecha"] = $mostrar[6];
					$ingresos[$pos]["creador"] = $mostrar[4];
					$ingresos[$pos]["aprueba"] = $usuario;
					$ingresos[$pos]["fecha_aprobacion"] = $fecha_h;

					$ingresos = json_encode($ingresos,JSON_UNESCAPED_UNICODE);

					$sql="UPDATE `caja` SET 
					`ingresos`='$ingresos'
					WHERE codigo='$cod_caja'";

					$verificacion = mysqli_query($conexion,$sql);
				}

				if($verificacion == 1)
				{
					$sql="UPDATE `ventas_directas` SET 
					`estado`='$estado'
					WHERE codigo='$cod_venta'";

					$verificacion = mysqli_query($conexion,$sql);
				}
			}
			else
				$verificacion = 'No se autorizó el pago. La caja NO se encuentra abierta';
		}
		else
			$verificacion = 'No se encontró la venta seleccionada';
	}
	else
	{
		$cod_trabajo = $_POST['cod_trabajo'];

		$sql = "SELECT `codigo`, `info`, `items`, `pagos`, `cliente`, `responsable`, `fecha_entrega`, `fecha_registro`, `estado`, `movimientos` FROM `trabajos` WHERE codigo = '$cod_trabajo'";
		$result=mysqli_query($conexion,$sql);
		$mostrar=mysqli_fetch_row($result);

		if ($mostrar[3] != '')
		{
			$pagos = json_decode($mostrar[3],true);
			$pagos[$num_item]['estado'] = $estado;

			$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `ingresos`, `egresos`, `base`, `creador`, `cajero`, `finalizador`, `estado`, `base_sig` FROM `caja` WHERE estado = 'ABIERTA'";
			$result=mysqli_query($conexion,$sql);
			$mostrar_caja=mysqli_fetch_row($result);

			if($mostrar_caja != NULL)
			{
				if($estado == 'AUTORIZADO')
				{
					$cod_caja = $mostrar_caja[0];

					if ($pagos[$num_item]['metodo'] == 'devolucion')
					{
						$egresos = array();
						$pos = 1;
						if ($mostrar_caja[5] != '')
						{
							$egresos = json_decode($mostrar_caja[5],true);
							$pos += count($egresos);
						}
						$info = 'Devolución total o parcial de orden #';

						$egresos[$pos]["concepto"] = $info.str_pad($cod_trabajo,5,"0",STR_PAD_LEFT);
						$egresos[$pos]["valor"] = $pagos[$num_item]['valor']*(-1);
						$egresos[$pos]["fecha"] = $fecha_h;
						$egresos[$pos]["creador"] = $usuario;

						$egresos = json_encode($egresos,JSON_UNESCAPED_UNICODE);

						$sql="UPDATE `caja` SET 
						`egresos`='$egresos'
						WHERE codigo='$cod_caja'";

						$verificacion = mysqli_query($conexion,$sql);
					}
					else
					{
						$ingresos = array();
						$pos = 1;
						if ($mostrar_caja[4] != '')
						{
							$ingresos = json_decode($mostrar_caja[4],true);
							$pos += count($ingresos);
						}

						$info = 'Pago total o parcial de orden #';

						$ingresos[$pos]["concepto"] = $info.str_pad($cod_trabajo,5,"0",STR_PAD_LEFT);
						$ingresos[$pos]["valor"] = $pagos[$num_item]['valor'];
						$ingresos[$pos]["metodo"] = $pagos[$num_item]['metodo'];
						$ingresos[$pos]["fecha"] = $pagos[$num_item]['fecha'];
						$ingresos[$pos]["creador"] = $pagos[$num_item]['creador'];
						$ingresos[$pos]["aprueba"] = $usuario;
						$ingresos[$pos]["fecha_aprobacion"] = $fecha_h;

						$ingresos = json_encode($ingresos,JSON_UNESCAPED_UNICODE);

						$sql="UPDATE `caja` SET 
						`ingresos`='$ingresos'
						WHERE codigo='$cod_caja'";

						$verificacion = mysqli_query($conexion,$sql);
					}
				}

				if($verificacion == 1)
				{
					$pagos = json_encode($pagos,JSON_UNESCAPED_UNICODE);
					$sql="UPDATE `trabajos` SET 
					`pagos`='$pagos'
					WHERE codigo='$cod_trabajo'";

					$verificacion = mysqli_query($conexion,$sql);
				}
			}
			else
				$verificacion = 'No se autorizó el pago. La caja NO se encuentra abierta';
		}
		else
			$verificacion = 'No se encontró el pago seleccionado';
	}
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
