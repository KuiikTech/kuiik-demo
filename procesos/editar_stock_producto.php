<?php 
date_default_timezone_set('America/Bogota');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();

$fecha_h=date('Y-m-d G:i:s');

$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];
	require_once "../clases/permisos.php";
	$obj_permisos = new permisos();
	$acceso = $obj_permisos->buscar_permiso($usuario,'Por Cobrar','COBRAR');

	if($acceso == 'SI')
	{
		$verificacion = 1;

		$cod_producto = $_POST['cod_producto'];
		$pos = $_POST['pos'];
		$bodega = $_POST['bodega'];
		$edit_valor_venta=str_replace('.', '', $_POST['edit_valor_venta']);
		$edit_valor_venta_mayor=str_replace('.', '', $_POST['edit_valor_venta_mayor']);
		$edit_costo=str_replace('.', '', $_POST['edit_costo']);
		$edit_proveedor = $_POST['edit_proveedor'];
		$edit_marca = $_POST['edit_marca'];

		if ($edit_marca == '')
			$verificacion = 'Escriba la marca del producto';
		if ($edit_proveedor == '')
			$verificacion = 'Escriba el nombre del proveedor';
		if ($edit_costo == '')
			$verificacion = 'Ingrese el costo del producto';
		if ($edit_valor_venta == '')
			$verificacion = 'Ingrese el valor de venta del producto';

		if ($verificacion == 1)
		{
			$sql = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo = '$cod_producto'";
			$result=mysqli_query($conexion,$sql);
			$mostrar=mysqli_fetch_row($result);

			$pos_mov = 1;

			$inventario = array();
			if($bodega == 'Principal')
			{
				$bodega_inventario = 'inventario';
				if ($mostrar[3] != '')
					$inventario = json_decode($mostrar[3],true);
			}
			else if($bodega == 'PDV_1')
			{
				$bodega_inventario = 'inventario_1';
				if ($mostrar[6] != '')
					$inventario = json_decode($mostrar[6],true);
			}
			else if($bodega == 'PDV_2')
			{
				$bodega_inventario = 'inventario_2';
				if ($mostrar[7] != '')
					$inventario = json_decode($mostrar[7],true);
			}

			if (!isset($inventario[$pos]['valor_venta_mayor']))
				$inventario[$pos]['valor_venta_mayor'] = 0;

			$pos_mov += count($inventario[$pos]['movimientos']);

			$info_edit = 'C:$'.number_format($inventario[$pos]['costo'],0,'.','.').' V:$'.number_format($inventario[$pos]['valor_venta'],0,'.','.').' VM:$'.number_format($inventario[$pos]['valor_venta_mayor'],0,'.','.');

			$inventario[$pos]['movimientos'][$pos_mov] = array(
				'Tipo' => 'Edición',
				'Cant' => '0',
				'creador' => $usuario,
				'Observaciones' => $info_edit,
				'fecha' => $fecha_h );

			$inventario[$pos] = array(
				'costo' => $edit_costo, 
				'valor_venta' => $edit_valor_venta, 
				'valor_venta_mayor' => $edit_valor_venta_mayor, 
				'creador' => $inventario[$pos]['creador'], 
				'cant_inicial' => $inventario[$pos]['cant_inicial'], 
				'stock' => $inventario[$pos]['stock'], 
				'fecha_registro' => $inventario[$pos]['fecha_registro'], 
				'marca' => $edit_marca, 
				'proveedor' => $edit_proveedor, 
				'movimientos' => $inventario[$pos]['movimientos']);

			if($verificacion == 1)
			{
				$inventario = json_encode($inventario,JSON_UNESCAPED_UNICODE);
				$sql="UPDATE `productos` SET 
				`$bodega_inventario`='$inventario'
				WHERE codigo='$cod_producto'";

				$verificacion = mysqli_query($conexion,$sql);
			}

		}
	}
	else
		$verificacion = 'Usted no tiene permisos para editar el stock de productos';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
