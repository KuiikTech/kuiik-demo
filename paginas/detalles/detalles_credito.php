<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$cod_cuenta = $_GET['cod_cuenta'];

$sql = "SELECT `codigo`, `cod_cliente`, `cliente`, `descripcion`, `valor`, `fecha_registro`, `fecha_pago`, `fecha_ingreso`, `creador`, `cobrador`, `cajero`, `estado`, `pagos` FROM `cuentas_por_cobrar` WHERE codigo = '$cod_cuenta'";
$result = mysqli_query($conexion, $sql);
$mostrar = mysqli_fetch_row($result);

$pagos = array();
if ($mostrar[12] != '')
	$pagos = json_decode($mostrar[12], true);
$cliente = array();
if ($mostrar[2] != '')
	$cliente = json_decode($mostrar[2], true);

if (!isset($cliente['id']))
	$cliente['id'] = '???';
if (!isset($cliente['nombre']))
	$cliente['nombre'] = '???';
if (!isset($cliente['telefono']))
	$cliente['telefono'] = '???';
$creador = $mostrar[8];
$estado = $mostrar[11];
$fecha_registro = date('d-m-Y h:i A', strtotime($mostrar[5]));

$sql_e = "SELECT nombre, apellido, rol, foto, color FROM `usuarios` WHERE codigo = '$creador'";
$result_e = mysqli_query($conexion, $sql_e);
$ver_e = mysqli_fetch_row($result_e);
if ($ver_e != null) {
	$nombre_aux = explode(' ', $ver_e[0]);
	$apellido_aux = explode(' ', $ver_e[1]);
	$creador = $nombre_aux[0] . ' ' . $apellido_aux[0];
}

if ($estado == 'EN MORA')
	$text_estado = 'text-danger';
if ($estado == 'COBRADO')
	$text_estado = 'text-success';

$valor_credito = $mostrar[4];
?>
<div class="modal-header text-center p-2">
	<h5 class="modal-title">Detalles de crédito # <?php echo str_pad($cod_cuenta, 3, "0", STR_PAD_LEFT) ?></h5>
