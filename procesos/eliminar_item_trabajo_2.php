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

	$cod_trabajo = $_POST['cod_trabajo'];
	$num_item = $_POST['num_item'];

	$sql = "SELECT `codigo`, `info`, `items`, `pagos`, `cliente`, `responsable`, `fecha_entrega`, `fecha_registro`, `estado`, `movimientos` FROM `trabajos` WHERE codigo = '$cod_trabajo'";
	$result=mysqli_query($conexion,$sql);
	$mostrar=mysqli_fetch_row($result);

	$items_trabajo = json_decode($mostrar[2],true);

	$movimientos = array();
	$pos = 1;
	if ($mostrar[9] != '')
	{
		$movimientos = json_decode($mostrar[9],true);
		$pos += count($movimientos);
	}

	$movimientos[$pos] = array(
		'tipo' => 'Item eliminado',
		'codigo' => $items_trabajo[$num_item]['codigo'],
		'descripcion' => $items_trabajo[$num_item]['descripcion'],
		'cant' => $items_trabajo[$num_item]['cant'],
		'valor_unitario' => $items_trabajo[$num_item]['valor_unitario'],
		'fecha' => $fecha_h
	);

	$pos = 1;
	foreach ($items_trabajo as $i => $item)
	{
		if($num_item != $i)
		{
			$items_trabajo_nuevo[$pos] = $item;
			$pos++;
		}
	}

	if(isset($items_trabajo_nuevo))
		$items_trabajo_nuevo = json_encode($items_trabajo_nuevo,JSON_UNESCAPED_UNICODE);
	else
		$items_trabajo_nuevo = '';
	$movimientos = json_encode($movimientos,JSON_UNESCAPED_UNICODE);
	$sql="UPDATE `trabajos` SET 
	`items`='$items_trabajo_nuevo',
	`movimientos`='$movimientos'
	WHERE codigo='$cod_trabajo'";

	$verificacion = mysqli_query($conexion,$sql);
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
