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

	$cod_espacio = $_POST['cod_espacio'];
	$item = $_POST['item'];
	$valor = $_POST['valor'];

	$verificacion = 1;

	$sql_espacio = "SELECT `codigo`, `nombre`, `items`, `fecha_creacion`, `cod_cliente`, `pagos`, `informacion`, `caja` FROM `espacios` WHERE codigo = '$cod_espacio'";
	$result_espacio=mysqli_query($conexion,$sql_espacio);
	$mostrar_espacio=mysqli_fetch_row($result_espacio);

	$informacion = array();
	if($mostrar_espacio[6] != '')
		$informacion = json_decode($mostrar_espacio[6],true);

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
		$verificacion = 'La lista de informacion no existe';

	if($verificacion == 1)
	{
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