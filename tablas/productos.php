<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();
$conexion = $obj->conexion();

$sql = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode`, `especial` FROM `productos` WHERE estado != 'ELIMINADO' order by descripcion ASC";
$result = mysqli_query($conexion, $sql);

$nombre_tabla = 'Productos';
session_set_cookie_params(7 * 24 * 60 * 60);
session_start();

if (isset($_SESSION['usuario_restaurante2']))
	$bodega = 'PDV_2';
else
	$bodega = 'PDV_1';

$rol = '';

$costo_total_invertido = 0;
$valor_total_invertido = 0;
?>
<!-- Tabla Productos -->
<div class="card">
	<div class="card-body p-2">
		<div class="d-sm-flex align-items-center row m-0 mb-2">
			<div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6">
				<h4 class="card-title"><?php echo $nombre_tabla; ?></h4>
			</div>
			<div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6 text-right">
				<button class="btn btn-sm btn-outline-primary ml-auto btn-round col-auto" data-bs-toggle="modal" data-bs-target="#Modal_Nuevo_Producto">
					<i class="icon-plus btn-icon-prepend"></i>Nuevo Producto
				</button>
			</div>

		</div>
		<div class="d-sm-flex align-items-center row m-0 mb-2">
			<table class="table text-dark table-sm" id="tabla_productos">
				<thead>
					<tr class="text-center">
						<th class="p-1" width="50px">#</th>
						<th class="p-1" width="80px">Cod</th>
						<th class="p-1" width="100px">Categoría</th>
						<th class="p-1">Descripción</th>
						<th class="p-1" width="100px">Stock</th>
						<th class="p-1" width="100px">Barcode</th>
						<th class="p-1" width="100px">Tipo</th>
						<th class="p-1" width="130px">Estado</th>
						<th class="p-1" width="100px"></th>
					</tr>
				</thead>
				<tbody class="overflow-auto">
					<?php
					$num_item = 1;
					while ($mostrar = mysqli_fetch_row($result)) {
						$codigo = $mostrar[0];
						$descripcion = $mostrar[1];
						$categoria = $mostrar[5];
						$estado = $mostrar[10];
						$barcode = $mostrar[11];
						$fecha_registro = $mostrar[7];
						$tipo = $mostrar[9];
						$especial = $mostrar[12];

						$inventario = $mostrar[4];

						if ($tipo != 'Producto')
							$inventario = 0;

						$sql_cat = "SELECT `cod_categoria`, `nombre` FROM `categorias_productos` WHERE cod_categoria='$categoria'";
						$result_cat = mysqli_query($conexion, $sql_cat);
						$mostrar_cat = mysqli_fetch_row($result_cat);

						if ($mostrar_cat != NULL)
							$categoria = ucwords(strtolower($mostrar_cat[1]));

						if ($estado == 'DISPONIBLE')
							$bg_button = 'btn-success';
						if ($estado == 'NO DISPONIBLE')
							$bg_button = 'btn-danger';

						if ($especial == 'SI')
							$especial = '<span class="fa fa-tag text-info" title="Producto especial"></span>';
						else
							$especial = "";

					?>
						<tr role="row" class="odd">
							<td class="text-center p-1"><?php echo str_pad($num_item, 3, "0", STR_PAD_LEFT) ?></td>
							<td class="text-center p-1"><?php echo str_pad($codigo, 3, "0", STR_PAD_LEFT) ?></td>
							<td class="align-middle p-1"><?php echo $categoria ?></td>
							<td class="p-1"><b><?php echo $especial ?> <?php echo $descripcion ?></b></td>
							<td class="text-center p-1 h5" id="td_stock_<?php echo $codigo ?>"><?php echo $inventario ?></td>
							<td class="text-center p-1"><?php echo $barcode ?></td>
							<td class="text-center p-1"><?php echo $tipo ?></td>
							<td class="text-center p-1">
								<button class="btn btn-sm <?php echo $bg_button ?> btn-round px-2" id="btn_estado_<?php echo $mostrar[0] ?>" onclick="cambiar_estado('<?php echo $mostrar[0] ?>')">
									<?php echo $estado ?>
								</button>
							</td>
							<td class="text-center p-1">
								<button class="btn btn-outline-primary btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Ver" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_modal_producto').load('paginas/detalles/detalles_producto.php/?cod_producto=<?php echo $codigo ?>&bodega=<?php echo $bodega ?>', function(){cerrar_loader();});">
									<span class="fa fa-search"></span>
								</button>
								<button class="btn btn-outline-warning btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Editar" onclick="actualizar_producto('<?php echo $codigo ?>')">
									<span class="fa fa-edit"></span>
								</button>
								<button class="btn btn-outline-danger btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Eliminar" onclick="$('#cod_producto_delete').val(<?php echo $codigo ?>);">
									<span class="fa fa-trash"></span>
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
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#tabla_productos').DataTable({
			responsive: true,
			columnDefs: [{
					responsivePriority: 1,
					targets: 0
				},
				{
					responsivePriority: 3,
					targets: 1
				},
				{
					responsivePriority: 1,
					targets: 2
				},
				{
					responsivePriority: 2,
					targets: 3
				},
				{
					responsivePriority: 4,
					targets: 4
				},
				{
					responsivePriority: 5,
					targets: 5
				},
				{
					responsivePriority: 6,
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