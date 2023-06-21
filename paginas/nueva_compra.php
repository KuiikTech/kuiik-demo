<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();
$conexion = $obj->conexion();
session_set_cookie_params(7 * 24 * 60 * 60);
session_start();

$usuario = $_SESSION['usuario_restaurante'];
$caja = $_SESSION['caja_restaurante'];

$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
$result_e = mysqli_query($conexion, $sql_e);
$ver_e = mysqli_fetch_row($result_e);

$nombre = $ver_e[0];
$rol = $ver_e[1];

$sql = "SELECT `codigo`, `productos`, `proveedor`, `creador`, `estado`, `fecha_registro`, `observaciones` FROM `compras` WHERE estado = 'EN PROCESO' order by fecha_registro DESC";
$result = mysqli_query($conexion, $sql);
$mostrar = mysqli_fetch_row($result);

if ($mostrar != NULL) {
	if ($mostrar[2] != '') {
		$proveedor_compra = json_decode($mostrar[2], true);
		$proveedor_compra = $proveedor_compra['codigo'];
	} else
		$proveedor_compra = '';

	$observaciones = $mostrar[6];
?>
	<div class="card">
		<div class="card-header text-center py-2 pl-2 bg-primary">
			<h4 class="card-title col text-white mb-0">Nueva compra</h4>
		</div>
		<div class="card-body p-2">
			<div class="row m-0 pt-1">
				<label class="col-sm-auto col-form-label py-1 text-truncate">Proveedor:<span class="requerido">*</span> </label>
				<div class="col-sm-7">
					<select class="form-control form-control-sm select2" id="input_tecnico_serv" name="input_tecnico_serv" onchange="guardar_proveedor(this.value)">
						<option value="">Seleccione el proveedor</option>
						<?php
						$sql_proveedores = "SELECT `codigo`, `nombre`, `telefono`, `ciudad` FROM `proveedores` ORDER BY `nombre` ASC";
						$result_proveedores = mysqli_query($conexion, $sql_proveedores);
						while ($mostrar_proveedores = mysqli_fetch_row($result_proveedores)) {
							$nombre_proveedor = $mostrar_proveedores[1] . ' (' . $mostrar_proveedores[3] . ')';
							if ($proveedor_compra == $mostrar_proveedores[0])
								$selecionado = 'selected';
							else
								$selecionado = '';
						?>
							<option value="<?php echo $mostrar_proveedores[0] ?>" <?php echo $selecionado ?>><?php echo $nombre_proveedor ?></option>
						<?php
						}
						?>
					</select>
				</div>
			</div>
			<hr>
			<div class="row m-0 text-center">
				<h5>Productos a ingresar</h5>
			</div>
			<div class="row m-0 pt-1" id="tabla_productos_compra"></div>

			<div class="row m-0 pt-1">
				<label class="col-sm-auto col-form-label py-1 text-truncate">Observaciones:</label>
				<div class="col-sm-9">
					<textarea class="form-control form-control-sm" id="input_obs" name="input_obs" onchange="guardar_obs(this.value)"><?php echo $observaciones ?></textarea>
				</div>
			</div>

		</div>
		<div class="card-footer p-2 row border-top border-2">
			<div class="col text-right">
				<button type="button" class="btn btn-sm btn-outline-primary btn-round" data-bs-toggle="modal" data-bs-target="#Modal_agregar_compra" id="btn_Modal_agregar_compra">Agregar Compra</button>
			</div>
		</div>
	</div>

	<!-- Modal agregar compra-->
	<div class="modal fade" id="Modal_agregar_compra" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header text-center">
					<h5 class="modal-title">Seguro desea agregar este compra?</h5>
				</div>
				<div class="modal-body p-3">
					<div class="row m-0 p-1">
						<button type="button" class="btn btn-sm btn-secondary btn-round col" data-bs-dismiss="modal" id="close_Modal_agregar_compra">NO</button>
						<button type="button" class="btn btn-sm btn-outline-primary btn-round col" id="btn_agregar_compra">SI, Agregar</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		document.getElementById('div_loader').style.display = 'block';
		$('#tabla_productos_compra').load('tablas/productos_compra.php', cerrar_loader());

		$('[data-bs-toggle="tooltip"]').tooltip();

		$(".select2").select2();

		function guardar_proveedor(cod_proveedor) {
			document.getElementById('div_loader').style.display = 'block';
			$.ajax({
				type: "POST",
				data: "cod_proveedor=" + cod_proveedor,
				url: "procesos/guardar_proveedor.php",
				success: function(r) {
					datos = jQuery.parseJSON(r);
					if (datos['consulta'] == 1)
						w_alert({
							titulo: 'Proveedor guardado',
							tipo: 'success'
						});
					else {
						w_alert({
							titulo: datos['consulta'],
							tipo: 'danger'
						});
						if (datos['consulta'] == 'Reload') {
							document.getElementById('div_login').style.display = 'block';
							cerrar_loader();

						}
					}
				}
			});
			cerrar_loader();
		}

		function guardar_obs(observaciones) {
			document.getElementById('div_loader').style.display = 'block';
			$.ajax({
				type: "POST",
				data: "observaciones=" + observaciones,
				url: "procesos/guardar_obs_compra.php",
				success: function(r) {
					datos = jQuery.parseJSON(r);
					if (datos['consulta'] == 1)
						w_alert({
							titulo: 'Observaciones guardadas',
							tipo: 'success'
						});
					else {
						w_alert({
							titulo: datos['consulta'],
							tipo: 'danger'
						});
						if (datos['consulta'] == 'Reload') {
							document.getElementById('div_login').style.display = 'block';
							cerrar_loader();

						}
					}
				}
			});
			cerrar_loader();
		}

		$('input.moneda').keyup(function(event) {
			if (event.which >= 37 && event.which <= 40) {
				event.preventDefault();
			}

			$(this).val(function(index, value) {
				return value
					.replace(/\D/g, "")
					.replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
			});
		});

		$('#btn_agregar_compra').click(function() {
			$.ajax({
				type: "POST",
				url: "procesos/procesar_compra.php",
				success: function(r) {
					datos = jQuery.parseJSON(r);
					if (datos['consulta'] == 1) {
						w_alert({
							titulo: 'Compra agregada correctamente',
							tipo: 'success'
						});
						$('.modal-backdrop').remove();
						$("#Modal_agregar_compra").modal('toggle');
						document.querySelector("body").style.overflow = "auto";

						click_item('compras');
					} else {
						w_alert({
							titulo: datos['consulta'],
							tipo: 'danger'
						});
						if (datos['consulta'] == 'Reload') {
							document.getElementById('div_login').style.display = 'block';
							cerrar_loader();

						}
					}
				}
			});
		});

		function eliminar_pago(cod_espacio, item) {
			document.getElementById('div_loader').style.display = 'block';
			$.ajax({
				type: "POST",
				data: "cod_espacio=" + cod_espacio + "&item=" + item,
				url: "procesos/eliminar_pago.php",
				success: function(r) {
					datos = jQuery.parseJSON(r);
					if (datos['consulta'] == 1) {
						w_alert({
							titulo: 'Descuento Eliminado',
							tipo: 'success'
						});
						$('#div_add_compra').load('paginas/nueva_compra/ns_info.php', function() {
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
					}

					cerrar_loader();
				}
			});
		}
	</script>
<?php
} else {
	$sql = "INSERT INTO `compras`(`productos`, `proveedor`, `creador`, `fecha_registro`, `estado`) VALUES (
		'',
		'',
		'$usuario',
		'$fecha_h',
		'EN PROCESO')";

	$verificacion = mysqli_query($conexion, $sql);
?>
	<script type="text/javascript">
		click_item('nueva_compra');
	</script>
<?php
}
?>