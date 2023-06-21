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
	$input_valor_venta=str_replace('.', '', $_POST['input_valor_venta']);
	$input_valor_venta_mayor=str_replace('.', '', $_POST['input_valor_venta_mayor']);
	$input_stock_inicial = $_POST['input_stock_inicial'];
	$input_costo=str_replace('.', '', $_POST['input_costo']);
	$input_proveedor = $_POST['input_proveedor'];
	$input_marca = $_POST['input_marca'];
	$bodega = $_POST['bodega'];

	if ($input_costo == '')
		$verificacion = 'Ingrese el costo del producto';
	if ($input_stock_inicial == '')
		$verificacion = 'Ingrese la cantidad de producto';
	if ($input_valor_venta_mayor == '')
		$verificacion = 'Ingrese el valor de venta del producto al Mayor';
	if ($input_valor_venta == '')
		$verificacion = 'Ingrese el valor de venta del producto al público';

	if ($verificacion == 1)
	{
		$sql = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo = '$cod_producto'";
		$result=mysqli_query($conexion,$sql);
		$mostrar=mysqli_fetch_row($result);

		$inventario = array();
		$pos = 1;
		if($bodega == 'Principal')
			$bodega_inventario = 'inventario';
		else if($bodega == 'PDV_1')
			$bodega_inventario = 'inventario_1';
		else if($bodega == 'PDV_2')
			$bodega_inventario = 'inventario_2';
		else
			$verificacion = 'No se encontróla bodega seleccionada';

		if($verificacion == 1)
		{
			$inventario_p = array();
			$inventario_1 = array();
			$inventario_2 = array();

			$pos_p = 1;
			$pos_1 = 1;
			$pos_2 = 1;

			if ($mostrar[3] != '')
			{
				$inventario_p = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[3]);
				$inventario_p = str_replace('	', ' ', $inventario_p);
				$inventario_p = json_decode($inventario_p,true);
			}

			$pos_p += count($inventario_p);

			if ($mostrar[6] != '')
			{
				$inventario_1 = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[6]);
				$inventario_1 = str_replace('	', ' ', $inventario_1);
				$inventario_1 = json_decode($inventario_1,true);
			}

			$pos_1 += count($inventario_1);

			if ($mostrar[7] != '')
			{
				$inventario_2 = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[7]);
				$inventario_2 = str_replace('	', ' ', $inventario_2);
				$inventario_2 = json_decode($inventario_2,true);
			}

			$pos_2 += count($inventario_2);

			$pos_max = max($pos_p,$pos_1,$pos_2);

			$movimientos[1] = array(
				'Tipo' => 'Ingreso',
				'Cant' => '+'.$input_stock_inicial,
				'creador' => $usuario,
				'Observaciones' => '',
				'fecha' => $fecha_h );

			$inventario_p[$pos_max] = array(
				'costo' => $input_costo, 
				'valor_venta' => $input_valor_venta, 
				'valor_venta_mayor' => $input_valor_venta_mayor, 
				'creador' => $usuario, 
				'cant_inicial' => $input_stock_inicial, 
				'stock' => $input_stock_inicial, 
				'fecha_registro' => $fecha_h, 
				'marca' => $input_marca, 
				'proveedor' => $input_proveedor, 
				'movimientos' => $movimientos);

			$inventario_1[$pos_max] = array(
				'costo' => $input_costo, 
				'valor_venta' => $input_valor_venta, 
				'valor_venta_mayor' => $input_valor_venta_mayor, 
				'creador' => $usuario, 
				'cant_inicial' => $input_stock_inicial, 
				'stock' => $input_stock_inicial, 
				'fecha_registro' => $fecha_h, 
				'marca' => $input_marca, 
				'proveedor' => $input_proveedor, 
				'movimientos' => $movimientos);

			$inventario_2[$pos_max] = array(
				'costo' => $input_costo, 
				'valor_venta' => $input_valor_venta, 
				'valor_venta_mayor' => $input_valor_venta_mayor, 
				'creador' => $usuario, 
				'cant_inicial' => $input_stock_inicial, 
				'stock' => $input_stock_inicial, 
				'fecha_registro' => $fecha_h, 
				'marca' => $input_marca, 
				'proveedor' => $input_proveedor, 
				'movimientos' => $movimientos);

			if($bodega == 'Principal')
				$inventario = json_encode($inventario_p,JSON_UNESCAPED_UNICODE);
			else if($bodega == 'PDV_1')
				$inventario = json_encode($inventario_1,JSON_UNESCAPED_UNICODE);
			else if($bodega == 'PDV_2')
				$inventario = json_encode($inventario_2,JSON_UNESCAPED_UNICODE);

			$sql="UPDATE `productos` SET 
			`$bodega_inventario`='$inventario'
			WHERE codigo='$cod_producto'";

			$verificacion = mysqli_query($conexion,$sql);
		}

	}
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
