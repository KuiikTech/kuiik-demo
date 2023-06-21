<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$sql = "SELECT `codigo`, `descripcion`, `valor`, `creador`, `fecha_registro`, `estado`, `aprobo`, `fecha_aprobacion`, `metodo_pago` FROM `caja_mayor` ORDER BY fecha_registro DESC";
$result = mysqli_query($conexion, $sql);

$nombre_tabla = 'Caja Mayor';
?>
<!-- Tabla Productos -->
<div class="card">
	<div class="card-body p-2">
		<div class="d-sm-flex align-items-center row m-0 mb-2">
			<div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6">
				<h4 class="card-title"><?php echo $nombre_tabla; ?></h4>
			</div>
		</div>
		<table class="table text-dark table-sm Data_Table" id="tabla_caja" width="100%">
			<thead>
				<tr class="text-center">
					<th class="p-1" width="20px">#</th>
					<th class="p-1" width="20px">Cod</th>
					<th class="p-1">Descripción</th>
					<th class="p-1">Valor</th>
					<th class="p-1">Método</th>
					<th class="p-1">Estado</th>
					<th class="p-1">Creó</th>
					<th class="p-1">Aprobó/Denegó</th>
					<th class="p-1" width="20px"></th>
				</tr>
			</thead>
			<tbody class="overflow-auto">
				<?php
				$num_item = 1;
				$total_caja = 0;
				$metodos = array();
				while ($mostrar = mysqli_fetch_row($result)) {
					$codigo = $mostrar[0];
					$descripcion = $mostrar[1];
					$valor = $mostrar[2];
					$metodo = $mostrar[8];

					$aprobo = '---';

					$fecha_registro = strftime("%A, %e %b %Y", strtotime($mostrar[4]));
					$fecha_registro = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_registro));
					$fecha_registro .= date(' h:i A', strtotime($mostrar[4]));

					if ($mostrar[7] != NULL) {
						$fecha_aprobacion = strftime("%A, %e %b %Y", strtotime($mostrar[7]));
						$fecha_aprobacion = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_aprobacion));
						$fecha_aprobacion .= date(' h:i A', strtotime($mostrar[7]));
					} else
						$fecha_aprobacion = '---';

					$estado = $mostrar[5];

					$creador = $mostrar[3];

					$text_valor = '';

					$sql_e = "SELECT nombre, apellido, foto FROM usuarios WHERE codigo = '$creador'";
					$result_e = mysqli_query($conexion, $sql_e);
					$ver_e = mysqli_fetch_row($result_e);

					$creador = $ver_e[0] . ' ' . $ver_e[1];

					if ($estado == 'APROBADO') {
						if ($valor > 0)
							$text_valor = 'text-success';
						else
							$text_valor = 'text-danger';

						$aprobo = $mostrar[3];

						$sql_e = "SELECT nombre, apellido, foto FROM usuarios WHERE codigo = '$aprobo'";
						$result_e = mysqli_query($conexion, $sql_e);
						$ver_e = mysqli_fetch_row($result_e);

						$aprobo = $ver_e[0] . ' ' . $ver_e[1];

						if (isset($metodos[$metodo]))
							$metodos[$metodo] += $valor;
						else
							$metodos[$metodo] = $valor;
					}

					if ($estado == 'DENEGADO') {
						$text_valor = 'text-decoration-line-through';

						$aprobo = $mostrar[3];

						$sql_e = "SELECT nombre, apellido, foto FROM usuarios WHERE codigo = '$aprobo'";
						$result_e = mysqli_query($conexion, $sql_e);
						$ver_e = mysqli_fetch_row($result_e);

						$aprobo = $ver_e[0] . ' ' . $ver_e[1];
					}
					if ($estado == 'PENDIENTE')
						$text_valor = 'text-info';
				?>
					<tr>
						<td class="text-center p-1"><?php echo $num_item ?></td>
						<td class="text-center p-1"><?php echo str_pad($codigo, 3, "0", STR_PAD_LEFT) ?></td>
						<td class="p-1"><?php echo $descripcion ?></td>
						<td class="text-right p-1 <?php echo $text_valor ?>"><b>$<?php echo number_format($valor, 0, '.', '.') ?></b></td>
						<td class="text-center p-1"><b><?php echo $metodo ?></b></td>
						<td class="text-center p-1"><b><?php echo $estado ?></b></td>
						<td class="text-center p-1 lh-1">
							<?php echo $creador ?>
							<p class="m-0 p-0"><small><?php echo $fecha_registro ?></small></p>
						</td>
						<td class="text-center p-1 lh-1">
							<?php echo $aprobo ?>
							<br>
							<p class="m-0 p-0"><small><?php echo $fecha_aprobacion ?></small></p>
						</td>
						<td class="text-center p-1" width="50px">
							<?php
							if ($estado == 'PENDIENTE') {
							?>
								<button class="btn btn-sm btn-outline-info btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Aprobar" onclick="$('#cod_movimiento_caja').val(<?php echo $mostrar[0] ?>);">
									APROBAR
								</button>
								<button class="btn btn-sm btn-outline-danger btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Denegar" onclick="$('#cod_movimiento_caja_d').val(<?php echo $mostrar[0] ?>);">
									DENEGAR
								</button>
							<?php
							}
							?>
						</td>
					</tr>
				<?php
					$num_item++;
				}
				?>
			</tbody>
		</table>

		<div class="row m-0 p-0 pt-3 text-left">
			<hr class="m-1">
			<h2 class="text-center m-0 p-0">Total en caja</h2>
			<hr class="m-1">
			<?php
			foreach ($metodos as $m => $valor) {
			?>
				<h4><?php echo '<b>' . $m . '</b>: $' . number_format($valor, 0, '.', '.') ?></h4>
			<?php
			}
			?>
		</div>
	</div>
