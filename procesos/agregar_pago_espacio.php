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
	$caja = $_SESSION['caja_restaurante'];
	$verificacion = 1;
	$cod_unico = uniqid();

	if(isset($_SESSION['usuario_restaurante2']))
		$local = 'Restaurante 2';
	else
		$local = 'Restaurante 1';

	$cod_espacio = $_POST['cod_espacio'];
	$metodo_pago = $_POST['input_metodo_pago'];
	$valor_pago = str_replace('.', '', $_POST['input_valor_pago']);

	if($valor_pago == '')
		$verificacion = 'Ingrese el valor del pago';
	if($metodo_pago == '')
		$verificacion = 'Seleccione un método de pago';

	if($verificacion == 1)
	{
		$sql_espacio = "SELECT `codigo`, `nombre`, `items`, `fecha_creacion`, `cod_cliente`, `pagos`, `informacion`, `caja` FROM `espacios` WHERE codigo = '$cod_espacio'";
		$result_espacio=mysqli_query($conexion,$sql_espacio);
		$mostrar_espacio=mysqli_fetch_row($result_espacio);

		$pagos = array();
		$pos = 1;
		if ($mostrar_espacio[5] != '')
		{
			$pagos = json_decode($mostrar_espacio[5],true);
			$pos += count($pagos);
		}

		$pagos[$pos] = array(
			'tipo' => $metodo_pago,
			'valor' => $valor_pago,
			'local' => $local,
			'caja' => $caja,
			'creador' => $usuario,
			'fecha' => $fecha_h,
			'cod_unico' => $cod_unico

		);

		$pagos = json_encode($pagos,JSON_UNESCAPED_UNICODE);

		$sql="UPDATE `espacios` SET `pagos`='$pagos' WHERE codigo='$cod_espacio'";

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