<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();
$conexion = $obj->conexion();

$sql = "SELECT `codigo`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_registro`, `cod_cliente`, `pagos`, `fecha_llegada`, `descuentos`, `code` FROM `reservas` ORDER BY FIELD(estado,'PENDIENTE','CANCELADA','PROCESADA')";
$result = mysqli_query($conexion, $sql);

$nombre_tabla = 'Reservas';
?>
<!-- Tabla Reservas -->
<div class="card">
	<div class="card-body p-2">
		<div class="d-sm-flex align-items-center row m-0 mb-2">
			<div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6">
				<h4 class="card-title"><?php echo $nombre_tabla; ?></h4>
			</div>
			<div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6 text-right">
				<button class="btn btn-sm btn-outline-primary ml-auto btn-round" onclick="agregar_reserva();">
					<i class="icon-plus btn-icon-prepend"></i>Nueva Reserva
				</button>
			</div>
		</div>
		<table class="table text-dark table-sm" id="tabla_reservas" width="100%">
			<thead>
				<tr class="text-center">
					<th class="p-1" width="40px">#</th>
					<th class="p-1" width="80px">Cod</th>
					<th class="p-1">Cliente</th>
					<th class="p-1" width="100px">CODE</th>
					<th class="p-1" width="100px">Estado</th>
					<th class="p-1" width="200px">Fecha Llegada</th>
					<th class="p-1" width="40px"></th>
				</tr>
			</thead>
			<tbody class="overflow-auto">
				<?php
				$num_item = 1;
				while ($mostrar = mysqli_fetch_row($result)) {
					$cod_reserva = $mostrar[0];
					$nombre = $mostrar[1];
					$descripcion = $mostrar[2];
					$productos = $mostrar[3];
					$estado = $mostrar[4];
					$fecha_registro = $mostrar[5];
					$code = $mostrar[10];

					$cliente = 'Sin Cliente';
					$cod_cliente = $mostrar[6];

					$sql_cliente = "SELECT `nombre`, `telefono` FROM `clientes` WHERE `codigo` = '$cod_cliente'";
					$result_cliente = mysqli_query($conexion, $sql_cliente);
					$mostrar_cliente = mysqli_fetch_row($result_cliente);
					if ($mostrar_cliente != null)
						$cliente = $mostrar_cliente[0] . ' [' . $mostrar_cliente[1] . ']';

					$pagos = $mostrar[7];
					$fecha_llegada = 'Sin fecha';
					if ($mostrar[8] != null)
						$fecha_llegada = date('d-m-Y G:i A', strtotime($mostrar[8]));
					$descuentos = $mostrar[9];
					$code = $mostrar[10];

					if ($estado == 'PENDIENTE')
						$bg_estado = 'bg-danger text-white';
					if ($estado == 'CANCELADA')
						$bg_estado = 'bg-info text-white';
					if ($estado == 'PROCESADA')
						$bg_estado = 'bg-success text-white';

				?>
					<tr>
						<td class="text-center p-1"><?php echo $num_item ?></td>
						<td class="text-center"><?php echo str_pad($cod_reserva, 4, "0", STR_PAD_LEFT); ?></td>
						<td class="p-1 text-truncate"><b><?php echo $cliente ?></b></td>
						<td class="text-center p-1"><b class="fs-2"><?php echo $code ?></b></td>
						<td class="text-center p-1 <?php echo $bg_estado ?>"><b><?php echo $estado ?></b></strong></td>
						<td class="text-center p-1">
							<?php echo $fecha_llegada ?></strong>
							<p class="mb-0" id="tiempo_<?php echo $cod_reserva ?>"></p>
						</td>
						<td class="text-center p-1">
							<button class="btn btn-outline-primary btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Ver" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_modal_reserva').load('paginas/detalles/detalles_reserva.php/?cod_reserva=<?php echo $cod_reserva ?>', function(){cerrar_loader();});">
								<span class="fa fa-search"></span>
							</button>
						</td>
					</tr>
					<?php
					if ($mostrar[8] != null) {
						$fecha_llegada = date("M d Y G:i:s",strtotime($mostrar[8]));
					?>
						<script type="text/javascript">
							countdown('<?php echo $fecha_llegada ?> GMT-0500', 'tiempo_<?php echo $cod_reserva ?>', 'FECHA PASADA');
						</script>
					<?php
					}
					?>
				<?php
					$num_item++;
				}
				?>
			</tbody>
		</table>
	</div>
</div>

<!-- Modal Ver-->
<div class="modal fade" id="Modal_Ver" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="div_modal_reserva">
		</div>
	</div>
</div>

<!-- Modal detalles de factura-->
<div class="modal fade" id="Modal_ticket" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content" id="contenedor_pdf"></div>
	</div>
</div>

<!-- #END# Tabla Reservas -->
<script type="text/javascript">
	$(document).ready(function() {
		$('#tabla_reservas').DataTable({
			responsive: true,
			columns: [{
					responsivePriority: 0
				},
				{
					responsivePriority: 3
				},
				{
					responsivePriority: 1
				},
				{
					responsivePriority: 4
				},
				{
					responsivePriority: 5
				}, {
					responsivePriority: 6
				},
				{
					responsivePriority: 2
				}
			]
		});
	});


	function agregar_reserva() {
		document.getElementById('div_loader').style.display = 'block';
		$.ajax({
			type: "POST",
			url: "procesos/agregar_reserva.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				if (datos['consulta'] == 1) {
					w_alert({
						titulo: 'Reserva creada Correctamente',
						tipo: 'success'
					});
					click_item('reservas');
				} else
					w_alert({
						titulo: datos['consulta'],
						tipo: 'danger'
					});

				cerrar_loader();
			}
		});
	}
</script>