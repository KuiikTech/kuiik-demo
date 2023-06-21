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

	if ($_POST['input_hora_e'] != '')
		$input_hora_e = $_POST['input_hora_e'];
	else
		$verificacion = 'Seleccione la hora posible de entrega.';

	if ($_POST['input_fecha_e'] != '')
		$input_fecha_e = $_POST['input_fecha_e'];
	else
		$verificacion = 'Seleccione la fecha posible de entrega.';

	if (isset($_SESSION['pagos_trabajo']))
		$pagos_trabajo = $_SESSION['pagos_trabajo'];
	else
		$verificacion = 'No existen pagos agregados. Agregue al menos uno para continuar.';

	if (isset($_SESSION['items_trabajo']))
		$items_trabajo = $_SESSION['items_trabajo'];
	else
		$verificacion = 'No existen items agregados. Agregue al menos uno para continuar.';

	if ($_POST['input_titulo'] != '')
		$input_titulo = $_POST['input_titulo'];
	else
		$verificacion = 'Ingrese un título para el trabajo.';

	if (isset($_SESSION['cliente_trabajo']))
		$cod_cliente = $_SESSION['cliente_trabajo'];
	else
		$verificacion = 'Seleccione un cliente o ingrese uno nuevo.';

	if($verificacion == 1)
	{
		$total_items = 0;
		foreach ($items_trabajo as $i => $item)
			$total_items += $item['valor_unitario']*$item['cant'];

		$total_pagos = 0;
		foreach ($pagos_trabajo as $i => $pago)
			$total_pagos += $pago['valor'];

		if ($total_pagos > $total_items)
			$verificacion = 'El total de los pagos supera el total de la orden';

		if($verificacion == 1)
		{
			$fecha_entrega = date('Y-m-d G:i:00',strtotime($input_fecha_e . ' '.$input_hora_e));
			$descripcion = $_POST['input_observaciones'];
			$items_trabajo = json_encode($items_trabajo,JSON_UNESCAPED_UNICODE);
			$pagos_trabajo = json_encode($pagos_trabajo,JSON_UNESCAPED_UNICODE);

			$data_send=array(
				ucwords($input_titulo),
				$items_trabajo,
				$pagos_trabajo,
				$cod_cliente,
				$descripcion,
				$usuario,
				$fecha_entrega
			);

			$verificacion = $obj->agregar_trabajo($data_send);

			if($verificacion == 1)
			{
				unset($_SESSION['cliente_trabajo']);
				unset($_SESSION['items_trabajo']);
				unset($_SESSION['pagos_trabajo']);
				unset($_SESSION['titulo_trabajo']);
				unset($_SESSION['observaciones_trabajo']);
				unset($_SESSION['fecha_trabajo']);
				unset($_SESSION['hora_trabajo']);
			}
		}
	}
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
