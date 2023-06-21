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
	$input_cant = $_POST['input_cant'];
	$input_descripcion = $_POST['input_descripcion'];
	$input_valor=str_replace('.', '', $_POST['input_valor']);

	if ($input_descripcion == '')
		$verificacion = 'Escriba la descripción del item';
	if ($input_valor == '')
		$verificacion = 'Ingrese el valor del item';
	if ($input_cant == '')
		$verificacion = 'Ingrese la cantidad del item';

	if ($verificacion == 1)
	{
		$sql = "SELECT `codigo`, `info`, `items`, `pagos`, `cliente`, `responsable`, `fecha_entrega`, `fecha_registro`, `estado`, `movimientos` FROM `trabajos` WHERE codigo = '$cod_trabajo'";
		$result=mysqli_query($conexion,$sql);
		$mostrar=mysqli_fetch_row($result);

		$items_trabajo = array();
		$pos = 1;
		if ($mostrar[2] != '')
		{
			$items_trabajo = json_decode($mostrar[2],true);
			$pos += count($items_trabajo);
		}

		$items_trabajo[$pos]['codigo'] = NULL;
		$items_trabajo[$pos]['cant'] = $input_cant;
		$items_trabajo[$pos]['descripcion'] = $input_descripcion;
		$items_trabajo[$pos]['valor_unitario'] = $input_valor;

		$movimientos = array();
		$pos = 1;
		if ($mostrar[9] != '')
		{
			$movimientos = json_decode($mostrar[9],true);
			$pos += count($movimientos);
		}

		$movimientos[$pos] = array(
			'tipo' => 'Item agregado',
			'codigo' => NULL,
			'descripcion' => $input_descripcion,
			'cant' => $input_cant,
			'valor_unitario' => $input_valor,
			'fecha' => $fecha_h
		);

		$items_trabajo = json_encode($items_trabajo,JSON_UNESCAPED_UNICODE);
		$movimientos = json_encode($movimientos,JSON_UNESCAPED_UNICODE);
		$sql="UPDATE `trabajos` SET 
		`items`='$items_trabajo',
		`movimientos`='$movimientos'
		WHERE codigo='$cod_trabajo'";

		$verificacion = mysqli_query($conexion,$sql);
	}
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
