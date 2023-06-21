<?php 
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();

$obj= new conectar();
$conexion=$obj->conexion();

$fecha_h = date('Y-m-d G:i:s');

$usuario = $_SESSION['usuario_restaurante'];

$verificacion = 1;

$pos = $_POST['num_item'];

if(isset($_SESSION['items_factura']))
{
	if(count($_SESSION['items_factura']) == 1)
	{
		$items_factura_nuevos = '';
		unset($_SESSION['items_factura']);
	}
	else
	{
		$j = 1;
		$items_factura = $_SESSION['items_factura'];
		foreach ($items_factura as $i => $item)
		{
			if($pos != $i)
			{
				$items_factura_nuevos[$j] = $item;
				$j++;
			}
		}
		$_SESSION['items_factura'] = $items_factura_nuevos;
	}
}
else
	$verificacion = 'No existen items agregados';

if (!isset($_SESSION['items_factura']))
	$btn_generar = true;
else
	$btn_generar = false;

$datos=array(
	'consulta' => $verificacion,
	'btn_generar' => $btn_generar
);

echo json_encode($datos);

?>