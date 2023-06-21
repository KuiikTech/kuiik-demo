<?php
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();

$obj = new conectar();
$conexion = $obj->conexion();

$fecha_h = date('Y-m-d G:i:s');


if (isset($_SESSION['usuario_restaurante'])) {
	
	$usuario = $_SESSION['usuario_restaurante'];
	$verificacion = 1;
	$cod_compra = $_POST['cod_compra'];

	if (isset($_SESSION['usuario_restaurante2']))
		$local = 'Restaurante 2';
	else
		$local = 'Restaurante 1';

	$metodo_pago = $_POST['input_metodo_pago'];
	$valor_pago = str_replace('.', '', $_POST['input_valor_pago']);

	if ($valor_pago == '')
		$verificacion = 'Ingrese el valor del pago';
	if ($metodo_pago == '')
		$verificacion = 'Seleccione un método de pago';

	if ($verificacion == 1) {
		$sql = "SELECT `codigo`, `productos`, `proveedor`, `creador`, `fecha_registro`, `observaciones`, `estado`, `pagos` FROM `compras` WHERE codigo = '$cod_compra'";
		$result = mysqli_query($conexion, $sql);
		$mostrar = mysqli_fetch_row($result);

		$pagos = array();
		$pos = 1;
		if ($mostrar[7] != '') {
			$pagos = json_decode($mostrar[7], true);
			$pos += count($pagos);
		}

		$pagos[$pos] = array(
			'tipo' => $metodo_pago,
			'valor' => $valor_pago,
			'local' => $local,
			'creador' => $usuario,
			'fecha_creacion' => $fecha_h,
		);

		$item_serv = $pos;

		$pagos = json_encode($pagos, JSON_UNESCAPED_UNICODE);

		$sql = "UPDATE `compras` SET `pagos`='$pagos' WHERE codigo='$cod_compra'";

		$verificacion = mysqli_query($conexion, $sql);
	}
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion
);

echo json_encode($datos);
