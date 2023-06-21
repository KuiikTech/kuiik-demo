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
	$num_item = $_POST['num_item'];

	if ($verificacion == 1)
	{
		$sql = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo = '$cod_producto'";
		$result=mysqli_query($conexion,$sql);
		$mostrar=mysqli_fetch_row($result);

		$codigo = $mostrar[0];
		$descripcion = ucwords(mb_strtolower($mostrar[1]));
		$categoria = $mostrar[2];
		$estado = $mostrar[4];
		$barcode = $mostrar[5];
		$fecha_registro = $mostrar[6];

		$inventario = array();
		if ($mostrar[6] != '')
			$inventario = json_decode($mostrar[6],true);

		foreach ($inventario as $i => $producto)
		{
			$valor_venta = $producto['valor_venta'];
			$stock = $producto['stock'];

			if($i == $num_item)
			{
				$carrito_productos = array();
				if (isset($_SESSION['carrito_productos']))
					$carrito_productos = $_SESSION['carrito_productos'];

				$pos = count($carrito_productos)+1;

				foreach ($carrito_productos as $j => $producto_c)
				{
					if($producto_c['num_inventario'] == $num_item && $producto_c['codigo'] == $cod_producto)
					{
						$carrito_productos[$j]['cant'] += 1;
						$encontrado = 1;
					}
				}

				if(!isset($encontrado))
				{
					$carrito_productos[$pos]['codigo'] = $cod_producto;
					$carrito_productos[$pos]['num_inventario'] = $num_item;
					$carrito_productos[$pos]['descripcion'] = $descripcion;
					$carrito_productos[$pos]['valor'] = $valor_venta;
					$carrito_productos[$pos]['cant'] = 1;
				}

				$_SESSION['carrito_productos'] = $carrito_productos;
			}
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
