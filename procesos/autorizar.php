<?php 
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();

$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();

$verificacion = 'Recargue la pagina e intente de nuevo';

if (isset($_POST['cod_compra']))
{
	$verificacion = $obj->autorizar_compra($_POST['cod_compra']);

	if($verificacion == 1)
	{
		$cod_compra = $_POST['cod_compra'];
		$sql = "SELECT `codigo`, `producto`, `cantidad`, `creador`, `autorizador`, `fecha_registro`, `fecha_autorización`, `valor` FROM `compras` WHERE codigo = '$cod_compra'";
		$result=mysqli_query($conexion,$sql);
		$mostrar=mysqli_fetch_row($result);

		$cod_producto = $mostrar[1];

		$sql_producto = "SELECT `cod_producto`, `descripción`, `tipo`, `valor`, `inventario`, `cod_categoria`, `fecha_modificacion`, `tipo` FROM `productos` WHERE cod_producto='$cod_producto'";
		$result_producto=mysqli_query($conexion,$sql_producto);
		$mostrar_producto=mysqli_fetch_row($result_producto);

		$descripcion_gasto = 'Compra de '.$mostrar_producto[1];

		$valor_gasto = $mostrar[7];
		$datos=array(
			$descripcion_gasto,
			$valor_gasto
		);

		$verificacion = $obj->agregar_gasto($datos);
	}
}

if (isset($_POST['cod_mesa']))
{
	$datos=array(
		$_POST['cod_mesa'],
		$_POST['num_item'],
		$_POST['estado']
	);
	$verificacion = $obj->autorizar_descuento($datos);
}

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);

?>