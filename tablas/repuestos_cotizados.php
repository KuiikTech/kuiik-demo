<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();

if(isset($_GET['busqueda']))
{
	$busqueda = '%'.$_GET['busqueda'].'%';
	$sql = "SELECT `codigo`, `producto`, `proveedor`, `valor`, `creador`, `estado`, `fecha_registro` FROM `repuestos_cotizados` WHERE productos LIKE '$busqueda' AND  estado != 'EN PROCESO' order by fecha_registro DESC";
}
else
	$sql = "SELECT `codigo`, `producto`, `proveedor`, `valor`, `creador`, `estado`, `fecha_registro` FROM `repuestos_cotizados` WHERE estado != 'EN PROCESO' order by fecha_registro DESC";

$result=mysqli_query($conexion,$sql);

$nombre_tabla = 'Repuestos Cotizados';
?>
<!-- Tabla Repuestos Cotizados -->
<div class="card">
	<div class="card-body p-2">
		<div class="d-sm-flex align-items-center row m-0 mb-2">
			<div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6">
				<h4 class="card-title"><?php echo $nombre_tabla; ?></h4>
			</div>
			<div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6 text-right">
				<div class="row">
					<input type="text" hidden class="form-control form-control-sm w-auto col" placeholder="Buscar producto" onkeydown="if(event.key=== 'Enter'){buscar_producto_repuesto_cotizados(this.value)}" autocomplete="off">
					<button class="btn btn-sm btn-outline-primary ml-auto btn-round col-auto" id="btn_nuevo_repuesto_cotizado" onclick="click_item('nuevo_repuesto_cotizado')">
						<i class="icon-plus btn-icon-prepend"></i>NUEVO REPUESTO COTIZADO
					</button>
				</div>
			</div>
		</div>
		<table class="table text-dark table-sm table-striped Data_Table" id="tabla_caja" width="100%">
			<thead>
				<tr class="text-center">
					<th width="20px">#</th>
					<th width="20px">Cod</th>
					<th>Producto</th>
					<th>Creador</th>
					<th>Total</th>
					<th hidden>Estado</th>
					<th width="150px" hidden></th>
					<th width="20px"></th>
				</tr>
			</thead>
			<tbody class="overflow-auto">
				<?php 
				$num_item = 1;
				
				while ($mostrar=mysqli_fetch_row($result)) 
				{ 
					$costo_total = 0;
					$codigo = $mostrar[0];

					$productos_repuesto_cotizado = array();
					if($mostrar[1] != '')
						$productos_repuesto_cotizado = json_decode($mostrar[1],true);
					$proveedor = array();
					if($mostrar[2] != '')
						$proveedor = json_decode($mostrar[2],true);
					$creador = $mostrar[4];
					$estado = $mostrar[5];

					$fecha_registro = date('d-m-Y h:i A',strtotime($mostrar[5]));

					$sql_e = "SELECT nombre, apellido, rol, foto, color FROM `usuarios` WHERE codigo = '$creador'";
					$result_e=mysqli_query($conexion,$sql_e);
					$ver_e=mysqli_fetch_row($result_e);
					if($ver_e != null)
					{
						$nombre_aux = explode(' ', $ver_e[0]);
						$apellido_aux = explode(' ', $ver_e[1]);
						$creador = $nombre_aux[0].' '.$apellido_aux[0];
					}

					foreach ($productos_repuesto_cotizado as $i => $item)
					{
						$cod_producto = $item['codigo'];
						$descripcion = $item['descripcion'];
						$categoria = $item['categoria'];
						$cant_bp = $item['cant_bp'];
						$cant_b1 = $item['cant_b1'];
						$cant_b2 = $item['cant_b2'];

						$marca = $item['marca'];

						$valor_venta = $item['valor_venta'];
						$valor_venta_mayor = $item['valor_venta_mayor'];
						$costo = $item['costo'];

						$editar = 0;
						if($cant_bp != '')
							$editar = 1;
						if($costo > 0)
							$costo_total = $costo;
					}

					if($estado == '')
						$estado_button = 'btn-info';
					?>
					<tr class="text-dark">
						<td class="text-center p-1"><?php echo $num_item ?></td>
						<td class="text-center p-1"><?php echo str_pad($codigo,3,"0",STR_PAD_LEFT) ?></td>
						<td class="text-center p-0"><?php echo $proveedor['nombre'].' ('.$proveedor['telefono'].')' ?></td>
						<td class="text-center p-1"><?php echo $creador ?></td>
						<td class="text-right p-1"><b>$<?php echo number_format($costo_total,0,'.','.')?></b></td>
						<td class="text-center p-1" hidden>
							<?php 
							if($estado == '')
								echo '<b class="text-danger">PENDIENTE</b>';
							else if($estado == 'CRÉDITO')
								echo '<b class="text-warning">CRÉDITO</b>';
							else
								echo '<b>'.$estado.'</b>';
							?>
						</td>
						<td class="text-center p-1" hidden>
							<?php 
							if($estado == '')
							{
								?>
								<button class="btn btn-sm btn-info btn-round px-2" onclick="cambiar_estado_repuesto_cotizado('PAGADO','<?php echo $codigo ?>')">
									PAGADO
								</button>
								<button class="btn btn-sm btn-warning btn-round px-2" onclick="cambiar_estado_repuesto_cotizado('CRÉDITO','<?php echo $codigo ?>')">
									CRÉDITO
								</button>
								<?php 
							}
							if($estado == 'CRÉDITO')
							{
								?>
								<button class="btn btn-sm btn-info btn-round px-2" onclick="cambiar_estado_repuesto_cotizado('PAGADO','<?php echo $codigo ?>')">
									PAGADO
								</button>
								<?php 
							}
							?>
						</td>
						<td class="text-center p-1" width="50px">
							<button class="btn btn-outline-primary btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Ver" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_ver_repuesto_cotizado').load('paginas/detalles/detalles_repuesto_cotizado.php/?cod_repuesto_cotizado=<?php echo $codigo ?>', function(){cerrar_loader();});">
								<span class="fa fa-search"></span>
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
<!-- #END# Tabla compras -->
<script type="text/javascript">
	$(document).ready(function()
	{
		$('.Data_Table').DataTable(
		{
			responsive: true,
			columns: [
			{ responsivePriority: 1 },
			{ responsivePriority: 5 },
			{ responsivePriority: 2 },
			{ responsivePriority: 7 },
			{ responsivePriority: 8 },
			{ responsivePriority: 4 },
			{ responsivePriority: 6 },
			{ responsivePriority: 3 }
			]
		});
	});

	function buscar_producto_repuesto_cotizados(busqueda)
	{
		document.getElementById('div_loader').style.display = 'block';
		input_busqueda = busqueda.replace(/ /g, "***");
		if(input_busqueda != '' && input_busqueda.length>2)
			$('#div_tabla_repuesto_cotizados').load('tablas/compras.php/?busqueda='+input_busqueda, function(){cerrar_loader();});
		else
			w_alert({ titulo: 'Ingrese al menos 3 caracteres', tipo: 'danger' });
		cerrar_loader();
	}
</script>