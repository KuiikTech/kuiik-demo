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

	$item = $_POST['item'];
	$cant = $_POST['cant'];
	$valor_venta = str_replace('.', '', $_POST['valor_venta']);
	$costo = str_replace('.', '', $_POST['costo']);

	$sql = "SELECT `codigo`, `productos`, `proveedor`, `creador`, `estado`, `fecha_registro` FROM `compras` WHERE estado = 'EN PROCESO' order by fecha_registro DESC";
	$result=mysqli_query($conexion,$sql);
	$mostrar=mysqli_fetch_row($result);

	if($mostrar != NULL)
	{
		$cod_compra = $mostrar[0];
		if($mostrar[1]!= '')
		{
			$productos_compra = json_decode($mostrar[1],true);
			$pos = count($productos_compra)+1;

			foreach ($productos_compra as $i => $producto)
			{
				if($item == $i)
				{
					$productos_compra[$i]['valor_venta'] = $valor_venta;
					$productos_compra[$i]['costo'] = $costo;
					$productos_compra[$i]['cant'] = $cant;
				}
			}
			
			$productos_compra = json_encode($productos_compra,JSON_UNESCAPED_UNICODE);

			$sql="UPDATE `compras` SET 
			`productos`='$productos_compra'
			WHERE codigo='$cod_compra'";

			$verificacion = mysqli_query($conexion,$sql);
		}
		else
			$verificacion = 'No existen productos agregados';
	}
	else
		$verificacion = 'No se encontró una compra en proceso';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
