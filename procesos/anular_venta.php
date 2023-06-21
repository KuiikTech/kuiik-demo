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
	$acceso = $obj_permisos->buscar_permiso($usuario,'Ventas','ANULAR');

	if($acceso == 'SI')
	{
		$verificacion = 1;
		$cod_venta = $_POST['cod_venta'];

		if (isset($_SESSION['usuario_restaurante2']))
			$bodega = 'PDV_2';
		else
			$bodega = 'PDV_1';

		$sql = "SELECT `codigo`, `cliente`, `productos`, `pago`, `fecha`, `cobrador`, `estado` FROM `ventas` WHERE codigo = '$cod_venta'";
		$result=mysqli_query($conexion,$sql);
		$mostrar=mysqli_fetch_row($result);

		$productos_venta = json_decode($mostrar[2],true);
		foreach ($productos_venta as $i => $producto)
		{
			$cod_producto = $producto['codigo'];
			$num_inv = $producto['num_inv'];
			$cant = $producto['cant'];
			$sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo = '$cod_producto'";
			$result_producto=mysqli_query($conexion,$sql_producto);
			$mostrar_producto=mysqli_fetch_row($result_producto);

			$inventario = array();

			if (isset($_SESSION['usuario_restaurante2']))
				$bodega = 'PDV_2';
			else
				$bodega = 'PDV_1';

			$pos = 1;
			if($bodega == 'PDV_1')
			{
				$bodega_inventario = 'inventario_1';
				if ($mostrar_producto[6] != '')
				{
					$inventario = json_decode($mostrar_producto[6],true);
					$pos += count($inventario[$num_inv]['movimientos']);
				}
			}
			else if($bodega == 'PDV_2')
			{
				$bodega_inventario = 'inventario_2';
				if ($mostrar_producto[7] != '')
				{
					$inventario = json_decode($mostrar_producto[7],true);
					$pos += count($inventario[$num_inv]['movimientos']);
				}
			}

			if(isset($inventario[$num_inv]))
			{
				$inventario[$num_inv]['movimientos'][$pos] = array(
					'Tipo' =>'Retorno por Anulacion de Venta (# '.$cod_venta.')',
					'Cant' => '-'.$producto['cant'],
					'creador' => $usuario,
					'Observaciones' => '',
					'fecha' => $fecha_h );

				$inventario[$num_inv]['stock'] += $cant;
			}
			else
				$verificacion = 'No se encontró el inventario seleccionado.('.$mostrar_producto[1].')';
			

			if($verificacion == 1)
			{
				$inventario = json_encode($inventario,JSON_UNESCAPED_UNICODE);
				$sql="UPDATE `productos` SET 
				`$bodega_inventario`='$inventario'
				WHERE codigo='$cod_producto'";
				$verificacion_2 = mysqli_query($conexion,$sql);
				if($verificacion_2 != 1)
					$verificacion .= $verificacion_2;
			}
		}

		if($verificacion == 1)
		{
			$sql="UPDATE `ventas` SET 
			`estado`='ANULADA'
			WHERE codigo='$cod_venta'";

			$verificacion = mysqli_query($conexion,$sql);
		}
	}
	else
		$verificacion = 'Usted no tiene permisos para anular ventas';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>