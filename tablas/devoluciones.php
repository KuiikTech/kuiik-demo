<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$sql = "SELECT `codigo`, `descripcion`, `producto`, `cambio`, `creador`, `fecha_registro` FROM `devolucion` order by fecha_registro DESC";
$result = mysqli_query($conexion, $sql);

$nombre_tabla = 'Devoluciones';
?>
<!-- Tabla Devoluciones -->
<div class="card">
	<div class="card-body p-2">
		<div class="d-sm-flex align-items-center row m-0 mb-2">
			<div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6">
				<h4 class="card-title"><?php echo $nombre_tabla; ?></h4>
			</div>
			<div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6 text-right">
				<button class="btn btn-sm btn-outline-primary ml-auto btn-round" data-bs-toggle="modal" data-bs-target="#Modal_Nuevo_Devolucion">
					<span class="fa fa-plus"></span>Nueva Devolución/Cambio
				</button>
			</div>
		</div>
		<div class="d-sm-flex align-items-center row m-0 mb-2">
			<table class="table text-dark table-sm" id="tabla_devoluciones">
				<thead>
					<tr class="text-center">
						<th class="p-1" width="20px">#</th>
						<th class="p-1" width="80px">Cod</th>
						<th class="p-1">Descripción</th>
						<th class="p-1">Producto</th>
						<th class="p-1">Cambio</th>
						<th class="p-1" width="100px">Fecha</th>
					</tr>
				</thead>
				<tbody class="overflow-auto">
					<?php
					$num_item = 1;
					while ($mostrar = mysqli_fetch_row($result)) {
						$codigo = $mostrar[0];
						$descripcion = $mostrar[1];
						$creador = $mostrar[4];
						$fecha_registro = $mostrar[5];

						$producto = array();
						if ($mostrar[2] != '')
							$producto = json_decode($mostrar[2], true);

						$cambio = array();
						if ($mostrar[3] != '')
							$cambio = json_decode($mostrar[3], true);

						$sql_e = "SELECT nombre, apellido, foto FROM `usuarios` WHERE codigo = '$creador'";
						$result_e = mysqli_query($conexion, $sql_e);
						$ver_e = mysqli_fetch_row($result_e);

						$creador = $ver_e[0] . ' ' . $ver_e[1];
					?>
						<tr role="row" class="odd">
							<td class="text-center p-1"><?php echo $num_item ?></td>
							<td class="text-center p-1"><?php echo str_pad($codigo, 3, "0", STR_PAD_LEFT) ?></td>
							<td class="align-middle p-1"><?php echo $descripcion ?></td>
							<td class="text-center p-0">
								<?php echo $producto['descripcion'] ?>
								<p class="m-0 p-0"><small>Cant:<b><?php echo $producto['cant'] ?></b> Valor:<b>$<?php echo number_format($producto['valor_unitario'], 0, '.', '.') ?></b></small></p>
							</td>
							<td class="text-center p-0">
								<?php
								if ($mostrar[3] != '') {
									echo $cambio['descripcion'];
								?>
									<p class="m-0 p-0"><small>Cant:<b><?php echo $cambio['cant'] ?></b> Valor:<b>$<?php echo number_format($cambio['valor_unitario'], 0, '.', '.') ?></b></small></p>
								<?php
								} ?>
							</td>
							<td class="text-center p-1"><?php echo $fecha_registro ?></td>
						</tr>
					<?php
						$num_item++;
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#tabla_devoluciones').DataTable({
			responsive: true,
			columnDefs: [{
					responsivePriority: 2,
					targets: 0
				},
				{
					responsivePriority: 4,
					targets: 1
				},
				{
					responsivePriority: 1,
					targets: 2
				},
				{
					responsivePriority: 3,
					targets: 3
				},
				{
					responsivePriority: 2,
					targets: 4
				}
			]
		});
	});
</script>