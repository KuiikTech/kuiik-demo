<?php 
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();

$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();

$fecha_h = date('Y-m-d G:i:s');

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$verificacion = 1;

	$cod_servicio = $_POST['cod_servicio'];
	$item = $_POST['num_item'];

	$sql_servicio = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro`, `local` FROM `servicios` WHERE codigo = '$cod_servicio'";
	$result_servicio=mysqli_query($conexion,$sql_servicio);
	$mostrar_servicio=mysqli_fetch_row($result_servicio);

	$result_2=mysqli_query($conexion,$sql_servicio);
	$info_respaldo = $result_2->fetch_object();

	if($mostrar_servicio[5] != '')
	{
		$repuestos = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar_servicio[5]);
		$repuestos = str_replace('	', ' ', $repuestos);
		$repuestos = json_decode($repuestos,true);

		$repuestos_nuevos = array();
		$pos_1 = 1;

		foreach ($repuestos as $i => $repuesto)
		{
			if($i == $item)
			{
				$cod_producto = $repuesto['codigo'];
				$num_inventario = $repuesto['num_inv'];
				$cod_servicio = $_POST['cod_servicio'];
				$cant = $repuesto['cant'];

				$bodega = '';

				$sql_repuestos = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion='Bodega Repuestos'";
				$result_repuestos=mysqli_query($conexion,$sql_repuestos);
				$mostrar_repuestos=mysqli_fetch_row($result_repuestos);

				if($mostrar_repuestos != null)
					$bodega = $mostrar_repuestos[2];

				$sql_servicio = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro` FROM `servicios` WHERE codigo = '$cod_servicio'";
				$result_servicio=mysqli_query($conexion,$sql_servicio);
				$mostrar_servicio=mysqli_fetch_row($result_servicio);

				$sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo='$cod_producto'";
				$result_producto=mysqli_query($conexion,$sql_producto);
				$mostrar_producto=mysqli_fetch_row($result_producto);

				$nombre_producto = $mostrar_producto[1];
				$cod_categoria = $mostrar_producto[2];

				$inventario = array();
				if($bodega == 'PDV_1')
				{
					$bodega_inventario = 'inventario_1';
					if ($mostrar_producto[6] != '')
						$inventario = json_decode($mostrar_producto[6],true);
				}
				else if($bodega == 'PDV_2')
				{
					$bodega_inventario = 'inventario_2';
					if ($mostrar_producto[7] != '')
						$inventario = json_decode($mostrar_producto[7],true);
				}

				if(!isset($inventario[$num_inventario]))
					$verificacion = 'No se encontró el inventario seleccionado';

				$pos = 1;
				if($verificacion == 1)
				{
					if(isset($inventario[$num_inventario]))
					{
						$inventario[$num_inventario]['stock'] += $cant;

						if(isset($inventario[$num_inventario]['movimientos']))
							$pos += count($inventario[$num_inventario]['movimientos']);

						$inventario[$num_inventario]['movimientos'][$pos] = array(
							'Tipo' =>'Retorno por eliminación en servicio # '.$cod_servicio,
							'Cant' => '+'.$cant,
							'creador' => $usuario,
							'Observaciones' => '',
							'fecha' => $fecha_h );
					}
				}

				if($verificacion == 1)
				{
					$inventario = json_encode($inventario,JSON_UNESCAPED_UNICODE);
					$sql="UPDATE `productos` SET 
					`$bodega_inventario`='$inventario'
					WHERE codigo='$cod_producto'";
					$verificacion = mysqli_query($conexion,$sql);
				}
			}
			else
			{
				$repuestos_nuevos[$pos_1] = $repuesto;
				$pos_1 ++;
			}
		}
		if($verificacion == 1)
		{
			$repuestos_nuevos = json_encode($repuestos_nuevos,JSON_UNESCAPED_UNICODE);
			$sql="UPDATE `servicios` SET 
			`repuestos`='$repuestos_nuevos'
			WHERE codigo='$cod_servicio'";

			$verificacion = mysqli_query($conexion,$sql);
		}
		if ($verificacion == 1)
		{
			$operacion = 'Baja de repuesto -> '.$mostrar_producto[1].' (Cod: '.$mostrar_producto[0].')';

			$info_respaldo = json_encode($info_respaldo,JSON_UNESCAPED_UNICODE);
			$sql="INSERT INTO `respaldo_info`(`cod_servicio`, `informacion`, `operacion`, `usuario`, `fecha_registro`) VALUES (
				'$cod_servicio',
				'$info_respaldo',
				'$operacion',
				'$usuario',
				'$fecha_h')";

			mysqli_query($conexion,$sql);
		}
	}
	else
		$verificacion = 'No existen repuestos colocados';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>