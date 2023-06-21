<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();

$sql = "SELECT `codigo`, `descripcion`, `valor`, `num_factura`, `fecha_registro` FROM `gastos` WHERE fecha_registro > (SELECT `fecha_apertura` FROM `caja` WHERE estado = 'ABIERTA') order by fecha_registro ASC";
$result=mysqli_query($conexion,$sql);

$nombre_tabla = 'Gastos';
?>
<!-- Tabla Productos -->
<div class="card">
	<div class="card-body">
		<div class="d-sm-flex align-items-center mb-4">
			<h4 class="card-title text-center"><?php echo $nombre_tabla; ?></h4>
			<button class="btn btn-outline-primary ml-auto mb-3 mb-sm-0 btn-round" data-bs-toggle="modal" data-bs-target="#Modal_Nuevo_Gasto">
				<i class="icon-plus btn-icon-prepend"></i>Nuevo Gasto</button>
			</div>

			<table class="table text-dark table-sm Data_Table" id="tabla_gastos">
				<thead>
					<tr class="text-center">
						<th>#</th>
						<th>cod</th>
						<th>Descripción</th>
						<th>Num Factura</th>
						<th>Valor</th>
						<th width="100px"></th>
					</tr>
				</thead>
				<tbody class="overflow-auto">
					<?php 
					$num_item = 1;
					while ($mostrar=mysqli_fetch_row($result)) 
					{ 
						?>
						<tr>
							<td class="text-center"><?php echo str_pad($num_item,3,"0",STR_PAD_LEFT) ?></td>
							<td class="text-center"><?php echo str_pad($mostrar[0],3,"0",STR_PAD_LEFT) ?></td>
							<td><?php echo $mostrar[1] ?></td>
							<td><?php echo $mostrar[3] ?></td>
							<td class="text-right"><b>$<?php echo number_format($mostrar[2],0,'.','.') ?></b></td>
							<td class="text-center p-1">
								<button class="btn btn-outline-warning btn-round px-2 py-2" data-bs-toggle="modal" data-bs-target="#Modal_Editar" onclick="actualizar_gasto('<?php echo $mostrar[0] ?>')">
									<span class="fa fa-pencil-alt"></span>
								</button>
								<button class="btn btn-outline-danger btn-round px-2 py-2" data-bs-toggle="modal" data-bs-target="#Modal_Eliminar" onclick="$('#cod_gasto_delete').val(<?php echo $mostrar[0] ?>);">
									<span class="fa fa-trash"></span>
								</button>
							</td>
						</tr>
						<?php 
						$num_item ++;
					} 
					?>
				</tbody>
			</table>
		</div>
	</div>
	<!-- #END# Tabla gastos -->
	<script type="text/javascript">
		$(document).ready(function()
		{
			$('.Data_Table').DataTable(
			{
				responsive: true,
				columns: [
				{ responsivePriority: 1 },
				{ responsivePriority: 6 },
				{ responsivePriority: 2 },
				{ responsivePriority: 4 },
				{ responsivePriority: 5 },
				{ responsivePriority: 3 }
				]
			});
		});
	</script>