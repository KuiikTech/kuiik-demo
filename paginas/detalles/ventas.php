<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();
$conexion = $obj->conexion();

$fecha_inicial = $_GET['fecha_inicial'] . ' 00:00:00';
$fecha_final = $_GET['fecha_final'] . ' 23:59:59';

$sql = "SELECT `codigo`, `cliente`, `productos`, `pago`, `fecha`, `cobrador`, `estado` FROM `ventas` WHERE fecha BETWEEN '$fecha_inicial' AND '$fecha_final' order by fecha ASC";
$result = mysqli_query($conexion, $sql);

$nombre_tabla = 'Ventas entre ' . $_GET['fecha_inicial'] . ' y ' . $_GET['fecha_final'];

$total_ventas = 0;
?>
<!-- Tabla Productos -->
<div class="card">
	<div class="card-body">
		<div class="d-sm-flex align-items-center mb-4">
			<h4 class="card-title text-center"><?php echo $nombre_tabla; ?></h4>
		</div>
		<div class="p-1">
			<table class="table text-dark table-sm Data_Table" id="tabla_ventas" width="100%">
				<thead>
					<tr class="text-center">
						<th>Cod</th>
						<th>Cliente</th>
						<th>Fecha</th>
						<th>Total</th>
						<th>Creador</th>
						<th></th>
					</tr>
				</thead>
				<tbody class="overflow-auto">
					<?php
					while ($mostrar = mysqli_fetch_row($result)) {
						$cod_venta = $mostrar[0];

						$total = 0;
						$cliente = json_decode($mostrar[1], true);
						$pagos = json_decode($mostrar[1], true);

						$productos_venta = json_decode($mostrar[2], true);
						foreach ($productos_venta as $i => $producto)
							if (isset($producto['valor_unitario']) && isset($producto['cant']))
								$total += $producto['valor_unitario'] * $producto['cant'];

						$cobrador = $mostrar[5];

						$sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$cobrador'";
						$result_e = mysqli_query($conexion, $sql_e);
						$ver_e = mysqli_fetch_row($result_e);

						$cobrador = $ver_e[0];

						$fecha_venta = strftime("%A, %e %b %Y", strtotime($mostrar[4]));
						$fecha_venta = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_venta));

						$fecha_venta .= date(' | h:i A', strtotime($mostrar[4]));

						$estado = $mostrar[6];

						$bg_estado = '';
						if ($estado == 'ANULADA') {
							$bg_estado = 'bg-danger-light';
							$total = 0;
						}
					?>
						<tr>
							<td class="text-center <?php echo $bg_estado ?>"><?php echo str_pad($mostrar[0], 3, "0", STR_PAD_LEFT) ?></td>
							<td><?php echo $cliente['nombre'] ?></td>
							<td><?php echo $fecha_venta ?></td>
							<td class="text-right"><strong>$<?php echo number_format($total, 0, '.', '.') ?></strong></td>
							<td class="text-center"><?php echo $cobrador ?></td>
							<td class="text-center">
								<button class="btn btn-sm btn-outline-primary btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_venta" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_modal_venta').load('paginas/detalles/detalles_venta.php/?cod_venta=<?php echo $cod_venta ?>', function(){cerrar_loader();});">
									<span class="fa fa-search"></span>
								</button>
							</td>
						</tr>
					<?php
						$total_ventas += $total;
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
	<div class="modal-dialog" role="document">
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
					responsivePriority: 4
				},
				{
					responsivePriority: 5
				},
				{
					responsivePriority: 6
				},
				{
					responsivePriority: 3
				}
			]
		});
	});

	$(".select2").select2({
		dropdownParent: $('#Modal_venta .modal-body')
	});
</script>