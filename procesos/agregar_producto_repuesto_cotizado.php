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

	$sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo='$cod_producto'";
	$result_producto=mysqli_query($conexion,$sql_producto);
	$mostrar_producto=mysqli_fetch_row($result_producto);

	if ($mostrar_producto != null)
	{
		$sql = "SELECT `codigo`, `producto`, `proveedor`, `creador`, `estado`, `fecha_registro` FROM `repuestos_cotizados` WHERE estado = 'EN PROCESO' order by fecha_registro DESC";
		$result=mysqli_query($conexion,$sql);
		$mostrar=mysqli_fetch_row($result);

		if($mostrar != NULL)
		{
			$cod_repuesto_cotizado = $mostrar[0];
			$nombre_producto = $mostrar_producto[1];
			$cod_categoria = $mostrar_producto[2];
			if($mostrar[1]!= '')
			{
				$productos_repuesto_cotizado = json_decode($mostrar[1],true);
				$pos = count($productos_repuesto_cotizado)+1;

				foreach ($productos_repuesto_cotizado as $i => $item)
				{
					if($cod_producto == $item['codigo'])
						$verificacion = 'El producto ya se encuentra agregado';
				}
			}
			else
			{
				$productos_repuesto_cotizado = array();
				$pos = 1;
			}

			if($verificacion == 1)
			{
				$productos_repuesto_cotizado[$pos]['codigo'] = $cod_producto;
				$productos_repuesto_cotizado[$pos]['descripcion'] = $nombre_producto;
				$productos_repuesto_cotizado[$pos]['categoria'] = $cod_categoria;
				$productos_repuesto_cotizado[$pos]['valor_venta'] = '';
				$productos_repuesto_cotizado[$pos]['marca'] = '';
				$productos_repuesto_cotizado[$pos]['valor_venta_mayor'] = '';
				$productos_repuesto_cotizado[$pos]['costo'] = '';
				$productos_repuesto_cotizado[$pos]['cant_bp'] = '';
				$productos_repuesto_cotizado[$pos]['cant_b1'] = '';
				$productos_repuesto_cotizado[$pos]['cant_b2'] = '';

				$productos_repuesto_cotizado = json_encode($productos_repuesto_cotizado,JSON_UNESCAPED_UNICODE);

				$sql="UPDATE `repuestos_cotizados` SET 
				`producto`='$productos_repuesto_cotizado'
				WHERE codigo='$cod_repuesto_cotizado'";

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
