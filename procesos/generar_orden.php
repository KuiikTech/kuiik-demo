<?php 
date_default_timezone_set('America/Bogota');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();

$fecha_h=date('Y-m-d G:i:s');

$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();

$cod_orden = '';

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$verificacion = 1;

	$input_fecha = $_POST['input_fecha'];
	$input_hora = $_POST['input_hora'];
	$input_minuto = $_POST['input_minuto'];
	$input_tipo = $_POST['input_tipo'];

	if (isset($_SESSION['lista_servicios']))
		$lista_servicios = $_SESSION['lista_servicios'];
	else
		$verificacion = 'No existen servicios agregados';

	if ($input_minuto =='')
		$verificacion = 'Seleccione la hora de la cita';
	if ($input_hora =='')
		$verificacion = 'Seleccione la hora de la cita';
	if ($input_fecha =='')
		$verificacion = 'Seleccione la fecha de la cita';

	if (isset($_SESSION['cliente_cita']))
		$cliente_cita = $_SESSION['cliente_cita'];
	else
		$verificacion = 'Seleccione un cliente / Agregue un nuevo cliente';

	if ($verificacion == 1)
	{
		$fecha_final = date('Y-m-d H:i:s',strtotime($input_fecha."+ ".$input_hora."hour"." + ".$input_minuto."minute"));

		foreach ($lista_servicios as $i => $servicio)
		{
			$codigo = $servicio['codigo'];
			$categoria = $servicio['categoria'];
			$descripcion = $servicio['descripcion'];
			$valor = $servicio['valor'];
			$recurso = $servicio['recurso'];
			$tiempo = $servicio['tiempo']-1;
			$estado = $servicio['estado'];
			$creador = $servicio['creador'];

			$fecha_inicial = $fecha_final;
			$fecha_final = date('Y-m-d H:i:s',strtotime($fecha_inicial." + ".$tiempo."minute"));

			$lista_servicios[$i]['fecha_inicial'] = $fecha_inicial;
			$lista_servicios[$i]['fecha_final'] = $fecha_final;

			$fecha_final = date('Y-m-d H:i:s',strtotime($fecha_final." + 1 minute"));

			$sql = "SELECT `codigo`, `categoria`, `nombre`, `descripcion`, `tiempo_estimado`, `precio`, `insumos`, `cuidados`, `garantias`, `recomendaciones`, `estado`, `fecha_registro` FROM `servicios` WHERE codigo = '$codigo'";
			$result=mysqli_query($conexion,$sql);
			$servicio = $result->fetch_object();

			$servicio = json_encode($servicio,JSON_UNESCAPED_UNICODE);
			$servicio = json_decode($servicio, true);

			$insumos_servicio = array();
			
			if($servicio['insumos'] != '')
				$insumos_servicio = json_decode($servicio['insumos'], true);
			
			$pos = 1;
			foreach ($insumos_servicio as $j => $insumo_servicio)
			{
				$cod_insumo = $insumo_servicio['codigo'];
				$item_insumo = $insumo_servicio['item'];

				$sql = "SELECT `codigo`, `descripcion`, `categoria`, `inventario`, `estado`, `barcode`, `unidades`, `fecha_registro` FROM `insumos` WHERE codigo = '$cod_insumo'";
				$result=mysqli_query($conexion,$sql);
				$mostrar_insumo=mysqli_fetch_row($result);

				$codigo = $mostrar_insumo[0];
				$descripcion = ucwords(mb_strtolower($mostrar_insumo[1]));
				$categoria = $mostrar_insumo[2];
				$estado = $mostrar_insumo[4];
				$barcode = $mostrar_insumo[5];
				$unidades = $mostrar_insumo[6];

				$inventario = json_decode($mostrar_insumo[3],true);

				$insumo = $inventario[$item_insumo];

				if($insumo['stock'] >= $insumo_servicio['cantidad'])
				{
					$insumos_cita[$pos]['codigo'] = $cod_insumo;
					$insumos_cita[$pos]['item'] = $item_insumo;
					$insumos_cita[$pos]['descripcion'] = $descripcion;
					$insumos_cita[$pos]['cantidad'] = $insumo_servicio['cantidad'];
					$insumos_cita[$pos]['unidades'] = $unidades;
					$insumos_cita[$pos]['creador'] = 'Sistema';
					$insumos_cita[$pos]['fecha'] = $fecha_h;

					$movimientos = array();
					$pos_2 = 1;
					if ($insumo['movimientos'] != '')
					{
						$movimientos = $insumo['movimientos'];
						$pos_2 += count($movimientos);
					}

					$movimientos[$pos_2] = array(
						'Tipo' => 'Salida por cita',
						'Cant' => '-'.$insumo_servicio['cantidad'],
						'creador' => 'Sistema',
						'Observaciones' => 'Servicio #'.$servicio['codigo'].' - '.$servicio['nombre'],
						'fecha' => $fecha_h 
					);

					$inventario[$item_insumo]['stock'] -= $insumo_servicio['item'];
					$inventario[$item_insumo]['movimientos'] = $movimientos;

					$pos ++;
				}
				else
					$verificacion = 'El inventario del insumo es menor a la cantidad ingresada';

				if($verificacion == 1)
				{
					$inventario = json_encode($inventario,JSON_UNESCAPED_UNICODE);
					$sql="UPDATE `insumos` SET 
					`inventario`='$inventario'
					WHERE codigo='$cod_insumo'";

					$verificacion = mysqli_query($conexion,$sql);
				}
			}

			if(isset($insumos_cita))
				$lista_servicios[$i]['insumos'] = $insumos_cita;
		}

		if($verificacion == 1)
		{
			$lista_servicios = json_encode($lista_servicios,JSON_UNESCAPED_UNICODE);
			$sql="INSERT INTO `ordenes`(`servicios`, `cliente`, `creador`, `tipo`, `fecha_registro`) VALUES (
			'$lista_servicios',
			'$cliente_cita',
			'$usuario',
			'$input_tipo',
			'".date('Y-m-d G:i:s')."')";

			$verificacion = mysqli_query($conexion,$sql);

			unset($_SESSION['cliente_cita']);
			unset($_SESSION['lista_servicios']);

			$sql="SELECT MAX(codigo) from ordenes";
			$result=mysqli_query($conexion,$sql);
			$ver=mysqli_fetch_row($result);

			$cod_orden = $ver[0]; 
		}
	}
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion,
	'cod_orden' => $cod_orden
);
echo json_encode($datos);
?>