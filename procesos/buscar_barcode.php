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

$usuario = $_SESSION['usuario_restaurante'];

$verificacion = 1;
$produc_code= '';
$inventario_nuevo = '';

if($_POST['busqueda_barcode'] != '')
{
	$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado` FROM `caja` WHERE estado = 'ABIERTA'";
	$result=mysqli_query($conexion,$sql);
	$mostrar=mysqli_fetch_row($result);

	if($mostrar != NULL)
	{
		$busqueda_barcode = $_POST['busqueda_barcode'];
		$cantidad_barcode = $_POST['cantidad_barcode'];

		$sql_producto = "SELECT `cod_producto`, `descripción`, `tipo`, `valor`, `inventario`, `cod_categoria`, `fecha_modificacion`, `barcode` FROM `productos` WHERE barcode='$busqueda_barcode'";
		$result_producto=mysqli_query($conexion,$sql_producto);
		$mostrar_producto=mysqli_fetch_row($result_producto);

		if($mostrar_producto != null)
		{
			$cod_mesa = 1;
			$cod_producto = $mostrar_producto[0];
			$produc_code = 'p_'.$cod_producto;

			$sql_mesa = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura` FROM `mesas` WHERE cod_mesa = '$cod_mesa'";
			$result_mesa=mysqli_query($conexion,$sql_mesa);
			$mostrar_mesa=mysqli_fetch_row($result_mesa);

			$inventario = $mostrar_producto[4];

			$i = 0;

			if ($mostrar_mesa[2] != '')
			{
				$productos_mesa = json_decode($mostrar_mesa[2],true);
				foreach ($productos_mesa as $i => $producto)
				{
					if ($producto['codigo'] == $cod_producto)
					{
						$encontrado = 1;
						$pos = $i;
						if($productos_mesa[$i]['cant'] > ($cantidad_barcode*(-1)))
							$productos_mesa[$i]['cant'] += $cantidad_barcode;
						else
						{
							if($productos_mesa[$i]['cant'] == ($cantidad_barcode*(-1)))
								unset($productos_mesa[$i]);
							else
								$verificacion = 'Para restar cantidad, debe ser menor o igual a la ingresada';
						}
					}
				}

				if (!isset($encontrado))
				{
					if($cantidad_barcode >0)
					{
						$pos = $i+1;
						$productos_mesa[$pos]['codigo'] = $cod_producto;
						$productos_mesa[$pos]['cant'] = $cantidad_barcode;
						$productos_mesa[$pos]['descripcion'] = $mostrar_producto[1];
						$productos_mesa[$pos]['tipo'] = $mostrar_producto[2];
						$productos_mesa[$pos]['valor_unitario'] = $mostrar_producto[3];
					}
					else
						$verificacion = '(1)Para agregar un producto la cantidad debe ser mayor a cero(0)';
				}
			}
			else
			{
				if($cantidad_barcode >0)
				{
					$pos = 1;
					$productos_mesa[$pos]['codigo'] = $cod_producto;
					$productos_mesa[$pos]['cant'] = $cantidad_barcode;
					$productos_mesa[$pos]['descripcion'] = $mostrar_producto[1];
					$productos_mesa[$pos]['tipo'] = $mostrar_producto[2];
					$productos_mesa[$pos]['valor_unitario'] = $mostrar_producto[3];
				}
				else
					$verificacion = '(2)Para agregar un producto la cantidad debe ser mayor a cero(0)';
			}

			if($verificacion == 1)
			{
				if($cantidad_barcode<=$inventario)
				{
					$sql="UPDATE `productos` SET 
					`inventario`=(`inventario`-$cantidad_barcode)
					WHERE cod_producto='$cod_producto'";
					$verificacion = mysqli_query($conexion,$sql);
				}
				else
					$verificacion = 'El inventario para '.$mostrar_producto[1].' es: '.$inventario;
			}

			if($verificacion == 1)
			{
				if(!isset($productos_mesa[$pos]) && $i == 1)
					$productos_mesa = '';
				else
					$productos_mesa = json_encode($productos_mesa,JSON_UNESCAPED_UNICODE);

				$sql="UPDATE `mesas` SET 
				`productos`='$productos_mesa'
				WHERE cod_mesa='$cod_mesa'";

				$verificacion = mysqli_query($conexion,$sql);

				$inventario_nuevo = $inventario-$cantidad_barcode;
			}
		}
		else
			$verificacion = 'No existe producto con el codigo ingresado';
	}
	else
		$verificacion = 'No se pueden agregar productos porque la caja NO se encuentra abierta';
}
else
	$verificacion = 'Ingrese un codigo';

$datos=array(
	'consulta' => $verificacion,
	'produc_code' => $produc_code,
	'inventario_nuevo' => $inventario_nuevo
);

echo json_encode($datos);

?>