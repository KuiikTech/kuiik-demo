<?php 
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();

$obj_2= new conectar();
$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();

$fecha_h = date('Y-m-d G:i:s');

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	if(isset($_POST['cod_mesa']))
		$cod_mesa = $_POST['cod_mesa'];
	if(isset($_POST['cod_espacio']))
		$cod_espacio = $_POST['cod_espacio'];

	$busqueda = $_POST['input_busqueda'];

	$sql = "SELECT `codigo`, `id`, `nombre`, `telefono` FROM `clientes` WHERE `id`='$busqueda'";
	$result=mysqli_query($conexion,$sql);
	$mostrar=mysqli_fetch_row($result);

	if($mostrar != NULL)
	{
		$cod_cliente = $mostrar[0];
		$verificacion = 'No se encontró la operación solicitada';
		if(isset($_POST['cod_mesa']))
		{
			$sql="UPDATE `mesas` SET `cod_cliente`='$cod_cliente' WHERE cod_mesa='$cod_mesa'";
			$verificacion = mysqli_query($conexion,$sql);
		}
		if(isset($_POST['cod_espacio']))
		{
			$sql="UPDATE `espacios` SET `cod_cliente`='$cod_cliente' WHERE codigo='$cod_espacio'";
			$verificacion = mysqli_query($conexion,$sql);
		}
	}
	else
		$verificacion = 'No se encontró cliente con Cédula/NIT -> '.$busqueda;
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>

