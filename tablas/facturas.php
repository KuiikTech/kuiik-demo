<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$fecha_inicial = $_GET['fecha_inicial'] . ' 00:00:00';
$fecha_final = $_GET['fecha_final'] . ' 23:59:59';

$sql = "SELECT `codigo`, `cliente`, `items`, `config`, `fecha_registro`, `creador` FROM `facturas` WHERE estado != 'ANULADA' AND fecha_registro BETWEEN '$fecha_inicial' AND '$fecha_final' order by fecha_registro ASC";
$result = mysqli_query($conexion, $sql);

$nombre_tabla = 'Facturas';
?>
<!-- Tabla Productos -->
<div class="card">
	<div class="card-body">
		<div class="d-sm-flex align-items-center mb-4">
			<div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6">
				<h4 class="card-title"><?php echo $nombre_tabla; ?></h4>
			</div>
			<div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6 text-right">
				<button class="btn btn-sm btn-outline-primary ml-auto mb-3 mb-sm-0 btn-round" onclick="click_item('vistas_pdv/nueva_factura')">
					Nueva Factura
				</button>
			</div>
		</div>
		<div class="p-1">
			<table class="table table-sm text-dark Data_Table" id="tabla_facturas" width="100%">
				<thead>
					<tr class="text-center">
						<th>#</th>
						<th>Num</th>
						<th>Cliente</th>
						<th>Fecha</th>
						<th>Total</th>
						<th>Creador</th>
						<th></th>
					</tr>
				</thead>
				<tbody class="overflow-auto">
					<?php
					$num_item = 1;
					$total_faturado = 0;
					while ($mostrar = mysqli_fetch_row($result)) {
						$cod_factura = $mostrar[0];
						$numero = '';

						$total = 0;
						$cliente = json_decode($mostrar[1], true);
						$config = json_decode($mostrar[3], true);
						if ($config['prefijo'] != '')
							$numero .= $config['prefijo'];

						$numero .= str_pad($config['numero'], 3, "0", STR_PAD_LEFT);

						if ($config['sufijo'] != '')
							$numero .= $config['sufijo'];

						$items_factura = json_decode($mostrar[2], true);
						foreach ($items_factura as $i => $producto)
							$total += $producto['valor_unitario'] * $producto['cant'];

						$creador = $mostrar[5];

						$sql_e = "SELECT nombre, apellido, foto FROM usuarios WHERE codigo = '$creador'";
						$result_e = mysqli_query($conexion, $sql_e);
						$ver_e = mysqli_fetch_row($result_e);

						$creador = $ver_e[0] . ' ' . $ver_e[1];

						$fecha_factura = strftime("%e/%m/%Y", strtotime($mostrar[4]));
						$fecha_factura = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_factura));

						$fecha_factura .= date(' - h:i A', strtotime($mostrar[4]));

						$inicio = $config['inicio'];
						$fin = $config['fin'];
						$prefijo = $config['prefijo'];
						$sufijo = $config['sufijo'];

						$ruta_pdf = 'facturas/Factura*No*' . $numero . '.pdf';
					?>
						<tr>
							<td class="text-center"><?php echo str_pad($num_item, 3, "0", STR_PAD_LEFT) ?></td>
							<td class="text-center"><?php echo $numero ?></td>
							<td><?php echo $cliente['nombre'] ?></td>
							<td><?php echo $fecha_factura ?></td>
							<td class="text-right"><strong>$<?php echo number_format($total, 0, '.', '.') ?></strong></td>
							<td class="text-center"><?php echo $creador ?></td>
							<td class="text-center">
								<button class="btn btn-outline-primary btn-round p-0 px-2" onclick="generar_PDF_factura('<?php echo $cod_factura ?>')">
									<span class="fa fa-print"></span>
								</button>
							</td>
						</tr>
					<?php
						$num_item++;
						$total_faturado += $total;
					}
					?>
				</tbody>
			</table>
		</div>
		<div class="row float-right mt-3">
			<h3>Total facturado: $<?php echo number_format($total_faturado, 0, '.', '.') ?></h3>
		</div>
	</div>
</div>

<!-- Modal detalles de factura-->
<div class="modal fade" id="Modal_factura" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content" id="contenedor_pdf"></div>
	</div>
</div>

<!-- #END# Tabla Productos -->
<script type="text/javascript">
	$(document).ready(function() {
		$('.Data_Table').DataTable();
	});
</script>