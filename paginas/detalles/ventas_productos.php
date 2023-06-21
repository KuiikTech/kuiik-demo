<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$fecha_inicial = $_GET['fecha_inicial'] . ' 00:00:00';
$fecha_final = $_GET['fecha_final'] . ' 23:59:59';

$productos_totales = array();
$total_ventas = 0;

$sql = "SELECT `codigo`, `cliente`, `productos`, `pago`, `fecha`, `cobrador` FROM `ventas` WHERE fecha BETWEEN '$fecha_inicial' AND '$fecha_final' order by fecha ASC";
$result = mysqli_query($conexion, $sql);

while ($mostrar = mysqli_fetch_row($result)) {
	$total = 0;
	$productos_venta = json_decode($mostrar[2], true);
	foreach ($productos_venta as $i => $producto) {
		if (isset($producto['valor_unitario']) && isset($producto['cant'])) {
			$total += $producto['valor_unitario'] * $producto['cant'];
			if (isset($productos_totales[$producto['codigo']])) {
				$productos_totales[$producto['codigo']]['cant'] += $producto['cant'];
				$productos_totales[$producto['codigo']]['total'] += $producto['valor_unitario'] * $producto['cant'];
			} else {
				$productos_totales[$producto['codigo']]['cant'] = $producto['cant'];
				$productos_totales[$producto['codigo']]['descripcion'] = $producto['descripcion'];
				$productos_totales[$producto['codigo']]['total'] = $producto['valor_unitario'] * $producto['cant'];
			}
		}
	}
	$total_ventas += $total;
}

$nombre_tabla = 'Productos Vendidos entre ' . $_GET['fecha_inicial'] . ' y ' . $_GET['fecha_final'];

?>
<!-- Tabla Productos -->
<div class="card">
	<div class="card-body">
		<div class="d-sm-flex align-items-center mb-4">
			<h4 class="card-title text-center"><?php echo $nombre_tabla; ?></h4>
		</div>
		<div class="p-1">
			<table class="table text-dark table-sm Data_Table" id="tabla_ventas_productos" width="100%">
				<thead>
					<tr class="text-center">
						<th>Cod</th>
						<th>Producto</th>
						<th>Cantidad</th>
						<th>Total</th>
						<th></th>
					</tr>
				</thead>
				<tbody class="overflow-auto">
					<?php
					foreach ($productos_totales as $j => $producto) {
					?>
						<tr>
							<td class="text-center"><?php echo $j ?></td>
							<td><?php echo $producto['descripcion'] ?></td>
							<td class="text-center"><?php echo $producto['cant'] ?></td>
							<td class="text-right"><strong>$<?php echo number_format($producto['total'], 0, '.', '.') ?></strong></td>
							<td></td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>
		<div class="row float-right mt-3">
			<h3>Total ventas: $<?php echo number_format($total_ventas, 0, '.', '.') ?></h3>
		</div>
	</div>
</div>

<!-- Modal detalles de venta-->
<div class="modal fade" id="Modal_venta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content" id="div_modal_venta"></div>
	</div>
</div>

<!-- #END# Tabla Productos -->
<script type="text/javascript">
	$(document).ready(function() {
		$('.Data_Table').DataTable({
			responsive: true,
			columns: [{
					responsivePriority: 1
				},
				{
					responsivePriority: 2
				},
				{
					responsivePriority: 3
				},
				{
					responsivePriority: 4
				},
				{
					responsivePriority: 6
				}
			]
		});
	});

	$(".select2").select2();
</script>