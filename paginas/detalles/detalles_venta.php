<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();
$conexion = $obj->conexion();

$cod_venta = $_GET['cod_venta'];
if (isset($_GET['anular']))
	$anular = $_GET['anular'];
else
	$anular = 0;

$sql = "SELECT `codigo`, `cliente`, `productos`, `pago`, `fecha`, `cobrador`, `estado`, `info` FROM `ventas` WHERE codigo = '$cod_venta'";
$result = mysqli_query($conexion, $sql);
$mostrar = mysqli_fetch_row($result);

$total = 0;

$cliente = json_decode($mostrar[1], true);
$productos_venta = json_decode($mostrar[2], true);
$pagos = json_decode($mostrar[3], true);

$info = array();
if ($mostrar[7] != null)
	$info = json_decode($mostrar[7], true);

$observaciones = '';
if (isset($info['observaciones']))
	$observaciones = $info['observaciones'];

$cobrador = $mostrar[5];
$estado = $mostrar[6];

if ($cobrador != 0) {
	$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$cobrador'";
	$result_e = mysqli_query($conexion, $sql_e);
	$ver_e = mysqli_fetch_row($result_e);
	if ($ver_e != null)
		$cobrador = $ver_e[0] . ' ' . $ver_e[1];
} else
	$cobrador = 'Sistema';

$fecha_venta = strftime("%A, %e %b %Y", strtotime($mostrar[4]));
$fecha_venta = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_venta));

$fecha_venta .= date(' | h:i A', strtotime($mostrar[4]));

if (!isset($cliente['id']))
	$cliente['id'] = '???';
if (!isset($cliente['nombre']))
	$cliente['nombre'] = '???';
if (!isset($cliente['telefono']))
	$cliente['telefono'] = '???';

?>
<?php
if ($estado == 'ANULADA') {
?>
	<div class="ribbon-wrapper">
		<div class="ribbon bg-danger">
			ANULADA
		</div>
	</div>
<?php
}
?>

<div class="modal-header text-center">
	<h5 class="modal-title">Detalles de venta (N° <?php echo str_pad($cod_venta, 3, "0", STR_PAD_LEFT) ?>)</h5>
	<div class="dropdown">
		<button type="button" class="btn btn-sm btn-outline-dark btn-round " id="menu_cuenta" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
			<span class="fa fa-bars"></span>
		</button>
		<div class="dropdown-menu dropdown-menu_cuenta" aria-labelledby="menu_cuenta">
			<a href="#" class="dropdown-item" onclick="$('#Modal_Generar_Factura').modal('show');$('#cod_venta_fact').val(<?php echo $cod_venta ?>);">Generar Factura</a>
			<a href="#" class="dropdown-item" onclick="imprimir_ticket_venta('<?php echo $cod_venta ?>')">Imprimir Ticket</a>
		</div>
	</div>
