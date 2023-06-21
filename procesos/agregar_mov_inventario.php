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

	$cod_producto = $_POST['cod_producto'];
	$num_inventario = $_POST['pos'];
	$tipo_mov = $_POST['tipo_mov'];
	$cant_mov = $_POST['cant_mov'];
	$cant_mov_2 = $_POST['cant_mov'];
	$obs_mov = $_POST['obs_mov'];
	$bodega_traslado = $_POST['bodega_traslado'];

	$bodega = $_POST['bodega'];

	$sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo='$cod_producto'";
	$result_producto=mysqli_query($conexion,$sql_producto);
	$mostrar_producto=mysqli_fetch_row($result_producto);

	$nombre_producto = $mostrar_producto[1];
	$cod_categoria = $mostrar_producto[2];

	$inventario = array();
	$pos = 1;

	if($bodega == 'Principal')
	{
		$bodega_inventario = 'inventario';
		if ($mostrar_producto[3] != '')
			$inventario = json_decode($mostrar_producto[3],true);
	}
	else if($bodega == 'PDV_1')
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
	else
		$verificacion = 'No se encontróla bodega seleccionada';

	if(isset($inventario[$num_inventario]))
	{
		$stock = $inventario[$num_inventario]['stock'];
		$pos += count($inventario[$num_inventario]['movimientos']);

		if($tipo_mov == 'Salida')
		{
			if($cant_mov>$stock)
				$verificacion = 'No se puede procesar la salida, el stock actual del lote es: '.$stock;
			else
			{
				$inventario[$num_inventario]['stock'] -=$cant_mov;
				$cant_mov = '-'.$cant_mov;
			}
		}
		else if($tipo_mov == 'Baja')
		{
			if($cant_mov>$stock)
				$verificacion = 'No se puede procesar la baja, el stock actual del lote es: '.$stock;
			else
			{
				$inventario[$num_inventario]['stock'] -=$cant_mov;
				$cant_mov = '-'.$cant_mov;
			}
		}
		else if($tipo_mov == 'Garantía')
		{
			if($cant_mov>$stock)
				$verificacion = 'No se puede procesar la garantía, el stock actual del lote es: '.$stock;
			else
			{
				$inventario[$num_inventario]['stock'] -=$cant_mov;
				$cant_mov = '-'.$cant_mov;
			}
		}
		else if($tipo_mov == 'Retorno')
		{
			$inventario[$num_inventario]['stock'] +=$cant_mov;
			$cant_mov = '+'.$cant_mov;
		}
		else
		{
			if($cant_mov>$stock)
				$verificacion = 'No se puede procesar la salida, el stock actual del lote es: '.$stock;
			else
			{
				$inventario[$num_inventario]['stock'] -=$cant_mov;
				$obs_mov = 'Traslado a Bodega '.str_replace('_', ' ', $bodega_traslado);

				$inventario_traslado = array();
				if($bodega_traslado == 'Principal')
				{
					$bodega_inventario_traslado = 'inventario';
					if ($mostrar_producto[3] != '')
						$inventario_traslado = json_decode($mostrar_producto[3],true);
				}
				else if($bodega_traslado == 'PDV_1')
				{
					$bodega_inventario_traslado = 'inventario_1';
					if ($mostrar_producto[6] != '')
						$inventario_traslado = json_decode($mostrar_producto[6],true);
				}
				else if($bodega_traslado == 'PDV_2')
				{
					$bodega_inventario_traslado = 'inventario_2';
					if ($mostrar_producto[7] != '')
						$inventario_traslado = json_decode($mostrar_producto[7],true);
				}
				else
					$verificacion = 'No se encontró la bodega de traslado seleccionada';

				if(isset($inventario_traslado[$num_inventario]))
				{
					$inventario_traslado[$num_inventario]['stock'] +=$cant_mov;
					$pos_traslado = count($inventario_traslado[$num_inventario]['movimientos']);
				}
				else
				{
					$inventario_traslado[$num_inventario] = $inventario[$num_inventario];
					$inventario_traslado[$num_inventario]['stock'] =$cant_mov;
					$inventario_traslado[$num_inventario]['inicial'] =$cant_mov;
					$inventario_traslado[$num_inventario]['movimientos'] = array();
					$pos_traslado = 1;
				}

				$inventario_traslado[$num_inventario]['movimientos'][$pos_traslado] = array(
						'Tipo' => 'Ingreso por traslado de Bodega '.str_replace('_', ' ', $bodega),
						'Cant' => '+'.$cant_mov_2,
						'creador' => $usuario,
						'Observaciones' => '',
						'fecha' => $fecha_h );

				$cant_mov = '-'.$cant_mov;
			}
		}

		$inventario[$num_inventario]['movimientos'][$pos] = array(
			'Tipo' => $tipo_mov,
			'Cant' => $cant_mov,
			'creador' => $usuario,
			'Observaciones' => $obs_mov,
			'fecha' => $fecha_h );
	}
	else
		$verificacion = 'No se encontró el inventario seleccionado';

	if($verificacion == 1)
	{
		$inventario = json_encode($inventario,JSON_UNESCAPED_UNICODE);
		$sql="UPDATE `productos` SET 
		`$bodega_inventario`='$inventario'
		WHERE codigo='$cod_producto'";

		$verificacion = mysqli_query($conexion,$sql);
	}

	if($verificacion == 1 && $tipo_mov == 'Traslado')
	{
		$inventario_traslado = json_encode($inventario_traslado,JSON_UNESCAPED_UNICODE);
		$sql="UPDATE `productos` SET 
		`$bodega_inventario_traslado`='$inventario_traslado'
		WHERE codigo='$cod_producto'";

		$verificacion = mysqli_query($conexion,$sql);
	}
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
