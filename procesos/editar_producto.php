<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();
$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();
$fecha_h=date('Y-m-d G:i:s');

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];
	$verificacion = 1;

	$cod_producto_U = $_POST['cod_producto_U'];
	$descripcion_producto = $_POST['descripcion_producto_U'];
	$categoria_producto = $_POST['categoria_producto_U'];
	$barcode = $_POST['barcode_U'];
	$valor = str_replace('.', '', $_POST['valor_producto_U']);
	$area = $_POST['area_producto_U'];
	$tipo = $_POST['tipo_producto_U'];

	if ($area =='')
		$verificacion='Seleccione un Área';
	if ($tipo =='')
		$verificacion='Seleccione un Tipo';
	if ($valor =='')
		$verificacion='Escriba el Valor del Producto';
	if ($categoria_producto =='')
		$verificacion='Seleccione una Categoría';
	if ($descripcion_producto =='')
		$verificacion='Escriba la descripción del Producto';

	if ($verificacion == 1)
	{
		$sql="UPDATE `productos` SET 
		`descripcion`='$descripcion_producto',
		`categoria`='$categoria_producto',
		`barcode`='$barcode',
		`valor`='$valor',
		`area`='$area',
		`tipo`='$tipo'
		WHERE codigo='$cod_producto_U'";

		$verificacion = mysqli_query($conexion,$sql);
	}
}
else
	$verificacion = 'Reload';

if(!isset($datos))
{
	$datos=array(
		'consulta' => $verificacion
	);
}

echo json_encode($datos);
?>