</div>
<div class="modal-body pt-2">
	<p class="row mb-0">
		<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col-3 text-right"> Cliente: </span>
		<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 col-9 text-left"><b> <?php echo $cliente['nombre'] ?> </b></span>
	</p>
	<p class="row mb-0">
		<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col-3 text-right"> Telefono: </span>
		<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 col-9 text-left"><b> <?php echo $cliente['telefono'] ?> </b></span>
	</p>
	<p class="row mb-0">
		<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col-3 text-right"> Fecha: </span>
		<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 col-9 text-left"><b> <?php echo $fecha_venta ?> </b></span>
	</p>
	<p class="row mb-0">
		<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col-3 text-right"> Cobrador: </span>
		<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 col-9 text-left"><b> <?php echo $cobrador ?> </b></span>
	</p>
	<hr>
	<p class="row mb-0">
		<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col-3 text-right"> Observaciones: </span>
		<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 col-9 text-left"><b> <?php echo $observaciones ?> </b></span>
	</p>
	<hr>
	<div class="row">
		<div class="table-responsive text-dark text-center py-0 px-1">
			<table class="table text-dark table-sm" id="tabla_ventas">
				<thead>
					<tr class="text-center">
						<th>Codigo</th>
						<th>Producto</th>
						<th>Cant</th>
						<th>Valor</th>
						<th>Total</th>
					</tr>
				</thead>
				<tbody class="overflow-auto">
					<?php
					$impuestos = array();
					$iva = 0;
					foreach ($productos_venta as $i => $producto) {
						$total_producto = $producto['valor_unitario'] * $producto['cant'];
						$total += $total_producto;
						$nombre_producto = $producto['descripcion'];
					?>
						<tr>
							<td class="text-center p-1"><?php echo str_pad($producto['codigo'], 3, "0", STR_PAD_LEFT) ?></td>
							<td class="p-1"><?php echo $nombre_producto ?></td>
							<td class="text-center p-1"><?php echo $producto['cant'] ?></td>
							<td class="text-right p-1">$<?php echo number_format($producto['valor_unitario'], 0, '.', '.') ?></td>
							<td class="text-right p-1"><strong>$<?php echo number_format($total_producto, 0, '.', '.') ?></strong></td>
						</tr>
					<?php
					}
					$total_producto
					?>
					<br>
					<tr class="bg-white">
						<td colspan="4" class="text-right p-1">
							<h4 class="mb-0">Subtotal</h4>
						</td>
						<td class="text-right p-1">
							<h5 class="mb-0"><b class="text-dark">$<?php echo number_format($total - $iva, 0, '.', '.') ?></b></h5>
						</td>
					</tr>
					<tr class="bg-white">
						<td colspan="4" class="text-right p-1">
							<h4 class="mb-0">IVA</h4>
						</td>
						<td class="text-right p-1">
							<h5 class="mb-0"><b class="text-dark">$<?php echo number_format($iva, 0, '.', '.') ?></b></h5>
						</td>
					</tr>
					<tr class="bg-white pb-3">
						<td colspan="4" class="text-right p-1">
							<h4 class="mb-0">Total</h4>
						</td>
						<td class="text-right p-1">
							<h5 class="mb-0"><b class="text-dark">$<?php echo number_format($total, 0, '.', '.') ?></b></h5>
						</td>
					</tr>
					<?php
					foreach ($pagos as $i => $pago) {
						$colspan = 4;
					?>
						<tr class="bg-white">
							<?php
							if ($i == 1) {
								$colspan = 1;
							?>
								<td colspan="3" class="text-center p-1">
									<h5 class="mb-0">PAGOS</h5>
								</td>
							<?php
							}
							?>
							<td colspan="<?php echo $colspan ?>" class="text-right p-1">
								<h5 class="mb-0"><?php echo $pago['tipo'] ?></h5>
							</td>
							<td class="text-right p-1">
								<h5 class="mb-0">$<?php echo number_format($pago['valor'], 0, '.', '.') ?></h5>
							</td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="modal-footer pb-0">
		<?php
		if ($estado != 'ANULADA' && $anular == 1) {
		?>
			<div class="col">
				<button type="button" class="btn btn-sm btn-danger btn-round" id="btn_anular_venta" hidden>Anular Venta</button>
			</div>
		<?php
		}
		?>
		<button type="button" class="btn btn-sm btn-secondary btn-round" onclick="$('#Modal_venta').modal('toggle');">Cerrar</button>
	</div>

	<!-- Modal Generar factura-->
	<div class="modal fade" id="Modal_Generar_Factura" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header text-center">
					<h3 class="modal-title">Desea generar una factura a partir de esta venta?</h3>
				</div>
				<div class="modal-body">
					<input type="number" name="cod_venta_fact" id="cod_venta_fact" hidden="">
					<div class="row px-4 pb-3">
						<label class="col-label pr-2">Cliente: </label>
						<select class="col form-control select2" id="cod_cliente_fact" name="cod_cliente_fact" style="width: 80% !important">
							<option value="">Buscar cliente </option>
							<?php
							$sql_clientes = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro`, `tipo`, `info` FROM `clientes` WHERE codigo != 0 order by nombre";
							$result_clientes = mysqli_query($conexion, $sql_clientes);
							while ($mostrar_clientes = mysqli_fetch_row($result_clientes)) {
								$str_check = '';
								$nombre = $mostrar_clientes[1] . ' - ' . $mostrar_clientes[2] . ' (' . $mostrar_clientes[3] . ')';
							?>
								<option value="<?php echo $mostrar_clientes[0] ?>" <?php echo $str_check ?>><?php echo $nombre ?></option>
							<?php
							}
							?>
						</select>
					</div>
					<div class="row m-0 p-1 d-flex justify-content-between align-items-center ">
						<button type="button" class="btn btn-sm btn-outline-secondary btn-round col" data-bs-dismiss="modal">NO</button>
						<button type="button" class="btn btn-sm btn-outline-primary btn-round col" id="btn_generar_factura">SI, Generar</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$(".select2").select2({
			dropdownParent: $('#Modal_venta .modal-body')
		});

		$('#btn_anular_venta').click(function() {
			document.getElementById('div_loader').style.display = 'block';
			$.ajax({
				type: "POST",
				data: "cod_venta=<?php echo $cod_venta ?>",
				url: "procesos/anular_venta.php",
				success: function(r) {
					datos = jQuery.parseJSON(r);
					if (datos['consulta'] == 1) {
						w_alert({
							titulo: 'Venta Anulada Correctamente',
							tipo: 'success'
						});
						$('#div_modal_venta').load('paginas/detalles/detalles_venta.php/?cod_venta=<?php echo $cod_venta ?>', function() {
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

		$('#btn_generar_factura').click(function() {
			cod_venta = document.getElementById("cod_venta_fact").value;
			cod_cliente = document.getElementById("cod_cliente_fact").value;
			document.getElementById('div_loader').style.display = 'block';
			if (cod_cliente != '') {
				$.ajax({
					type: "POST",
					data: "cod_venta=" + cod_venta + "&cod_cliente=" + cod_cliente,
					url: "procesos/generar_factura.php",
					success: function(r) {
						datos = jQuery.parseJSON(r);
						if (datos['consulta'] == 1) {
							//w_alert({ titulo: 'Factura Generada Correctamente', tipo: 'success' });
							$("#Modal_Generar_Factura").modal('toggle');
							$('.modal-backdrop').remove();
							document.querySelector("body").style.overflow = "auto";
							cerrar_loader();

							ruta_pdf = datos['ruta_pdf'];

							$("#Modal_factura").modal('show');
							w_alert({
								titulo: 'Factura Generada Correctamente',
								tipo: 'success'
							});
							$('#contenedor_pdf').load('paginas/detalles/ver_factura_pdf.php/?ruta=' + ruta_pdf + '&imprimir=1');
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
			} else {
				w_alert({
					titulo: 'Seleccione un cliente',
					tipo: 'danger'
				});
				cerrar_loader();
			}
		});
	</script>