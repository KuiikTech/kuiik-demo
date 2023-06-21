<?php 
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();

$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();

$fecha_h = date('Y-m-d G:i:s');

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$cod_servicio = $_POST['cod_servicio'];
	$item = $_POST['item'];
	$valor = $_POST['valor'];

	$operacion = 'Cambio de información de equipo';

	$verificacion = 1;

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

	if(isset($informacion['lista_info']))
	{
		if($informacion['lista_info'][$item]['longitud'] == '')
			$informacion['lista_info'][$item]['valor'] = $valor;
		else
		{
			$longitud = strlen($valor);
			if($informacion['lista_info'][$item]['longitud'] == $longitud)
				$informacion['lista_info'][$item]['valor'] = $valor;
			else
				$verificacion = $informacion['lista_info'][$item]['nombre'].' debe tener '.$informacion['lista_info'][$item]['longitud'].' caracteres';
		}
	}
	else
		$verificacion = 'La lista de información no existe';

	if($verificacion == 1)
	{
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
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>