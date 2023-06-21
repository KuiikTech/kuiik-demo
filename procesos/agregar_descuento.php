<?php 
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();

$obj= new conectar();
$conexion=$obj->conexion();

$fecha_h = date('Y-m-d G:i:s');

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];
	$verificacion = 1;

	$cod_mesa = $_POST['cod_mesa'];
	$descripcion_descuento = $_POST['descripcion_descuento'];
	$valor_descuento = str_replace('.', '', $_POST['valor_descuento']);

	if($valor_descuento == '')
		$verificacion = 'Ingrese el valor del descuento';
	if($_POST['descripcion_descuento'] == '')
		$verificacion = 'Escriba la descripción del descuento';

	if($verificacion == 1)
	{
		$descuento = array(
			'descripcion' => $descripcion_descuento,
			'valor' => $valor_descuento
		);

		$descuento = json_encode($descuento,JSON_UNESCAPED_UNICODE);

		$sql="UPDATE `mesas` SET `pagos`='$descuento' WHERE cod_mesa='$cod_mesa'";

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