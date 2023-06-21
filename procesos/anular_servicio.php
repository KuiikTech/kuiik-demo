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

	$cod_servicio = $_POST['cod_servicio'];

	$estado = 'ANULADO';

	$operacion = 'Anulación de servicio';

	$sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro` FROM `servicios` WHERE `codigo` = '$cod_servicio'";
	$result=mysqli_query($conexion,$sql);
	$mostrar=mysqli_fetch_row($result);

	$result_2=mysqli_query($conexion,$sql);
	$info_respaldo = $result_2->fetch_object();

	$informacion = array();
	if($mostrar[4] != '')
	{
		$informacion = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[4]);
		$informacion = str_replace('	', ' ', $informacion);
		$informacion = json_decode($informacion,true);
	}

	$informacion['fecha_anulacion'] = $fecha_h;
	$informacion['anuló'] = $usuario;

	$info_registro = array(
		'cod_servicio' => $cod_servicio,
		'fecha_anulacion' => $fecha_h,
		'responsable' => $usuario
	);

	$info_registro = array(
		'Tipo' => 'Anulación de servicio',
		'Información' => $info_registro
	);

	$info_registro = json_encode($info_registro,JSON_UNESCAPED_UNICODE);

	$sql="INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
		'$info_registro',
		'$usuario',
		'$fecha_h')";
	$verificacion = mysqli_query($conexion,$sql);

	if($verificacion == 1)
	{
		$informacion = json_encode($informacion,JSON_UNESCAPED_UNICODE);

		$sql="UPDATE `servicios` SET 
		`estado`='$estado',
		`informacion`='$informacion'
		WHERE codigo='$cod_servicio'";

		$verificacion = mysqli_query($conexion,$sql);
	}

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
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
