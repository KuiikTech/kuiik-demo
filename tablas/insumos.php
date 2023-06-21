<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();

$sql = "SELECT `codigo`, `descripcion`, `categoria`, `inventario`, `estado`, `barcode`, `unidades`, `fecha_registro` FROM `insumos` WHERE estado != 'ELIMINADO' order by descripcion ASC";
$result=mysqli_query($conexion,$sql);

$nombre_tabla = 'Insumos';
?>
<!-- Tabla Insumos -->
<div class="card">
	<div class="card-body p-2">
		<div class="d-sm-flex align-items-center mb-4">
			<h4 class="card-title col"><?php echo $nombre_tabla; ?></h4>
			<div class="col text-right">
				<button class="btn btn-sm btn-outline-primary ml-auto btn-round" data-bs-toggle="modal" data-bs-target="#Modal_Nuevo_Insumo">
					<span class="fas fa-plus"></span>Nuevo Insumo
				</button>
			</div>
		</div>
		<table width="100%" class="table text-dark table-sm" id="tabla_insumos">
			<thead>
				<tr class="text-center">
					<th width="50px">Cod</th>
					<th>Categoría</th>
					<th class="table-plus text-dark datatable-nosort">Descripción</th>
					<th width="120px">Stock</th>
					<th width="120px">Barcode</th>
					<th width="120px">Estado</th>
					<th width="100px"></th>
				</tr>
			</thead>
			<tbody class="overflow-auto">
				<?php 
				while ($mostrar=mysqli_fetch_row($result)) 
				{ 
					$codigo = $mostrar[0];
					$descripcion = ucwords(mb_strtolower($mostrar[1]));
					$categoria = $mostrar[2];
					$estado = $mostrar[4];
					$barcode = $mostrar[5];
					$unidades = $mostrar[6];
					$fecha_registro = $mostrar[7];

					$inventario = array();
					if ($mostrar[6] != '')
						$inventario = json_decode($mostrar[6],true);

					$stock = 0;
					foreach ($inventario as $i => $insumo)
						$stock += $insumo['stock'];

					$sql_cat = "SELECT `cod_categoria`, `nombre` FROM `categorias` WHERE cod_categoria='$categoria'";
					$result_cat=mysqli_query($conexion,$sql_cat);
					$mostrar_cat=mysqli_fetch_row($result_cat);

					$categoria = ucwords(mb_strtolower($mostrar_cat[1]));

					if($estado == 'DISPONIBLE')
						$bg_button = 'btn-success';
					if($estado == 'NO DISPONIBLE')
						$bg_button = 'btn-danger';
					?>
					<tr role="row" class="odd">
						<td class="text-center p-1"><?php echo str_pad($mostrar[0],3,"0",STR_PAD_LEFT) ?></td>
						<td class="align-middle p-1"><?php echo $categoria ?></td>
						<td width="100px"><b><?php echo $descripcion ?></b></td>
						<td class="text-center p-1 h5" id="td_stock_<?php echo $codigo ?>"><?php echo $stock.' '.$unidades ?></td>
						<td class="text-center p-1"><?php echo $barcode ?></td>
						<td class="text-center p-1">
							<button class="btn btn-sm <?php echo $bg_button ?> btn-round px-2" id="btn_estado_<?php echo $mostrar[0] ?>" onclick="cambiar_estado('<?php echo $mostrar[0] ?>')">
								<?php echo $estado ?>
							</button>
						</td>
						<td class="text-center p-1">
							<button class="btn btn-sm btn-outline-primary btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Ver" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_modal_insumo').load('detalles/detalles_insumo.php/?cod_insumo=<?php echo $codigo ?>', function(){cerrar_loader();});">
								<span class="fas fa-search"></span>
							</button>
							<button class="btn btn-sm btn-outline-warning btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Editar" onclick="actualizar_insumo('<?php echo $codigo ?>')">
								<span class="fas fa-edit"></span>
							</button>
							<button class="btn btn-sm btn-outline-danger btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Eliminar" onclick="$('#cod_insumo_delete').val(<?php echo $codigo ?>);">
								<span class="fas fa-trash"></span>
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

<!-- Modal camara-->
<div class="modal fade" id="modal_camara" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content" id="contenedor_camara"></div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function()
	{
		var input = $("input[aria-controls='tabla_insumos']");

		$('#tabla_insumos').DataTable(
		{
			responsive: true,
			columnDefs: [
			{ responsivePriority: 2, targets: 0 },
			{ responsivePriority: 4, targets: 1 },
			{ responsivePriority: 1, targets: 2 },
			{ responsivePriority: 3, targets: 3 },
			{ responsivePriority: 5, targets: 4 },
			{ responsivePriority: 6, targets: 5 },
			{ responsivePriority: 2, targets: 6 }
			]
		});
	});

	function abrir_camara(ejecutar)
	{
		document.getElementById('div_loader').style.display = 'block';
		$('#contenedor_camara').load('detalles/scan_qr.php/?ejecutar='+ejecutar, function(){cerrar_loader();});
	}

	function cargar_codigo(code)
	{
		$("input[aria-controls='tabla_insumos']").val(code);

		setTimeout('focus_input()',500);
	}

	function focus_input()
	{
		$("input[aria-controls='tabla_insumos']").focus();
	}
</script>