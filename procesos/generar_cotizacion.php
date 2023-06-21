<?php 
date_default_timezone_set('America/Bogota');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();

$fecha_h=date('Y-m-d G:i:s');

$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();

$cod_cotizacion = '';

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$verificacion = 1;

	$input_servicio = $_POST['input_servicio'];
	$input_recurso = $_POST['input_recurso'];
	$valor_cotizacion = str_replace('.', '', $_POST['valor_cotizacion']);
	$obs_cotizacion = $_POST['obs_cotizacion'];

	if ($valor_cotizacion =='')
		$verificacion = 'Ingrese el valor del servicio';
	if ($input_recurso =='')
		$verificacion = 'Seleccione el cotizante del servicio';
	if ($input_servicio =='')
		$verificacion = 'Seleccione el servicio a cotizar';

	if (isset($_SESSION['cliente_cot']))
		$cliente_cot = $_SESSION['cliente_cot'];
	else
		$verificacion = 'Seleccione un cliente / Agregue un nuevo cliente';

	if($verificacion == 1)
	{
		$sql = "SELECT `codigo`, `categoria`, `nombre`, `descripcion`, `tiempo_estimado`, `precio`, `insumos`, `cuidados`, `garantias`, `recomendaciones`, `estado`, `fecha_registro` FROM `servicios` WHERE codigo = '$input_servicio'";
		$result=mysqli_query($conexion,$sql);
		$mostrar=mysqli_fetch_row($result);

		$categoria = $mostrar[1];
		$descripcion = $mostrar[2];

		$servicio['codigo'] = $input_servicio;
		$servicio['categoria'] = $categoria;
		$servicio['descripcion'] = $descripcion;
		$servicio['valor'] = $valor_cotizacion;

		$servicio = json_encode($servicio,JSON_UNESCAPED_UNICODE);
		$sql="INSERT INTO `cotizaciones`(`cliente`, `servicio`, `cotizó`, `creador`, `observaciones`, `estado`, `fecha_registro`) VALUES (
		'$cliente_cot',
		'$servicio',
		'$input_recurso',
		'$usuario',
		'$obs_cotizacion',
		'PENDIENTE',
		'".date('Y-m-d G:i:s')."')";

		$verificacion = mysqli_query($conexion,$sql);

		unset($_SESSION['cliente_cot']);

		$sql="SELECT MAX(codigo) from cotizaciones";
		$result=mysqli_query($conexion,$sql);
		$ver=mysqli_fetch_row($result);

		$cod_cotizacion = $ver[0]; 
	}
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion,
	'cod_cotizacion' => $cod_cotizacion
);
echo json_encode($datos);
?>