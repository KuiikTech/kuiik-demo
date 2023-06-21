<?php 
session_set_cookie_params(7*24*60*60);
session_start();
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();

$sql = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE estado != 'ELIMINADO' order by descripcion ASC";
$result=mysqli_query($conexion,$sql);

$nombre_tabla = 'Todas las Bodegas';

$rol = '';

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
	$result_e=mysqli_query($conexion,$sql_e);
	$ver_e=mysqli_fetch_row($result_e);

	$cedula = $ver_e[1];

	$nombre_usuario = $ver_e[2].' '.$ver_e[3];
	$rol = $ver_e[5];
}

$costo_total_invertido = 0;
$valor_total_invertido = 0;
?>
<!-- Tabla Bodega -->
<div class="card-body p-2">
	<div class="d-flex justify-content-between m-0 mb-2">
		<div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6">
			<h4 class="card-title"><?php echo $nombre_tabla; ?>
			<?php 
			if($rol == 'Administrador')
			{
				?>
				<button class="btn btn-sm btn-outline-primary ml-auto btn-round" data-bs-toggle="modal" data-bs-target="#Modal_inversion">
					Inversión
				</button>
				<?php 
			}
			?>
		</h4>
	</div>
</div>
<div class="d-sm-flex align-items-center row m-0 mb-2">
	<table class="table text-dark table-sm" style="width: 100% !important" id="tabla_bodega_todas">
		<thead>
			<tr class="text-center">
				<th class="p-1" width="50px">#</th>
				<th class="p-1" width="80px">Cod</th>
				<th class="p-1" width="100px">Categoría</th>
				<th class="p-1">Descripción</th>
				<th class="p-1" width="120px">Principal</th>
				<th class="p-1" width="120px">PDV 1</th>
				<th class="p-1" width="120px">PDV 2</th>
				<th class="p-1" width="100px">Barcode</th>
			</tr>
		</thead>
		<tbody class="overflow-auto">
			<?php 
			$num_item = 1;
			while ($mostrar=mysqli_fetch_row($result)) 
			{ 
				$codigo = $mostrar[0];
				$descripcion = $mostrar[1];
				$categoria = $mostrar[2];
				$estado = $mostrar[4];
				$barcode = $mostrar[5];
				$fecha_registro = $mostrar[6];

				$btn_estado_d = 'disabled';

				$inventario_p = array();
				$inventario_1 = array();
				$inventario_2 = array();

				if ($mostrar[3] != '')
					$inventario_p = json_decode($mostrar[3],true);

				if ($mostrar[6] != '')
					$inventario_1 = json_decode($mostrar[6],true);

				if ($mostrar[7] != '')
					$inventario_2 = json_decode($mostrar[7],true);

				$stock_p = 0;
				$valor_publico_p = 0;
				$valor_mayor_p = 0;
				foreach ($inventario_p as $i => $producto)
				{
					$stock_p += intval($producto['stock']);
					$valor_publico_p = $producto['valor_venta'];
					$valor_mayor_p = $producto['valor_venta_mayor'];

					if($producto['stock'] > 0)
					{
						$costo_total_invertido += intval($producto['stock'])*intval($producto['costo']);
						$valor_total_invertido += intval($producto['stock'])*intval($producto['valor_venta']);
					}
				}

				$stock_1 = 0;
				$valor_publico_1 = 0;
				$valor_mayor_1 = 0;
				foreach ($inventario_1 as $i => $producto)
				{
					$stock_1 += intval($producto['stock']);
					$valor_publico_1 = $producto['valor_venta'];
					$valor_mayor_1 = $producto['valor_venta_mayor'];

					if($producto['stock'] > 0)
					{
						$costo_total_invertido += intval($producto['stock'])*intval($producto['costo']);
						$valor_total_invertido += intval($producto['stock'])*intval($producto['valor_venta']);
					}
				}

				$stock_2 = 0;
				$valor_publico_2 = 0;
				$valor_mayor_2 = 0;
				foreach ($inventario_2 as $i => $producto)
				{
					$stock_2 += intval($producto['stock']);
					$valor_publico_2 = $producto['valor_venta'];
					$valor_mayor_2 = $producto['valor_venta_mayor'];

					if($producto['stock'] > 0)
					{
						$costo_total_invertido += intval($producto['stock'])*intval($producto['costo']);
						$valor_total_invertido += intval($producto['stock'])*intval($producto['valor_venta']);
					}
				}

				$sql_cat = "SELECT `cod_categoria`, `nombre` FROM `categorias_productos` WHERE cod_categoria='$categoria'";
				$result_cat=mysqli_query($conexion,$sql_cat);
				$mostrar_cat=mysqli_fetch_row($result_cat);

				if($mostrar_cat != NULL)
					$categoria = ucwords(strtolower($mostrar_cat[1]));

				if($estado == 'DISPONIBLE')
					$bg_button = 'btn-success';
				if($estado == 'NO DISPONIBLE')
					$bg_button = 'btn-danger';
				?>
				<tr role="row" class="odd">
					<td class="text-center p-1"><?php echo str_pad($num_item,3,"0",STR_PAD_LEFT) ?></td>
					<td class="text-center p-1"><?php echo str_pad($codigo,3,"0",STR_PAD_LEFT) ?></td>
					<td class="align-middle p-1"><?php echo $categoria ?></td>
					<td class="p-1"><b><?php echo $descripcion ?></b></td>
					<td class="text-center p-1 h5" id="td_stock_p_<?php echo $codigo ?>">
						<?php echo $stock_p ?>
						<br>
						<small>VP: $<?php echo number_format($valor_publico_p,0,'.','.') ?></small>
						<br>
						<small>VM: $<?php echo number_format($valor_mayor_p,0,'.','.') ?></small>
					</td>
					<td class="text-center p-1 h5" id="td_stock_1_<?php echo $codigo ?>">
						<?php echo $stock_1 ?>
						<br>
						<small>VP: $<?php echo number_format($valor_publico_1,0,'.','.') ?></small>
						<br>
						<small>VM: $<?php echo number_format($valor_mayor_1,0,'.','.') ?></small>
					</td>
					<td class="text-center p-1 h5" id="td_stock_2_<?php echo $codigo ?>">
						<?php echo $stock_2 ?>
						<br>
						<small>VP: $<?php echo number_format($valor_publico_2,0,'.','.') ?></small>
						<br>
						<small>VM: $<?php echo number_format($valor_mayor_2,0,'.','.') ?></small>
					</td>
					<td class="text-center p-1"><?php echo $barcode ?></td>
				</tr>
				<?php 
				$num_item ++;
			} 
			?>
		</tbody>
	</table>
