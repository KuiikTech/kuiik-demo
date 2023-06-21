<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();
$conexion = $obj->conexion();

$sql = "SELECT `codigo`, `cod_cliente`, `cliente`, `descripcion`, `valor`, `fecha_registro`, `fecha_pago`, `fecha_ingreso`, `creador`, `cobrador`, `cajero`, `estado` FROM `cuentas_por_cobrar` order by FIELD(estado, 'EN MORA', 'COBRADO', 'INGRESADO') ASC, fecha_registro ASC";
$result = mysqli_query($conexion, $sql);

$nombre_tabla = 'Cuentas por cobrar';
?>
<!-- Tabla Productos -->
<div class="card">
	<div class="card-body">
		<div class="d-sm-flex align-items-center mb-4">
			<h4 class="card-title text-center"><?php echo $nombre_tabla; ?></h4>
		</div>

		<table class="table text-dark table-sm Data_Table" id="tabla_x_cobrar" style="width: 100%">
			<thead>
				<tr class="text-center">
					<th class="p-1">#</th>
					<th class="p-1">cod</th>
					<th class="p-1">Cliente</th>
					<th class="p-1">Descripción</th>
					<th class="p-1">Valor</th>
					<th class="p-1">Fecha Registro</th>
					<th class="p-1">Estado</th>
					<th class="p-1" width="50px"></th>
				</tr>
			</thead>
			<tbody class="overflow-auto">
				<?php
				$num_item = 1;
				while ($mostrar = mysqli_fetch_row($result)) {
					$cliente = json_decode($mostrar[2], true);

					$fecha_registro = strftime("%A, %e %b %Y", strtotime($mostrar[5]));
					$fecha_registro = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_registro));

					$fecha_registro .= date(' | h:i A', strtotime($mostrar[5]));

					$fecha_cobro = date('d-m-Y h:i A', strtotime($mostrar[6]));
					$fecha_ingreso = date('d-m-Y h:i A', strtotime($mostrar[7]));

					$estado = $mostrar[11];

					$codigo = $mostrar[0];

					if ($estado == 'EN MORA')
						$bg_estado = 'bg-danger text-white';
					if ($estado == 'INGRESADO')
						$estado = 'COBRADO';

					if ($estado == 'COBRADO')
						$bg_estado = 'bg-info text-white';
					$codigo_cuenta = explode('° ', $mostrar[3]);

					$cobrador = $mostrar[9];
					$cajero = $mostrar[10];

					$sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$cobrador'";
					$result_e = mysqli_query($conexion, $sql_e);
					$ver_e = mysqli_fetch_row($result_e);

					if ($ver_e != null)
						$cobrador = $ver_e[0];

					$sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$cajero'";
					$result_e = mysqli_query($conexion, $sql_e);
					$ver_e = mysqli_fetch_row($result_e);

					if ($ver_e != null)
						$cajero = $ver_e[0];

						if(!isset($cliente['nombre']))
							$cliente['nombre'] = '?????';

				?>
					<tr>
						<td class="text-center"><?php echo $num_item ?></td>
						<td class="text-center"><?php echo str_pad($mostrar[0], 3, "0", STR_PAD_LEFT) ?></td>
						<td><?php echo $cliente['nombre'] ?></td>
						<td class="text-center">
							<?php
							if ($codigo_cuenta[0] == 'Servicio N') {
							?>
								<a class="text-info" href="javascript:mostrar_servicio('<?php echo $codigo_cuenta[1] ?>')">
									<?php echo $mostrar[3] ?>
								</a>
							<?php
							} else {
							?>
								<a class="text-success fw-bold" href="#" data-bs-toggle="modal" data-bs-target="#Modal_venta" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_modal_venta').load('paginas/detalles/detalles_venta.php/?cod_venta=<?php echo $codigo_cuenta[1] ?>', function(){cerrar_loader();});">
									<?php echo $mostrar[3] ?>
								</a>
							<?php
							}
							?>
						</td>
						<td class="text-right"><b>$<?php echo number_format($mostrar[4], 0, '.', '.') ?></b></td>
						<td><?php echo $fecha_registro ?></td>
						<td class="text-center <?php echo $bg_estado ?>"><b><?php echo $estado ?></b></td>
						<td class="text-center p-1 lh-1">
							<button class="btn btn-sm btn-outline-primary btn-round" data-bs-toggle="modal" data-bs-target="#Modal_ver_cuenta" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_modal_cuenta').load('paginas/detalles/detalles_credito.php/?cod_cuenta=<?php echo $codigo ?>', function(){cerrar_loader();});">
								<span class="fas fa-search"></span>
							</button>
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

<!-- Modal detalles de venta-->
<div class="modal fade" id="Modal_venta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content sombra_modal" id="div_modal_venta"></div>
	</div>
</div>

<!-- Modal detalles de cuenta-->
<div class="modal fade" id="Modal_ver_cuenta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content sombra_modal" id="div_modal_cuenta"></div>
	</div>
</div>

<!-- #END# Tabla gastos -->
<script type="text/javascript">
	$(document).ready(function() {
		$('.Data_Table').DataTable({
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
				},
				{
					responsivePriority: 6,
					targets: 5
				},
				{
					responsivePriority: 5,
					targets: 6
				},
				{
					responsivePriority: 2,
					targets: 7
				}
			]
		});
	});
</script>