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

	$cod_equipo = $_POST['cod_equipo'];
	$nombre = $_POST['nombre'];
	$tipo = $_POST['tipo'];
	$longitud = $_POST['longitud'];

	if ($nombre == '')
		$verificacion = 'Ingrese el nombre del equipo';
	if ($tipo == '')
		$verificacion = 'Seleccione el tipo de dato';

	if ($verificacion == 1)
	{
		$sql = "SELECT `codigo`, `nombre`, `informacion`, `estado`, `fecha_creacion`, `creador` FROM `tipo_equipos` WHERE `codigo` = '$cod_equipo'";
		$result=mysqli_query($conexion,$sql);
		$mostrar=mysqli_fetch_row($result);

		$informacion = array();
		$pos = 1;
		if($mostrar[2] != '')
		{
			$informacion = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[2]);
			$informacion = str_replace('  ', ' ', $informacion);
			$informacion = json_decode($informacion,true);

			$pos += count($informacion);
		}
		$informacion[$pos]['nombre'] = $nombre;
		$informacion[$pos]['tipo'] = $tipo;
		$informacion[$pos]['longitud'] = $longitud;
		$informacion[$pos]['fecha'] = $fecha_h;
		$informacion[$pos]['creador'] = $usuario;

		$informacion = json_encode($informacion,JSON_UNESCAPED_UNICODE);

		$sql="UPDATE `tipo_equipos` set 
		`informacion`='$informacion'
		where `codigo`='$cod_equipo'";

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
