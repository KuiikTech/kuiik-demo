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

	$sql = "SELECT `codigo`, `productos`, `proveedor`, `creador`, `estado`, `fecha_registro` FROM `compras` WHERE estado = 'EN PROCESO' order by fecha_registro DESC";
	$result = mysqli_query($conexion, $sql);
	$mostrar = mysqli_fetch_row($result);

	if ($mostrar != NULL) {
		if ($mostrar[1] != '')
			$productos_compra = json_decode($mostrar[1], true);
		else
			$verificacion = 'No existen productos agregados';

		if ($mostrar[2] != '')
			$proveedor = json_decode($mostrar[2], true);
		else
			$verificacion = 'Seleccione un proveedor';
	} else
		$verificacion = 'No se encontró una compra en proceso';

	if ($verificacion == 1) {
		$cod_compra = $mostrar[0];
		$nombre_proveedor = $proveedor['nombre'];
		$proveedor_compra = $proveedor['codigo'];

		$proveedor_sql = json_encode($proveedor, JSON_UNESCAPED_UNICODE);
		$productos_compra_sql = json_encode($productos_compra, JSON_UNESCAPED_UNICODE);
		$sql = "UPDATE `compras` SET 
		`estado`='',
		`fecha_registro`='$fecha_h'
		WHERE codigo='$cod_compra'";

		$verificacion = mysqli_query($conexion, $sql);

		if ($verificacion == 1) {
			foreach ($productos_compra as $i => $item) {
				$cod_producto = $item['codigo'];
				$descripcion = $item['descripcion'];
				$categoria = $item['categoria'];
				$valor_venta = str_replace('.', '', $item['valor_venta']);
				$costo = str_replace('.', '', $item['costo']);
				$cant = $item['cant'];
				$sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode`, `movimientos`, `especial` FROM `productos` WHERE codigo='$cod_producto'";
				$result_producto = mysqli_query($conexion, $sql_producto);
				$mostrar_producto = mysqli_fetch_row($result_producto);

				$nombre_producto = $mostrar_producto[1];
				$cod_categoria = $mostrar_producto[5];
				$inventario = $mostrar_producto[4];

				$movimientos = array();
				if ($mostrar_producto[12] != '')
					$movimientos = json_decode($mostrar_producto[12], true);
				$pos = count($movimientos) + 1;

				$movimientos[$pos] = array(
					'Tipo' => 'Ingreso',
					'Cant' => '+' . $cant,
					'creador' => $usuario,
					'Observaciones' => 'Compra # ' . $cod_compra,
					'fecha' => $fecha_h
				);

				$movimientos = json_encode($movimientos, JSON_UNESCAPED_UNICODE);
				$inventario += $cant;

				$sql = "UPDATE `productos` SET 
				`inventario`='$inventario',
				`movimientos`='$movimientos'
				WHERE codigo='$cod_producto'";

				$verificacion = mysqli_query($conexion, $sql);
				if ($verificacion != 1) {
					$info_registro = array(
						'cod_compra' => $cod_compra,
						'cod_producto' => $cod_producto
					);

					$info_registro = array(
						'Tipo' => 'Procesar Compra',
						'Información' => $info_registro
					);

					$info_registro = json_encode($info_registro, JSON_UNESCAPED_UNICODE);

					$sql = "INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
						'$info_registro',
						'$usuario',
						'$fecha_h')";
					$verificacion_2 = mysqli_query($conexion, $sql);
				}
			}

			$sql = "INSERT INTO `compras`(`productos`, `proveedor`, `creador`, `fecha_registro`, `estado`) VALUES (
				'',
				'',
				'$usuario',
				'$fecha_h',
				'EN PROCESO')";

			$verificacion = mysqli_query($conexion, $sql);
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