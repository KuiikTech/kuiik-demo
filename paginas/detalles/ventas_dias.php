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

$total_x_dias = array();
$total_ventas = 0;
$fecha_row = '';

$sql = "SELECT `codigo`, `cliente`, `productos`, `pago`, `fecha`, `cobrador` FROM `ventas` WHERE fecha BETWEEN '$fecha_inicial' AND '$fecha_final' order by fecha ASC";
$result = mysqli_query($conexion, $sql);

while ($mostrar = mysqli_fetch_row($result)) {
	$fecha_row = date('Y-m-d', strtotime($mostrar[4]));;
	$total = 0;
	$productos_venta = json_decode($mostrar[2], true);
	foreach ($productos_venta as $i => $producto) {
		if (isset($producto['valor_unitario']) && isset($producto['cant']))
			$total += $producto['valor_unitario'] * $producto['cant'];
	}

	$total_ventas += $total;

	if (isset($total_x_dias[$fecha_row]))
		$total_x_dias[$fecha_row]['total'] += $total;
	else
		$total_x_dias[$fecha_row]['total'] = $total;
}

$nombre_tabla = 'Total por Días entre ' . $_GET['fecha_inicial'] . ' y ' . $_GET['fecha_final'];

?>
<!-- Tabla Productos -->
<div class="card">
	<div class="card-body">
		<div class="d-sm-flex align-items-center mb-4">
			<h4 class="card-title text-center"><?php echo $nombre_tabla; ?></h4>
		</div>
		<div class="p-1">
			<table class="table text-dark table-sm Data_Table" id="tabla_ventas_dias" width="100%">
				<thead>
					<tr class="text-center">
						<th>Cod</th>
						<th>Fecha</th>
						<th>Total</th>
						<th></th>
					</tr>
				</thead>
				<tbody class="overflow-auto">
					<?php
					$num_item = 1;
					foreach ($total_x_dias as $j => $dia) {
					?>
						<tr>
							<td class="text-center"><?php echo $num_item ?></td>
							<td class="text-center"><?php echo $j ?></td>
							<td class="text-right"><strong>$<?php echo number_format($dia['total'], 0, '.', '.') ?></strong></td>
							<td></td>
						</tr>
					<?php
						$num_item++;
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
				}
			]
		});
	});

	$(".select2").select2();

	function generar_factura(cod_venta) {
		document.getElementById('div_loader').style.display = 'block';
		$.ajax({
			type: "POST",
			data: "cod_venta=" + cod_venta,
			url: "procesos/generar_factura.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				if (datos['consulta'] == 1) {
					w_alert({
						titulo: 'Factura Generada Correctamente',
						tipo: 'success'
					});
					$("#Modal_venta").modal('toggle');
					$('.modal-backdrop').remove();
					document.querySelector("body").style.overflow = "auto";
					cerrar_loader();
					click_item('facturas');
				} else {
					w_alert({
						titulo: datos['consulta'],
						tipo: 'danger'
					});
					if (datos['consulta'] == 'Reload') {
						document.getElementById('div_login').style.display = 'block';
						cerrar_loader();

					}
					cerrar_loader()
				}
			}
		});
	}
</script>