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

	$cod_reserva = $_POST['cod_reserva'];
	$item = $_POST['item'];

	$sql_reserva = "SELECT `codigo`, `nombre`, `productos`, `estado`, `fecha_registro`, `pagos` FROM `reservas` WHERE codigo = '$cod_reserva'";
	$result_reserva=mysqli_query($conexion,$sql_reserva);
	$mostrar_reserva=mysqli_fetch_row($result_reserva);

	$pagos = array();
	$pagos_nuevos = array();
	$pos = 1;
	if ($mostrar_reserva[5] != '')
		$pagos = json_decode($mostrar_reserva[5],true);

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

	$sql="UPDATE `reservas` SET `pagos`='$pagos_nuevos' WHERE codigo='$cod_reserva'";

	$verificacion = mysqli_query($conexion,$sql);
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>