<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

session_set_cookie_params(7 * 24 * 60 * 60);
session_start();

$busqueda = str_replace("***", "%", $_GET['consulta']);
$consulta = "WHERE descripcion LIKE '%$busqueda%' AND estado = 'DISPONIBLE' order by descripcion ASC";

$sql_productos = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode`, `movimientos`, `especial` FROM `productos` $consulta";
$result_productos = mysqli_query($conexion, $sql_productos);

?>
<div class="contendor_productos p-1">
	<?php
	$num_item = 0;
	while ($mostrar_productos = mysqli_fetch_row($result_productos)) {
		$nombre_producto = $mostrar_productos[1];
		$precio_producto = number_format($mostrar_productos[3], 0, '.', '.');
		if ($mostrar_productos[9] == 'Producto')
			$inventario_producto = $mostrar_productos[4];
		else
			$inventario_producto = '';
		$cod_producto = $mostrar_productos[0];

		if ($inventario_producto < 0)
			$bg_inv = 'bg-danger';
		else
			$bg_inv = 'bg-primary';

	?>
		<span class="item p-1" onclick="agregar_producto_compra('<?php echo $cod_producto ?>')">
			<div class="btn btn-outline-secondary p-1" style="width: 100%; border-radius: 10px; text-align: left">
				<strong class="ml-2 row m-0 p-0 text-truncate w-100"><?php echo $nombre_producto ?></strong>
				<div class="row m-0 p-1 px-0">
					<span class="inventario_producto <?php echo $bg_inv ?> col-4 rounded-pill"><?php echo $inventario_producto ?></span>
					<strong class="text-right col-8 px-1 pr-0">$<?php echo $precio_producto ?></strong>
				</div>
			</div>
		</span>
	<?php
		$num_item++;
	}
	?>
</div>
<div class="row m-0 p-1">
	<?php
	if ($num_item == 0) {
	?>
		<div class="ml-3">No se han regitrado productos para esta categoría.</div>
	<?php
	}
	?>
</div>
<!---->