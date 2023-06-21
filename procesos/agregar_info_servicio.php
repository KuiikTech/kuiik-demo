<?php 
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();

$obj= new conectar();
$conexion=$obj->conexion();

$fecha_h = date('Y-m-d G:i:s');

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$cod_espacio = $_POST['cod_espacio'];
	$info = $_POST['info'];
	$valor = $_POST['valor'];

	$verificacion = 1;

	if($info == '')
		$verificacion = 'Seleccione la información';
		if($valor == '')
		$verificacion = 'Ingrese la info';

	if($verificacion == 1)
	{
		$sql_espacio = "SELECT `codigo`, `nombre`, `items`, `fecha_creacion`, `cod_cliente`, `pagos`, `informacion`, `caja` FROM `espacios` WHERE codigo = '$cod_espacio'";
		$result_espacio=mysqli_query($conexion,$sql_espacio);
		$mostrar_espacio=mysqli_fetch_row($result_espacio);

		$informacion = array();
		if($mostrar_espacio[6] != '')
			$informacion = json_decode($mostrar_espacio[6],true);

		$info_equipo = array();
		if(isset($informacion['info_equipo']))
			$info_equipo = $informacion['info_equipo'];
		$pos = count($info_equipo)+1;

		$informacion['info_equipo'][$pos]['info'] = $info;
		$informacion['info_equipo'][$pos]['valor'] = $valor;

		$informacion = json_encode($informacion,JSON_UNESCAPED_UNICODE);

		$sql="UPDATE `espacios` SET `informacion`='$informacion' WHERE codigo='$cod_espacio'";

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