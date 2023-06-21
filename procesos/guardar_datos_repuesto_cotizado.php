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

	$item = $_POST['item'];

	$item = $_POST['item'];
	$valor_venta = str_replace('.', '', $_POST['valor_venta']);
	$valor_venta_mayor = str_replace('.', '', $_POST['valor_venta_mayor']);
	$costo = str_replace('.', '', $_POST['costo']);
	$marca = $_POST['marca'];

	$sql = "SELECT `codigo`, `producto`, `proveedor`, `creador`, `estado`, `fecha_registro` FROM `repuestos_cotizados` WHERE estado = 'EN PROCESO' order by fecha_registro DESC";
	$result=mysqli_query($conexion,$sql);
	$mostrar=mysqli_fetch_row($result);

	if($mostrar != NULL)
	{
		$cod_repuesto_cotizado = $mostrar[0];
		if($mostrar[1]!= '')
		{
			$productos_repuesto_cotizado = json_decode($mostrar[1],true);
			$pos = count($productos_repuesto_cotizado)+1;

			foreach ($productos_repuesto_cotizado as $i => $producto)
			{
				if($item == $i)
				{
					$productos_repuesto_cotizado[$i]['marca'] = $marca;
					$productos_repuesto_cotizado[$i]['valor_venta'] = $valor_venta;
					$productos_repuesto_cotizado[$i]['valor_venta_mayor'] = $valor_venta_mayor;
					$productos_repuesto_cotizado[$i]['costo'] = $costo;
				}
			}
			
			$productos_repuesto_cotizado = json_encode($productos_repuesto_cotizado,JSON_UNESCAPED_UNICODE);

			$sql="UPDATE `repuestos_cotizados` SET 
			`producto`='$productos_repuesto_cotizado'
			WHERE codigo='$cod_repuesto_cotizado'";

			$verificacion = mysqli_query($conexion,$sql);
		}
		else
			$verificacion = 'No existen productos agregados';
	}
	else
		$verificacion = 'No se encontró un repuesto cotizado en proceso';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
