<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

session_set_cookie_params(7 * 24 * 60 * 60);
session_start();

if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];

	$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
	$result_e = mysqli_query($conexion, $sql_e);
	$ver_e = mysqli_fetch_row($result_e);

	$cedula = $ver_e[1];

	$nombre_usuario = $ver_e[2] . ' ' . $ver_e[3];
	$rol = $ver_e[5];

	$sql_vista_caja = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Vista caja'";
	$result_vista_caja = mysqli_query($conexion, $sql_vista_caja);
	$ver_vista_caja = mysqli_fetch_row($result_vista_caja);

	$vista_caja = $ver_vista_caja[2];

	$sql = "SELECT `codigo`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `creador`, `cajero`, `estado`, '1' AS caja  FROM `caja` ORDER BY FIELD(estado,'ABIERTA','CREADA','CERRADA'), fecha_registro DESC";
	$result = mysqli_query($conexion, $sql);

	$nombre_tabla = 'Caja';
?>
	<!-- Tabla Productos -->
	<div class="card">
		<div class="card-body p-2">
			<div class="d-sm-flex align-items-center row m-0 mb-2">
				<div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6">
					<h4 class="card-title"><?php echo $nombre_tabla; ?></h4>
				</div>
				<div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6 text-right">
					<button class="btn btn-sm btn-outline-primary ml-auto btn-round" id="btn_crear_caja">
						<i class="icon-plus btn-icon-prepend"></i>CREAR CAJA
					</button>
				</div>
			</div>
			<table class="table text-dark table-sm Data_Table" id="tabla_caja" width="100%">
				<thead>
					<tr class="text-center">
						<th width="20px">#</th>
						<th width="20px">Cod</th>
						<th width="20px">Caja</th>
						<th>Fecha apertura</th>
						<th>Fecha cierre</th>
						<th>Estado</th>
						<th>Base</th>
						<th width="20px"></th>
					</tr>
				</thead>
				<tbody class="overflow-auto">
					<?php
					$num_item = 1;
					while ($mostrar = mysqli_fetch_row($result)) {
						$caja = $mostrar[11];
						$cod_caja = $mostrar[0];
						$fecha_apertura = '---';
						$hora_apertura = '---';
						if ($mostrar[1] != NULL) {
							$fecha_apertura = strftime("%A,", strtotime($mostrar[1]));
							$fecha_apertura = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_apertura));
							$hora_apertura = date('d-m-Y h:i A', strtotime($mostrar[1]));
						}

						if ($mostrar[2] != NULL) {
							$fecha_cierre = strftime("%A, ", strtotime($mostrar[2]));
							$fecha_cierre = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_cierre));
							$hora_cierre = date('d-m-Y h:i A', strtotime($mostrar[2]));
						} else {
							$fecha_cierre = '---';
							$hora_cierre = '---';
						}

						$estado = $mostrar[10];
						$base = $mostrar[7];
					?>
						<tr>
							<td class="text-center p-1"><?php echo $num_item ?></td>
							<td class="text-center p-1"><?php echo str_pad($mostrar[0], 3, "0", STR_PAD_LEFT) ?></td>
							<td class="text-center p-1"><?php echo $caja ?></td>
							<td class="text-center p-0">
								<?php echo $fecha_apertura ?>
								<br>
								<?php echo $hora_apertura ?>
							</td>
							<td class="text-center p-1">
								<?php echo $fecha_cierre ?>
								<br>
								<?php echo $hora_cierre ?>
							</td>
							<td class="text-center p-1"><?php echo $estado ?></td>
							<td class="text-right p-1"><b>$<?php echo number_format($base, 0, '.', '.') ?></b></td>
							<td class="text-center p-1" width="50px">
								<?php
								if ($rol == 'Administrador' || ($rol == 'Mesero' && $vista_caja == 'Global')) {
								?>
									<button class="btn btn-outline-primary btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Ver" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_cierre_caja').load('paginas/detalles/caja.php/?cod_caja=<?php echo $cod_caja ?>&caja=<?php echo $caja ?>', function(){cerrar_loader();});">
										<span class="fa fa-search"></span>
									</button>
								<?php
								} else if ($rol == 'Mesero' && $vista_caja == 'Individual') {
								?>
									<button class="btn btn-outline-primary btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Ver" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_cierre_caja').load('paginas/detalles/caja_mesero.php/?cod_caja=<?php echo $cod_caja ?>&caja=<?php echo $caja ?>', function(){cerrar_loader();});">
										<span class="fa fa-search"></span>
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
						responsivePriority: 4
					},
					{
						responsivePriority: 2
					},
					{
						responsivePriority: 7
					},
					{
						responsivePriority: 8
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

		$('#btn_crear_caja').click(function() {
			document.getElementById('div_loader').style.display = 'block';
			$.ajax({
				url: "procesos/crear_caja.php",
				success: function(r) {
					datos = jQuery.parseJSON(r);
					if (datos['consulta'] == 1) {
						w_alert({
							titulo: 'Caja Creada Correctamente',
							tipo: 'success'
						});
						$('#div_tabla_caja').load('tablas/caja.php', function() {
							cerrar_loader();
						});
					} else {
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
				}
			});

		});
	</script>

<?php
} else {
	header("location:../index.php");
}
?>