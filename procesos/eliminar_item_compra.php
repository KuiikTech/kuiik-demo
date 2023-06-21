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

	$num_item = $_POST['num_item'];

	$sql = "SELECT `codigo`, `productos`, `proveedor`, `creador`, `estado`, `fecha_registro` FROM `compras` WHERE estado = 'EN PROCESO' order by fecha_registro DESC";
	$result=mysqli_query($conexion,$sql);
	$mostrar=mysqli_fetch_row($result);

	if($mostrar != NULL)
	{
		if($mostrar[1]!= '')
		{
			$cod_compra = $mostrar[0];
			$productos_compra = json_decode($mostrar[1],true);
			unset($productos_compra[$num_item]);
			$pos = 1;
			foreach ($productos_compra as $i => $item)
			{
				$productos_compra_nuevos[$pos] = $item;
				$pos ++;
			}
			if($pos == 1)
				$productos_compra_nuevos = '';
			else
				$productos_compra_nuevos = json_encode($productos_compra_nuevos,JSON_UNESCAPED_UNICODE);

			$sql="UPDATE `compras` SET 
			`productos`='$productos_compra_nuevos'
			WHERE codigo='$cod_compra'";

			$verificacion = mysqli_query($conexion,$sql);
		}
		else
			$verificacion = 'No existen items agregados';
	}
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
