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

	$cod_insumo = $_POST['cod_insumo'];
	$input_valor_venta=str_replace('.', '', $_POST['input_valor_venta']);
	$input_stock_inicial = $_POST['input_stock_inicial'];
	$input_costo=str_replace('.', '', $_POST['input_costo']);

	if ($input_costo == '')
		$verificacion = 'Ingrese el costo del insumo';
	if ($input_stock_inicial == '')
		$verificacion = 'Ingrese la cantidad de insumo';
	if ($input_valor_venta == '')
		$verificacion = 'Ingrese el valor de venta del insumo';

	if ($verificacion == 1)
	{
		$sql = "SELECT `codigo`, `descripcion`, `categoria`, `inventario`, `estado`, `barcode`, `unidades`, `fecha_registro` FROM `insumos` WHERE codigo = '$cod_insumo'";
		$result=mysqli_query($conexion,$sql);
		$mostrar=mysqli_fetch_row($result);

		$unidades = $mostrar[6];

		$inventario = array();
		$pos = 1;
		if ($mostrar[6] != '')
		{
			$inventario = json_decode($mostrar[6],true);
			$pos += count($inventario);
		}

		$movimientos[1] = array(
			'Tipo' => 'Ingreso',
			'Cant' => '+'.$input_stock_inicial,
			'creador' => $usuario,
			'Observaciones' => '',
			'fecha' => $fecha_h );

		$inventario[$pos] = array(
			'costo' => $input_costo, 
			'valor_venta' => $input_valor_venta, 
			'creador' => $usuario, 
			'cant_inicial' => $input_stock_inicial, 
			'stock' => $input_stock_inicial, 
			'fecha_registro' => $fecha_h, 
			'movimientos' => $movimientos);

		if($verificacion == 1)
		{
			$inventario = json_encode($inventario,JSON_UNESCAPED_UNICODE);
			$sql="UPDATE `insumos` SET 
			`inventario`='$inventario'
			WHERE codigo='$cod_insumo'";

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