</div>
<!-- #END# Tabla Productos -->

<!-- Modal Aprobar movimiento-->
<div class="modal fade" id="Modal_Aprobar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h5 class="modal-title">Seguro desea APROBAR este movimiento?</h5>
			</div>
			<div class="modal-body">
				<input type="number" name="cod_movimiento_caja" id="cod_movimiento_caja" hidden="">
				<div class="row">
					<button type="button" class="btn btn-sm btn-secondary btn-round col-6 px-2" data-bs-dismiss="modal" id="close_Modal_Aprobar">NO</button>
					<button type="button" class="btn btn-sm btn-outline-primary btn-round col-6 px-2" id="btn_aprobar_movimiento">SI, Aprobar</button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Denegar movimiento-->
<div class="modal fade" id="Modal_Denegar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h5 class="modal-title">Seguro desea DENEGAR este movimiento?</h5>
			</div>
			<div class="modal-body">
				<input type="number" name="cod_movimiento_caja_d" id="cod_movimiento_caja_d" hidden="">
				<div class="row">
					<button type="button" class="btn btn-sm btn-secondary btn-round col-6 px-2" data-bs-dismiss="modal" id="close_Modal_Denegar">NO</button>
					<button type="button" class="btn btn-sm btn-outline-primary btn-round col-6 px-2" id="btn_denegar_movimiento">SI, Denegar</button>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('.Data_Table').DataTable({
			responsive: true,
			columns: [{
				responsivePriority: 1
			}, {
				responsivePriority: 4
			}, {
				responsivePriority: 2
			}, {
				responsivePriority: 7
			}, {
				responsivePriority: 5
			}, {
				responsivePriority: 6
			}, {
				responsivePriority: 8
			}, {
				responsivePriority: 9
			}, {
				responsivePriority: 3
			}]
		});
	});

	$('#btn_aprobar_movimiento').click(function() {
		document.getElementById('div_loader').style.display = 'block';
		cod_mivimiento = document.getElementById("cod_movimiento_caja").value;
		$.ajax({
			type: "POST",
			data: "cod_mivimiento=" + cod_mivimiento + "&estado=APROBADO",
			url: "procesos/actualizar_movimiento.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				if (datos['consulta'] == 1) {
					w_alert({
						titulo: 'Movimiento aprobado Correctamente',
						tipo: 'success'
					});
					document.getElementById('div_loader').style.display = 'block';
					$('#div_tabla_caja').load('tablas/caja_mayor.php', function() {
						cerrar_loader();
					});
					$("#close_Modal_Aprobar").click();
				} else
					w_alert({
						titulo: datos['consulta'],
						tipo: 'danger'
					});
				if (datos['consulta'] == 'Reload') {
					document.getElementById('div_login').style.display = 'block';
					cerrar_loader();
					
				}

				cerrar_loader();
			}
		});
	});

	$('#btn_denegar_movimiento').click(function() {
		document.getElementById('div_loader').style.display = 'block';
		cod_mivimiento = document.getElementById("cod_movimiento_caja_d").value;
		$.ajax({
			type: "POST",
			data: "cod_mivimiento=" + cod_mivimiento + "&estado=DENEGADO",
			url: "procesos/actualizar_movimiento.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				if (datos['consulta'] == 1) {
					w_alert({
						titulo: 'Movimiento denegado Correctamente',
						tipo: 'success'
					});
					document.getElementById('div_loader').style.display = 'block';
					$('#div_tabla_caja').load('tablas/caja_mayor.php', function() {
						cerrar_loader();
					});
					$("#close_Modal_Denegar").click();
				} else
					w_alert({
						titulo: datos['consulta'],
						tipo: 'danger'
					});
				if (datos['consulta'] == 'Reload') {
					document.getElementById('div_login').style.display = 'block';
					cerrar_loader();
					
				}

				cerrar_loader();
			}
		});
	});
</script>