<?php 
require_once "../clases/conexion.php";

$obj= new conectar();
$conexion=$obj->conexion();
session_set_cookie_params(7*24*60*60);
session_start();

$eventos = array();

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$verificacion = 1;

	if(isset($_POST['cod_orden']))
	{
		$item = 0;
		$cod_orden = $_POST['cod_orden'];
		$sql = "SELECT `codigo`, `servicios`, `cliente`, `pagos`, `creador`, `fecha_registro` FROM `ordenes` WHERE codigo = '$cod_orden'";
		$result=mysqli_query($conexion,$sql);
		$mostrar=mysqli_fetch_row($result);

		$cod_cliente = $mostrar[2];
		$sql_cliente = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `telefono`, `direccion`, `fecha_registro` FROM `clientes` WHERE codigo = '$cod_cliente'";
		$result_cliente=mysqli_query($conexion,$sql_cliente);
		$ver_cliente=mysqli_fetch_row($result_cliente);

		$cliente = array(
			'codigo' => $ver_cliente[0], 
			'id' => $ver_cliente[1], 
			'nombre' => $ver_cliente[2].' '.$ver_cliente[3], 
			'telefono' => $ver_cliente[4], 
			'direccion' => $ver_cliente[5]
		);

		if ($mostrar[1] != '')
			$lista_servicios = json_decode($mostrar[1],true);

		foreach ($lista_servicios as $i => $servicio)
		{
			$codigo = $servicio['codigo'];
			$categoria = $servicio['categoria'];
			$descripcion = $servicio['descripcion'];
			$valor = $servicio['valor'];
			$recurso = $servicio['recurso'];
			$tiempo = $servicio['tiempo'];
			$estado = $servicio['estado'];
			$creador = $servicio['creador'];
			$inicial = $servicio['fecha_inicial'];
			$final = $servicio['fecha_final'];

			$color_texto = 'black';

			$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `foto`, `telefono`, `rol`, `fecha_registro`, `estado`, `color`, `movimientos` FROM `usuarios` WHERE codigo = '$recurso'";
			$result_e=mysqli_query($conexion,$sql_e);
			$ver_e=mysqli_fetch_row($result_e);

			$color = $ver_e[10];

			if ($estado == 'PENDIENTE')
				$color_borde = '#FF0000';
			else
				$color_borde = $color;

			$eventos[$item] = array(
				"id" => $mostrar[0],
				"item" => $i,
				"resourceId" => $recurso,
				"title" => ucwords(strtolower($cliente['nombre'])).' - '.$descripcion,
				"color" => $color,
				"textColor" => $color_texto,
				"start" => $inicial,
				"end" => $final,
				"borderColor" => $color_borde,
				"tipo" => 'CITA'
			);

			$item ++;
		}
	}
	else
	{
		$fecha_inicial_1 = $_POST['fecha_inicial'];
		$fecha_final_1 = $_POST['fecha_final'];

		$inicio = new DateTime($fecha_inicial_1);
		$final = new DateTime($fecha_final_1);

		$intervalo = DateInterval::createFromDateString('1 day');
		$periodo = new DatePeriod($inicio, $intervalo, $final);
		$item=0;

		foreach ($periodo as $fecha_i)
		{
			$fecha_inicial = $fecha_i->format("Y-m-d");

			$busqueda = '%"fecha_inicial":"'.$fecha_inicial.'%';

			$tipo = "AND (tipo = '' OR tipo = 'principal')";
			if(isset($_POST['tipo']))
				$tipo = "AND tipo = '".$_POST['tipo']."'";

			$sql = "SELECT `codigo`, `servicios`, `cliente`, `pagos`, `creador`, `fecha_registro` FROM `ordenes` WHERE servicios LIKE '$busqueda' $tipo";
			$result=mysqli_query($conexion,$sql);

			while ($mostrar=mysqli_fetch_row($result))
			{
				$cod_cliente = $mostrar[2];
				$sql_cliente = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `telefono`, `direccion`, `fecha_registro` FROM `clientes` WHERE codigo = '$cod_cliente'";
				$result_cliente=mysqli_query($conexion,$sql_cliente);
				$ver_cliente=mysqli_fetch_row($result_cliente);

				$cliente = array(
					'codigo' => $ver_cliente[0], 
					'id' => $ver_cliente[1], 
					'nombre' => $ver_cliente[2].' '.$ver_cliente[3], 
					'telefono' => $ver_cliente[4], 
					'direccion' => $ver_cliente[5]
				);

				if ($mostrar[1] != '')
					$lista_servicios = json_decode($mostrar[1],true);

				foreach ($lista_servicios as $i => $servicio)
				{
					$codigo = $servicio['codigo'];
					$categoria = $servicio['categoria'];
					$descripcion = $servicio['descripcion'];
					$valor = $servicio['valor'];
					$recurso = $servicio['recurso'];
					$tiempo = $servicio['tiempo'];
					$estado = $servicio['estado'];
					$creador = $servicio['creador'];
					$inicial = $servicio['fecha_inicial'];
					$final = $servicio['fecha_final'];

					if($estado != 'ELIMINADO')
					{
						$fecha_cita = date('Y-m-d',strtotime($inicial));

						if($fecha_cita == $fecha_inicial)
						{
							$color_texto = 'black';

							$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `foto`, `telefono`, `rol`, `fecha_registro`, `estado`, `color`, `movimientos` FROM `usuarios` WHERE codigo = '$recurso'";
							$result_e=mysqli_query($conexion,$sql_e);
							$ver_e=mysqli_fetch_row($result_e);

							$color = $ver_e[10];

							if ($estado == 'PENDIENTE')
								$color_borde = '#FF0000';
							else
								$color_borde = $color;

							$eventos[$item] = array(
								"id" => $mostrar[0],
								"item" => $i,
								"resourceId" => $recurso,
								"title" => ucwords(strtolower($cliente['nombre'])).' - '.$descripcion,
								"color" => $color,
								"textColor" => $color_texto,
								"start" => $inicial,
								"end" => $final,
								"borderColor" => $color_borde,
								"tipo" => 'CITA'
							);

							$item ++;
						}
					}
				}
			}
		}

		$fecha_inicial_2 = date('Y-m-d',strtotime($fecha_inicial_1));
		$fecha_final_2 = date('Y-m-d',strtotime($fecha_final_1.' -1 day'));

		if($fecha_inicial_2 == $fecha_final_2 && !isset($_POST['tipo']))
		{
			$sql = "SELECT `codigo`, `cod_usuario`, `fecha_registro`, `fecha_inicial`, `fecha_final`, `creador` FROM `horario_ocupado` WHERE fecha_inicial > '$fecha_inicial_1' AND fecha_final < '$fecha_final_1'";
			$result=mysqli_query($conexion,$sql);
			while ($mostrar=mysqli_fetch_row($result))
			{
				$eventos[$item] = [
					"id" => $mostrar[0],
					"resourceId" => $mostrar[1],
					"start" => $mostrar[3],
					"end" => $mostrar[4],
					"creador" => $mostrar[5],
					"color" => 'WHITE',
					"borderColor" => '#DF6C4F',
					"className" => 'fc-nonbusiness',
					"tipo" => 'HORARIO'
				];

				$item ++;
			}
		}
	}
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion,
	'eventos' => $eventos
);
echo json_encode($datos);
?>