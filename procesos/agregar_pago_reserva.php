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

	$cod_reserva = $_POST['cod_reserva'];
	$metodo_pago = $_POST['metodo_pago'];
	$valor_pago = str_replace('.', '', $_POST['valor_pago']);

	if($valor_pago == '')
		$verificacion = 'Ingrese el valor del pago';
	if($_POST['metodo_pago'] == '')
		$verificacion = 'Seleccione un método de pago';

	if($verificacion == 1)
	{
		$sql_reserva = "SELECT `codigo`, `nombre`, `productos`, `estado`, `fecha_registro`, `pagos` FROM `reservas` WHERE codigo = '$cod_reserva'";
		$result_reserva=mysqli_query($conexion,$sql_reserva);
		$mostrar_reserva=mysqli_fetch_row($result_reserva);

		$pagos = array();
		$pos = 1;
		if ($mostrar_reserva[5] != '')
		{
			$pagos = json_decode($mostrar_reserva[5],true);
			$pos += count($pagos);
		}

		$pagos[$pos] = array(
			'tipo' => $metodo_pago,
			'valor' => $valor_pago,
            'fecha' => $fecha_h,
            'creador' => $usuario,
			'eliminable' => 'NO'
		);

		$pagos = json_encode($pagos,JSON_UNESCAPED_UNICODE);

		$sql="UPDATE `reservas` SET `pagos`='$pagos' WHERE codigo='$cod_reserva'";

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