<?php
date_default_timezone_set('America/Bogota');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();

$fecha_h = date('Y-m-d G:i:s');

$conexion = $obj_2->conexion();
$conexion = $obj_2->conexion();

if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];

	$verificacion = 1;

	$descripcion_producto = $_POST['descripcion_producto'];
	$categoria_producto = $_POST['categoria_producto'];
	$valor_producto = str_replace('.', '', $_POST['valor_producto']);
	$barcode = $_POST['barcode'];
	$area = $_POST['area_producto'];
	$tipo = $_POST['tipo_producto'];


	if ($area == '')
		$verificacion = 'Seleccione un Área';
	if ($tipo == '')
		$verificacion = 'Seleccione un Tipo';
	if ($valor_producto == '')
		$verificacion = 'Escriba el Valor del Producto';
	if ($categoria_producto == '')
		$verificacion = 'Seleccione una Categoría';
	if ($descripcion_producto == '')
		$verificacion = 'Escriba la descripción del Producto';

	if ($verificacion == 1) {
		$sql = "INSERT INTO `productos`(`descripcion`, `categoria`, `valor`, `inventario`, `estado`, `barcode`, `area`, `tipo`, `movimientos`, `fecha_registro`) VALUES (
		'$descripcion_producto',
		'$categoria_producto',
		'$valor_producto',
		'0',
		'DISPONIBLE',
		'$barcode',
		'$area',
		'$tipo',
		'',
		'$fecha_h')";

		$verificacion = mysqli_query($conexion, $sql);
	}
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion
);
echo json_encode($datos);
