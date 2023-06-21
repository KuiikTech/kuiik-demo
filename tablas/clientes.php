<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();
$conexion = $obj->conexion();

if (isset($_GET['input_buscar'])) {
	$busqueda = str_replace("***", "%", $_GET['input_buscar']);

	$sql = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro`, `tipo` FROM `clientes` WHERE `nombre` LIKE '%$busqueda%' OR `id` LIKE '%$busqueda%' OR `telefono` LIKE '%$busqueda%' ORDER BY `nombre` ASC";
	$result = mysqli_query($conexion, $sql);
} else {
	$sql = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro`, `tipo` FROM `clientes` ORDER BY `nombre` ASC LIMIT 50";
	$result = mysqli_query($conexion, $sql);
	$busqueda = '';
}


$nombre_tabla = 'Clientes encontrados';
?>
<!-- Tabla Productos -->
<div class="card">
	<div class="card-body p-2">
		<div class="d-sm-flex align-items-center row m-0 mb-2">
			<h4 class="card-title"><?php echo $nombre_tabla; ?></h4>
		</div>
		<div class="d-sm-flex align-items-center row m-0 mb-2">
			<table class="table text-dark table-sm Data_Table" id="tabla_clientes" width="100%">
				<thead>
					<tr class="text-center">
						<th class="p-1">#</th>
						<th class="p-1">COD</th>
						<th class="p-1" width="150px">Identificación</th>
						<th class="p-1">Nombre</th>
						<th class="p-1" width="120px">Telefono</th>
						<th class="p-1">Dirección</th>
						<th class="p-1" width="120px"></th>
					</tr>
				</thead>
				<tbody class="overflow-auto">
					<?php
					$num_item = 1;
					$busqueda = explode('%', $busqueda);
					while ($mostrar = mysqli_fetch_row($result)) {
						$codigo = $mostrar[0];
						$identificacion = $mostrar[1];
						$nombre = mb_strtolower($mostrar[2]);
						$direccion = $mostrar[4];
						$telefono = $mostrar[3];
						$fecha_registro = $mostrar[6];

						foreach ($busqueda as $i => $palabra) {
							$nombre = ucwords(str_ireplace($palabra, '??//' . ucwords($palabra) . '))//', $nombre));
							$identificacion = ucwords(str_ireplace($palabra, '??//' . ucwords($palabra) . '))//', $identificacion));
							$telefono = ucwords(str_ireplace($palabra, '??//' . ucwords($palabra) . '))//', $telefono));
						}

						$nombre = ucwords(str_ireplace('??//', '<mark>', $nombre));
						$identificacion = ucwords(str_ireplace('??//', '<mark>', $identificacion));
						$telefono = ucwords(str_ireplace('??//', '<mark>', $telefono));

						$nombre = ucwords(str_ireplace('))//', '</mark>', $nombre));
						$identificacion = ucwords(str_ireplace('))//', '</mark>', $identificacion));
						$telefono = ucwords(str_ireplace('))//', '</mark>', $telefono));

						$tipo = $mostrar[8];
						$vip = '';

						if ($tipo == '')
							$tipo = "Regular";
						else
							$vip = '<span class="fa fa-tag text-info" title="Cliente especial"></span>';

					?>
						<tr role="row" class="odd">
							<td class="p-1 text-center"><?php echo str_pad($num_item, 3, "0", STR_PAD_LEFT) ?></td>
							<td class="p-1 text-center"><?php echo str_pad($codigo, 3, "0", STR_PAD_LEFT) ?></td>
							<td class="p-1 text-center"><b><?php echo $identificacion ?></b></td>
							<td class="p-1"><b><?php echo $nombre . ' ' . $vip ?></b></td>
							<td class="p-1 text-right"><?php echo $telefono ?></td>
							<td class="p-1"><?php echo $direccion ?></td>
							<td class="p-0 text-center">
								<button class="btn btn-sm btn-outline-primary btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Ver" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_detalles_cliente').load('paginas/detalles/detalles_cliente.php/?cod_cliente=<?php echo $codigo ?>', function(){cerrar_loader();});">
									<span class="fa fa-search"></span>
								</button>
								<button class="btn btn-sm btn-outline-warning btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Editar" onclick="actualizar_cliente('<?php echo $mostrar[0] ?>')">
									<span class="fa fa-edit"></span>
								</button>
								<!--
								<button class="btn btn-sm btn-outline-danger btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Eliminar" onclick="$('#cod_cliente_delete').val(<?php echo $mostrar[0] ?>);">
									<span class="fa fa-trash"></span>
								</button>-->
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
</div>

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
					responsivePriority: 5
				},
				{
					responsivePriority: 6
				},
				{
					responsivePriority: 7
				},
				{
					responsivePriority: 3
				}
			]
		});
	});
</script>