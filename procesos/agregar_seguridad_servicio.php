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

	$verificacion = 'No se encontró la operación solicitada';

	if(isset($_POST['cod_espacio']))
	{
		$cod_espacio = $_POST['cod_espacio'];
		$input_seguridad = $_POST['input_seguridad'];
		$input_valor_seguridad = $_POST['input_valor_seguridad'];

		$verificacion = 1;

		if ($input_valor_seguridad == '')
			$verificacion = 'Ingrese el valor de seguridad del equipo';
		if ($input_seguridad == '')
			$verificacion = 'Seleccione el tipo de seguridad del equipo';

		if ($verificacion == 1)
		{
			$sql_espacio = "SELECT `codigo`, `nombre`, `items`, `fecha_creacion`, `cod_cliente`, `pagos`, `informacion`, `caja` FROM `espacios` WHERE codigo = '$cod_espacio'";
			$result_espacio=mysqli_query($conexion,$sql_espacio);
			$mostrar_espacio=mysqli_fetch_row($result_espacio);

			$informacion = array();
			if($mostrar_espacio[6] != '')
			{
				$informacion = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar_espacio[6]);
				$informacion = str_replace('	', ' ', $informacion);
				$informacion = json_decode($informacion,true);
			}

			$items = array();
			if(isset($informacion['seguridad']))
				$items = $informacion['seguridad'];

			$pos = count($items)+1;

			$items[$pos]['tipo_seguridad'] = $input_seguridad;
			$items[$pos]['valor'] = $input_valor_seguridad;
			$items[$pos]['creador'] = $usuario;

			$informacion['seguridad'] = $items;

			$informacion = json_encode($informacion,JSON_UNESCAPED_UNICODE);

			$sql="UPDATE `espacios` SET `informacion`='$informacion' WHERE codigo='$cod_espacio'";

			$verificacion = mysqli_query($conexion,$sql);
		}
	}

	if(isset($_POST['cod_servicio']))
	{
		$cod_servicio = $_POST['cod_servicio'];
		$input_seguridad = $_POST['input_seguridad'];
		$input_valor_seguridad = $_POST['input_valor_seguridad'];

		$verificacion = 1;

		if ($input_valor_seguridad == '')
			$verificacion = 'Ingrese el valor de seguridad del equipo';
		if ($input_seguridad == '')
			$verificacion = 'Seleccione el tipo de seguridad del equipo';

		if ($verificacion == 1)
		{
			$operacion = 'Ingreso de seguridad ('.$input_seguridad.')';

			$sql_servicio = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro`, `local` FROM `servicios` WHERE codigo = '$cod_servicio'";
			$result_servicio=mysqli_query($conexion,$sql_servicio);
			$mostrar_servicio=mysqli_fetch_row($result_servicio);

			$result_2=mysqli_query($conexion,$sql_servicio);
			$info_respaldo = $result_2->fetch_object();

			$informacion = array();
			if($mostrar_servicio[4] != '')
			{
				$informacion = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar_servicio[4]);
				$informacion = str_replace('	', ' ', $informacion);
				$informacion = json_decode($informacion,true);
			}

			$items = array();
			if(isset($informacion['seguridad']))
				$items = $informacion['seguridad'];

			$pos = count($items)+1;

			$items[$pos]['tipo_seguridad'] = $input_seguridad;
			$items[$pos]['valor'] = $input_valor_seguridad;
			$items[$pos]['creador'] = $usuario;

			$informacion['seguridad'] = $items;

			$informacion = json_encode($informacion,JSON_UNESCAPED_UNICODE);

			$sql="UPDATE `servicios` SET `informacion`='$informacion' WHERE codigo='$cod_servicio'";

			$verificacion = mysqli_query($conexion,$sql);

			if ($verificacion == 1)
			{
				$info_respaldo = json_encode($info_respaldo,JSON_UNESCAPED_UNICODE);
				$sql="INSERT INTO `respaldo_info`(`cod_servicio`, `informacion`, `operacion`, `usuario`, `fecha_registro`) VALUES (
					'$cod_servicio',
					'$info_respaldo',
					'$operacion',
					'$usuario',
					'$fecha_h')";

				mysqli_query($conexion,$sql);
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
