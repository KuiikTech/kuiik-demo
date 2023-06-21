<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$palabras = '';
$busqueda = '';
$busqueda2 = '';

if (isset($_GET['busqueda'])) {
	$busqueda = str_replace("***", "%", $_GET['busqueda']);
	$busqueda2 = str_replace("***", " ", $_GET['busqueda']);
	$busqueda = '%' . $busqueda . '%';
	$sql = "SELECT `codigo`, `productos`, `proveedor`, `creador`, `estado`, `fecha_registro`, `observaciones`, `pagos`, `notas`, `factura` FROM `compras` WHERE productos LIKE '$busqueda' AND  estado != 'EN PROCESO' order by fecha_registro DESC";
	$palabras = '&palabras=' . $_GET['busqueda'];
} else
	$sql = "SELECT `codigo`, `productos`, `proveedor`, `creador`, `estado`, `fecha_registro`, `observaciones`, `pagos`, `notas`, `factura` FROM `compras` WHERE estado != 'EN PROCESO' order by fecha_registro DESC";

$result = mysqli_query($conexion, $sql);



$nombre_tabla = 'Compras';
?>
<!-- Tabla compras -->
<div class="card">
	<div class="card-body p-2">
		<div class="d-sm-flex align-items-center row m-0 mb-2">
			<div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6">
				<h4 class="card-title"><?php echo $nombre_tabla; ?></h4>
			</div>
			<div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6 text-right">
				<div class="row">
					<input type="text" class="form-control form-control-sm w-auto col" placeholder="Buscar producto" onkeydown="if(event.key=== 'Enter'){buscar_producto_compras(this.value)}" autocomplete="off" value="<?php echo $busqueda2 ?>">
					<button class="btn btn-sm btn-outline-primary ml-auto btn-round col-auto" id="btn_nueva_compra" onclick="click_item('nueva_compra')">
						<i class="icon-plus btn-icon-prepend"></i>NUEVA COMPRA
					</button>
				</div>
			</div>
		</div>
		<table class="table text-dark table-sm table-striped Data_Table" id="tabla_caja" width="100%">
			<thead>
				<tr class="text-center">
					<th width="20px">#</th>
					<th width="20px">Cod</th>
					<th>Proveedor</th>
					<th>Creador</th>
					<th>Total</th>
					<th>Estado</th>
					<th width="150px"></th>
					<th width="20px"></th>
				</tr>
			</thead>
			<tbody class="overflow-auto">
				<?php
				$num_item = 1;

				while ($mostrar = mysqli_fetch_row($result)) {
					$costo_total = 0;
					$codigo = $mostrar[0];

					$productos_compra = array();
					if ($mostrar[1] != '')
						$productos_compra = json_decode($mostrar[1], true);
					$proveedor = array();
					if ($mostrar[2] != '')
						$proveedor = json_decode($mostrar[2], true);
					$notas = array();
					if ($mostrar[8] != '')
						$notas = json_decode($mostrar[8], true);
					$creador = $mostrar[3];
					$estado = $mostrar[4];

					$fecha_registro = date('d-m-Y h:i A', strtotime($mostrar[5]));

					$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
					$result_e = mysqli_query($conexion, $sql_e);
					$ver_e = mysqli_fetch_row($result_e);
					if ($ver_e != null) {
						$nombre_aux = explode(' ', $ver_e[0]);
						$apellido_aux = explode(' ', $ver_e[1]);
						$creador = $nombre_aux[0] . ' ' . $apellido_aux[0];
					}

					foreach ($productos_compra as $i => $item) {
						$cod_producto = $item['codigo'];
						$descripcion = $item['descripcion'];
						$categoria = $item['categoria'];
						$cant = $item['cant'];

						$valor_venta = $item['valor_venta'];
						$costo = $item['costo'];

						$editar = 0;
						if ($cant != '')
							$editar = 1;
						if ($costo > 0)
							$costo_total += $cant * $costo;
					}

					if ($estado == '')
						$estado_button = 'btn-info';

					foreach ($notas as $i => $nota) {
						$tipo = $nota['tipo'];
						$valor = $nota['valor'];

						if ($tipo == 'Nota Crédito')
							$valor *= -1;

						$costo_total += $valor;
					}
				?>
					<tr class="text-dark">
						<td class="text-center p-1"><?php echo $num_item ?></td>
						<td class="text-center p-1"><?php echo str_pad($codigo, 3, "0", STR_PAD_LEFT) ?></td>
						<td class="text-center p-0"><?php echo $proveedor['nombre'] . ' (' . $proveedor['telefono'] . ')' ?></td>
						<td class="text-center p-1"><?php echo $creador ?></td>
						<td class="text-right p-1"><b>$<?php echo number_format($costo_total, 0, '.', '.') ?></b></td>
						<td class="text-center p-1">
							<?php
							if ($estado == '')
								echo '<b class="text-danger">PENDIENTE</b>';
							else if ($estado == 'CRÉDITO')
								echo '<b class="text-warning">CRÉDITO</b>';
							else
								echo '<b>' . $estado . '</b>';
							?>
						</td>
						<td class="text-center p-1">
							<?php
							if ($estado == '') {
							?>
								<button class="btn btn-sm btn-info btn-round px-2" onclick="cambiar_estado_compra('PAGADO','<?php echo $codigo ?>')">
									PAGADO
								</button>
								<button class="btn btn-sm btn-warning btn-round px-2" onclick="cambiar_estado_compra('CRÉDITO','<?php echo $codigo ?>')">
									CRÉDITO
								</button>
							<?php
							}
							if ($estado == 'CRÉDITO') {
							?>
								<button class="btn btn-sm btn-info btn-round px-2" onclick="cambiar_estado_compra('PAGADO','<?php echo $codigo ?>')">
									PAGADO
								</button>
							<?php
							}
							?>
						</td>
						<td class="text-center p-1" width="50px">
							<button class="btn btn-outline-primary btn-round p-0 px-1" data-bs-toggle="modal" data-bs-target="#Modal_Ver" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_ver_compra').load('paginas/detalles/detalles_compra.php/?cod_compra=<?php echo $codigo ?><?php echo $palabras ?>', function(){cerrar_loader();});">
								<span class="fa fa-search"></span>
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
<!-- #END# Tabla compras -->
<script type="text/javascript">
	$(document).ready(function() {
		$('.Data_Table').DataTable({
			responsive: true,
			columns: [{
					responsivePriority: 1
				},
				{
					responsivePriority: 5
				},
				{
					responsivePriority: 2
				},
				{
					responsivePriority: 7
				},
				{
					responsivePriority: 8
				},
				{
					responsivePriority: 4
				},
				{
					responsivePriority: 6
				},
				{
					responsivePriority: 3
				}
			]
		});
	});

	function buscar_producto_compras(busqueda) {
		document.getElementById('div_loader').style.display = 'block';
		input_busqueda = busqueda.replace(/ /g, "***");
		if (input_busqueda != '' && input_busqueda.length > 2)
			$('#div_tabla_compras').load('tablas/compras.php/?busqueda=' + input_busqueda, function() {
				cerrar_loader();
			});
		else
			w_alert({
				titulo: 'Ingrese al menos 3 caracteres',
				tipo: 'danger'
			});
		cerrar_loader();
	}
</script>