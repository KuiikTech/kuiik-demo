<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();


$cod_reserva = $_GET['cod_reserva'];

$consulta = '';

if (isset($_GET['cod_categoria'])) {
	$cod_categoria = $_GET['cod_categoria'];
	$consulta = "WHERE categoria='$cod_categoria' AND estado = 'DISPONIBLE' order by descripcion ASC";
} else {
	if (isset($_GET['consulta'])) {
		$busqueda = str_replace("***", "%", $_GET['consulta']);
		$consulta = "WHERE descripcion LIKE '%$busqueda%' AND estado = 'DISPONIBLE' order by descripcion ASC";
	} else {
		if (isset($_GET['consulta0']))
			$consulta = "WHERE codigo<0";
	}
}
$sql_productos = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` $consulta";
$result_productos = mysqli_query($conexion, $sql_productos);

?>
<div class="contendor_productos p-1" id="div_contenedor_productos">
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

		if ($inventario_producto <= 0)
			$bg_inv = 'bg-danger';
		else
			$bg_inv = 'bg-success';

		if ($mostrar_productos[9] == 'Preparación')
			$bg_inv = 'd-print-none';
	?>
		<span class="item p-1" onclick="modal_cantidad_r('<?php echo $cod_producto ?>','<?php echo $cod_reserva ?>')">
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
<div class="row m-0 p-1" id="div_cantidad" hidden>
	<div class="row m-0 p-1 text-center">
		<h5 class="text-center">Ingrese la cantidad de producto</h5>
	</div>
	<div class="row m-0 p-2">
		<input type="text" name="cod_reserva_pedido" id="cod_reserva_pedido" hidden="">
		<input type="text" name="cod_producto_pedido" id="cod_producto_pedido" hidden="">
		<div class="row m-0 p-2">
			<label class="text-center col p-1">
				<h5>Cantidad:</h5>
			</label>
			<input type="number" class="form-control form-control-sm col text-center" id="cantidad_pedido" name="cantidad_pedido" onFocus="this.select()" value="1">
		</div>
		<div class="row m-0 p-1 mt-4 d-flex justify-content-between">
			<div class="col-6 text-center">
				<button type="button" class="btn btn-outline-secondary btn-round" data-bs-dismiss="modal">CANCELAR</button>
			</div>
			<div class="col-6 text-center">
				<button type="button" class="btn btn-outline-primary btn-round" onclick="agregar_producto_r()" id="btn_agregar_producto_m">ACEPTAR</button>
			</div>
		</div>
	</div>
</div>
<?php
if ($num_item == 0) {
?>
	<div class="row m-0 p-1">
		<div class="ml-3">No se han regitrado productos para esta categoría.</div>
	</div>
<?php
}
?>

<script type="text/javascript">
	$('#cantidad_pedido').keypress(function(e) {
		if (e.keyCode == 13)
			$('#btn_agregar_producto_m').click();
	});

	function modal_cantidad_r(cod_producto, cod_reserva) {
		$('#cod_producto_pedido').val(cod_producto);
		$('#cod_reserva_pedido').val(cod_reserva);
		$('#cantidad_pedido').val(1);

		document.getElementById('div_cantidad').hidden = false;
		document.getElementById('div_contenedor_productos').hidden = true;

		setTimeout("document.getElementById('cantidad_pedido').focus();", 500);

	}
</script>