</div>
<div class="modal-body p-2">
	<div class="row m-0">
		<p class="row mb-0">
			<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Id: </span>
			<span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $cliente['id'] ?> </b></span>
		</p>
		<p class="row mb-0">
			<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Cliente: </span>
			<span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $cliente['nombre'] ?> </b></span>
		</p>
		<p class="row mb-0">
			<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Telefono: </span>
			<span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $cliente['telefono'] ?> </b></span>
		</p>
		<hr class="m-0">
		<p class="row mb-0">
			<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Creador: </span>
			<span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $creador ?> </b></span>
		</p>
		<p class="row mb-0">
			<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Fecha Registro: </span>
			<span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $fecha_registro ?> </b></span>
		</p>
		<hr class="m-0">
		<p class="row mb-0">
			<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Asociado a: </span>
			<span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $mostrar[3] ?> </b></span>
		</p>
		<p class="row mb-0">
			<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Estado: </span>
			<span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left <?php echo $text_estado ?>"><b> <?php echo $estado ?> </b></span>
		</p>
		<hr class="m-0">
		<p class="row mb-0">
			<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate h4"> Valor Total: </span>
			<span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left h4"><b>$<?php echo number_format($valor_credito, 0, '.', '.') ?></b></span>
		</p>


		<div class="row m-0 mt-2" id="div_tabla_pagos_credito">
			<div class="border-top text-center px-2">
				<h4>Pagos/Abonos</h4>
				<table class="table text-dark table-sm w-100" id="tabla_pagos_credito">
					<thead>
						<tr class="text-center">
							<th width="30px" class="table-plus text-dark datatable-nosort px-1">#</th>
							<th class="px-1">Método</th>
							<th>Valor</th>
							<th width="80px">Creador</th>
							<th width="180px">Fecha</th>
						</tr>
					</thead>
					<tbody class="overflow-auto">
						<?php
						$num_item = 1;
						$total_pagos = 0;
						foreach ($pagos as $i => $item) {
							$tipo = $item['tipo'];
							$valor = $item['valor'];
							$creador = $item['creador'];

							$fecha_pago = date('d-m-Y h:i A', strtotime($item['fecha']));

							$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
							$result_e = mysqli_query($conexion, $sql_e);
							$ver_e = mysqli_fetch_row($result_e);
							if ($ver_e != null) {
								$nombre_aux = explode(' ', $ver_e[0]);
								$apellido_aux = explode(' ', $ver_e[1]);
								$creador = $nombre_aux[0];	//.' '.$apellido_aux[0];
							}

							$total_pagos += $valor;
						?>
							<tr role="row" class="odd" title="<?php echo $fecha_pago ?>">
								<td class="text-center p-0 text-muted"><?php echo $num_item ?></td>
								<td class="text-center p-0"><?php echo $tipo ?></td>
								<td class="text-right p-0"><b>$<?php echo number_format($valor, 0, '.', '.') ?></b></td>
								<td class="text-center p-0"><?php echo $creador ?></td>
								<td class="text-center p-0"><?php echo $fecha_pago ?></td>
							</tr>
						<?php
							$num_item++;
						}
						$text_saldo = 'text-danger';

						$saldo = $valor_credito - $total_pagos;

						if ($saldo == 0)
							$text_saldo = 'text-success';

						if ($estado == 'EN MORA') {
						?>
							<tr>
								<td class="text-center p-1 text-muted"><?php echo $num_item ?></td>
								<td class="text-center" colspan="2">
									<select class="form-control form-control-sm" id="input_metodo_pago" name="input_metodo_pago">
										<option value="">Seleccione uno...</option>
										<option value="Efectivo">Efectivo</option>
										<option value="Tarjeta">Tarjeta</option>
										<option value="Nequi">Nequi</option>
										<option value="Bancolombia">Bancolombia</option>
										<option value="Daviplata">Daviplata</option>
									</select>
								</td>
								<td class="text-center" colspan="2">
									<input type="text" class="form-control form-control-sm moneda" id="input_valor_pago" name="input_valor_pago" placeholder="Valor" autocomplete="off">
								</td>
								<td class="text-center">
									<button type="button" class="btn btn-sm btn-outline-success btn-round p-0 px-1" id="btn_agregar_pago_credito">+</button>
								</td>
							</tr>

						<?php
						}
						?>
						<tr role="row" class="odd">
							<td class="text-center p-0 text-muted" colspan="2">Total Pagos/Abonos</td>
							<td class="text-right p-0"><b>$<?php echo number_format($total_pagos, 0, '.', '.') ?></b></td>
							<td class="text-center p-0" colspan="3"></td>
						</tr>
						<tr role="row" class="odd">
							<td class="text-center p-0 text-muted" colspan="2">Saldo</td>
							<td class="text-right p-0 <?php echo $text_saldo ?>"><b>$<?php echo number_format($saldo, 0, '.', '.') ?></b></td>
							<td class="text-center p-0" colspan="3"></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('input.moneda').keyup(function(event) {
		if (event.which >= 37 && event.which <= 40) {
			event.preventDefault();
		}
		$(this).val(function(index, value) {
			return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
		});
	});

	$('#input_valor_pago').keypress(function(e) {
		if (e.keyCode == 13)
			$('#btn_agregar_pago_credito').click();
	});

	$('#btn_agregar_pago_credito').click(function() {
		document.getElementById('div_loader').style.display = 'block';
		document.getElementById("btn_agregar_pago_credito").disabled = true;
		input_metodo_pago = document.getElementById("input_metodo_pago").value;
		input_valor_pago = document.getElementById("input_valor_pago").value;
		if (input_metodo_pago != '' && input_valor_pago != '') {
			$.ajax({
				type: "POST",
				data: "cod_cuenta=<?php echo $cod_cuenta ?>&input_metodo_pago=" + input_metodo_pago + "&input_valor_pago=" + input_valor_pago,
				url: "procesos/agregar_pago_credito.php",
				success: function(r) {
					datos = jQuery.parseJSON(r);
					if (datos['consulta'] == 1) {
						w_alert({
							titulo: 'Pago agregado con exito',
							tipo: 'success'
						});
						document.getElementById('div_loader').style.display = 'block';
						$('#div_modal_cuenta').load('paginas/detalles/detalles_credito.php/?cod_cuenta=<?php echo $cod_cuenta ?>', function() {
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
				}
			});
		} else {
			if (input_metodo_pago == '') {
				w_alert({
					titulo: 'Seleccione el metodo de pago',
					tipo: 'danger'
				});
				document.getElementById("input_metodo_pago").focus();
			} else if (input_valor_pago == '') {
				w_alert({
					titulo: 'Ingrese el valor del pago',
					tipo: 'danger'
				});
				document.getElementById("input_valor_pago").focus();
			}
		}

		cerrar_loader();
		document.getElementById("btn_agregar_pago_credito").disabled = false;
	});
</script>