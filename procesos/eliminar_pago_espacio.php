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

	$cod_espacio = $_POST['cod_espacio'];
	$item = $_POST['item'];

	$sql_mesa = "SELECT `codigo`, `nombre`, `items`, `fecha_creacion`, `cod_cliente`, `pagos`, `informacion`, `cambios`, `caja` FROM `espacios` WHERE `codigo` = '$cod_espacio'";
	$result_mesa=mysqli_query($conexion,$sql_mesa);
	$mostrar_mesa=mysqli_fetch_row($result_mesa);

	$pagos = array();
	$pagos_nuevos = array();
	$pos = 1;
	if ($mostrar_mesa[5] != '')
		$pagos = json_decode($mostrar_mesa[5],true);

	foreach ($pagos as $j => $pago)
	{
		if($j != $item)
		{
			$pagos_nuevos[$pos] = $pago;
			$pos ++;
		}
	}

	if($pos == 1)
		$pagos_nuevos = '';
	else
		$pagos_nuevos = json_encode($pagos_nuevos,JSON_UNESCAPED_UNICODE);

	$sql="UPDATE `espacios` SET `pagos`='$pagos_nuevos' WHERE `codigo`='$cod_espacio'";

	$verificacion = mysqli_query($conexion,$sql);
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>