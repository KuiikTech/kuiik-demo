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

	if($mostrar_servicio[6] != '')
	{
		$accesorios = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar_servicio[6]);
		$accesorios = str_replace('	', ' ', $accesorios);
		$accesorios = json_decode($accesorios,true);

		$accesorios_nuevos = array();
		$pos_1 = 1;

		foreach ($accesorios as $i => $accesorio)
		{
			if($i == $item)
			{
				$cod_producto = $accesorio['codigo'];
				$num_inventario = $accesorio['num_inv'];
				$cod_servicio = $_POST['cod_servicio'];
				$cant = $accesorio['cant'];

				if (isset($_SESSION['usuario_restaurante2']))
					$bodega = 'PDV_2';
				else
					$bodega = 'PDV_1';

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
				$accesorios_nuevos[$pos_1] = $accesorio;
				$pos_1 ++;
			}
		}
		if($verificacion == 1)
		{
			$accesorios_nuevos = json_encode($accesorios_nuevos,JSON_UNESCAPED_UNICODE);
			$sql="UPDATE `servicios` SET 
			`accesorios`='$accesorios_nuevos'
			WHERE codigo='$cod_servicio'";

			$verificacion = mysqli_query($conexion,$sql);
		}
	}
	else
		$verificacion = 'No existen accesorios colocados';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>