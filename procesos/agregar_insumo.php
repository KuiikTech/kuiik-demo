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

	$descripcion_insumo = $_POST['descripcion_insumo'];
	$categoria_insumo = $_POST['categoria_insumo'];
	$unidades = $_POST['unidades_insumo'];
	$barcode = $_POST['barcode'];

	if ($categoria_insumo =='')
		$verificacion='Seleccione una Categoría';
	if ($descripcion_insumo =='')
		$verificacion='Escriba la descripción del Insumo';

	if ($verificacion == 1)
	{

		$sql="INSERT INTO `insumos`(`descripcion`, `categoria`, `inventario`, `estado`, `barcode`, `unidades`, `fecha_registro`) VALUES (
		'$descripcion_insumo',
		'$categoria_insumo',
		'',
		'DISPONIBLE',
		'$barcode',
		'$unidades',
		'".date('Y-m-d G:i:s')."')";

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
