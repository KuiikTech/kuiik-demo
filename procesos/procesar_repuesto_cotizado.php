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

	$sql = "SELECT `codigo`, `producto`, `proveedor`, `creador`, `estado`, `fecha_registro` FROM `repuestos_cotizados` WHERE estado = 'EN PROCESO' order by fecha_registro DESC";
	$result=mysqli_query($conexion,$sql);
	$mostrar=mysqli_fetch_row($result);

	if($mostrar != NULL)
	{
		if($mostrar[1]!= '')
			$productos_repuesto_cotizado = json_decode($mostrar[1],true);
		else
			$verificacion = 'No existen productos agregados';

		if($mostrar[2]!= '')
			$proveedor = json_decode($mostrar[2],true);
		else
			$verificacion='Seleccione un proveedor';
	}
	else
		$verificacion = 'No se encontró una repuesto cotizado en proceso';

	if ($verificacion == 1)
	{
		$cod_repuesto_cotizado = $mostrar[0];
		$nombre_proveedor = $proveedor['nombre'];
		$proveedor_repuesto_cotizado = $proveedor['codigo'];

		$proveedor_sql = json_encode($proveedor,JSON_UNESCAPED_UNICODE);
		$productos_repuesto_cotizado_sql = json_encode($productos_repuesto_cotizado,JSON_UNESCAPED_UNICODE);
		$sql="UPDATE `repuestos_cotizados` SET 
		`estado`=''
		WHERE codigo='$cod_repuesto_cotizado'";

		$verificacion = mysqli_query($conexion,$sql);

		if($verificacion == 1)
		{
			$sql="INSERT INTO `repuestos_cotizados`(`producto`, `proveedor`, `creador`, `fecha_registro`, `estado`) VALUES (
				'',
				'',
				'$usuario',
				'$fecha_h',
				'EN PROCESO')";

			$verificacion = mysqli_query($conexion,$sql);
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