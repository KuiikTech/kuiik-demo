<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();

$sql = "SELECT `cod_categoria`, `nombre` FROM `categorias_productos` order by nombre ASC";
$result=mysqli_query($conexion,$sql);

$nombre_tabla = 'Categorias de productos';
?>
<!-- Tabla Categorias -->
<div class="card">
	<div class="card-body">
		<div class="d-sm-flex align-items-center mb-4">
			<h4 class="card-title text-center"><?php echo $nombre_tabla; ?></h4>
			<button class="btn btn-sm btn-outline-primary ml-auto mb-3 mb-sm-0 btn-round" data-bs-toggle="modal" data-bs-target="#Modal_Nueva_Categoria">
				Nueva Categoria
			</button>
		</div>
		<table class="table text-dark table-sm Data_Table" id="tabla_categoria">
			<thead>
				<tr class="text-center">
					<th width="120px">Cod</th>
					<th>Descripción</th>
					<th width="120px"></th>
				</tr>
			</thead>
			<tbody class="overflow-auto">
				<?php 
				while ($mostrar=mysqli_fetch_row($result)) 
				{ 
					$codigo = $mostrar[0];
					$descripcion = $mostrar[1];
					?>
					<tr>
						<td class="text-center"><?php echo str_pad($codigo,3,"0",STR_PAD_LEFT) ?></td>
						<td><?php echo $descripcion ?></td>
						<td class="text-center p-1">
								<!--
								<button class="btn btn-outline-primary btn-round" data-bs-toggle="modal" data-bs-target="#Modal_Ver" onclick="ver_categoria('<?php echo $mostrar[0] ?>')">
									<i class="icon-magnifier"></i>
								</button>
								<button class="btn btn-outline-warning btn-round" data-bs-toggle="modal" data-bs-target="#Modal_Editar" onclick="actualizar_categoria('<?php echo $mostrar[0] ?>')">
									<span class="fa fa-pencil-alt"></span>
								</button>-->
								<button class="btn btn-outline-danger btn-round" data-bs-toggle="modal" data-bs-target="#Modal_Eliminar" onclick="$('#cod_categoria_delete').val(<?php echo $mostrar[0] ?>);">
									<span class="fa fa-trash"></span>
								</button>
							</td>
						</tr>
						<?php 
					} 
					?>
				</tbody>
			</table>
		</div>
	</div>
	<!-- #END# Tabla Categorias -->
	<script type="text/javascript">
		$(document).ready(function()
		{
			$('.Data_Table').DataTable(
			{
				responsive: true,
				columns: [
				{ responsivePriority: 1 },
				{ responsivePriority: 2 },
				{ responsivePriority: 2 }
				]
			});
		});
	</script>