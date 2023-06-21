<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
session_set_cookie_params(7 * 24 * 60 * 60);
session_start();

$btn_generar = true;

if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];
	$verificacion = 1;

	$descripcion = $_POST['descripcion'];
	$cantidad = $_POST['cantidad'];
	$valor_unitario = str_replace('.', '', $_POST['valor_unitario']);

	if ($descripcion == '')
		$verificacion = 'Ingrese una descripción';
	if ($cantidad == '')
		$verificacion = 'Ingrese una cantidad';
	if ($valor_unitario == '')
		$verificacion = 'Ingrese un valor unitario';

	if ($verificacion == 1) {

		$items_factura = array();

		if (isset($_SESSION['items_factura'])) {
			$items_factura = $_SESSION['items_factura'];
			$count = count($items_factura) + 1;
		} else
			$count = 1;

		$items_factura[$count]['descripcion'] = $descripcion;
		$items_factura[$count]['cant'] = $cantidad;
		$items_factura[$count]['valor_unitario'] = $valor_unitario;

		$_SESSION['items_factura'] = $items_factura;

		if (!isset($_SESSION['items_factura']))
			$btn_generar = true;
		else
			$btn_generar = false;
	}
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion,
	'btn_generar' => $btn_generar
);

echo json_encode($datos);
