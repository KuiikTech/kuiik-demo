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

	$sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode`, `movimientos`, `especial` FROM `productos` WHERE codigo='$cod_producto'";
	$result_producto=mysqli_query($conexion,$sql_producto);
	$mostrar_producto=mysqli_fetch_row($result_producto);

	if ($mostrar_producto != null)
	{
		$sql = "SELECT `codigo`, `productos`, `proveedor`, `creador`, `estado`, `fecha_registro` FROM `compras` WHERE estado = 'EN PROCESO' order by fecha_registro DESC";
		$result=mysqli_query($conexion,$sql);
		$mostrar=mysqli_fetch_row($result);

		if($mostrar != NULL)
		{
			$cod_compra = $mostrar[0];
			$nombre_producto = $mostrar_producto[1];
			$cod_categoria = $mostrar_producto[5];
			$valor_venta = $mostrar_producto[3];
			if($mostrar[1]!= '')
			{
				$productos_compra = json_decode($mostrar[1],true);
				$pos = count($productos_compra)+1;

				foreach ($productos_compra as $i => $item)
				{
					if($cod_producto == $item['codigo'])
						$verificacion = 'El producto ya se encuentra agregado a la compra';
				}
			}
			else
			{
				$productos_compra = array();
				$pos = 1;
			}

			if($verificacion == 1)
			{
				$productos_compra[$pos]['codigo'] = $cod_producto;
				$productos_compra[$pos]['descripcion'] = $nombre_producto;
				$productos_compra[$pos]['categoria'] = $cod_categoria;
				$productos_compra[$pos]['valor_venta'] = $valor_venta;
				$productos_compra[$pos]['costo'] = '';
				$productos_compra[$pos]['cant'] = '';

				$productos_compra = json_encode($productos_compra,JSON_UNESCAPED_UNICODE);

				$sql="UPDATE `compras` SET 
				`productos`='$productos_compra'
				WHERE codigo='$cod_compra'";

				$verificacion = mysqli_query($conexion,$sql);
			}
		}
		else
			$verificacion = 'No se encontró una compra en proceso';
	}
	else
		$verificacion = 'No se encontró el producto seleccionado';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