</div>
</div>

<?php 
if($rol == 'Administrador')
{
	?>
	<div class="modal fade" id="Modal_inversion" tabindex="-1" role="dialog" aria-bs-labelledby="exampleModalLabel" aria-bs-hidden="true">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-body p-2">
					<div class="row px-2 text-center">
						<h3 class="mb-0">COSTO TOTAL INVERTIDO: <strong> $<?php echo number_format($costo_total_invertido,0,'.','.')?> </strong></h3>
					</div>
					<hr>
					<div class="row px-2 text-center">
						<h3 class="mb-0">VALOR DE VENTA: <strong> $<?php echo number_format($valor_total_invertido,0,'.','.')?> </strong></h3>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php 
}
?>

<script type="text/javascript">
	$('#tabla_bodega_todas').DataTable(
	{
		responsive: true,
		autoWidth: false,
		columnDefs: [
		{ responsivePriority: 1, targets: 0 },
		{ responsivePriority: 3, targets: 1 },
		{ responsivePriority: 1, targets: 2 },
		{ responsivePriority: 2, targets: 3 },
		{ responsivePriority: 4, targets: 4 },
		{ responsivePriority: 5, targets: 5 },
		{ responsivePriority: 6, targets: 6 },
		{ responsivePriority: 2, targets: 7 }
		]
	});

	$('#tabla_bodega_todas_wrapper').addClass('px-0');
</script>