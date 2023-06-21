<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();

$sql = "SELECT `codigo`, `cliente`, `servicio`, `cotizó`, `creador`, `fecha_registro`, `observaciones`, `estado` FROM `cotizaciones` order by codigo ASC, FIELD(estado,'PENDIENTE','DESPACHADO','CANCELADO')";
$result=mysqli_query($conexion,$sql);

$nombre_tabla = 'Cotizaciones';
?>
<!-- Tabla Cotizaciones -->
<div class="card">
	<div class="card-body p-2">
		<div class="d-sm-flex align-items-center mb-4">
			<h4 class="card-title col"><?php echo $nombre_tabla; ?></h4>
			<div class="col text-right">
				<button class="btn btn-sm btn-outline-primary ml-auto btn-round" data-bs-toggle="modal" data-bs-target="#Modal_Nueva_Cotizacion" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_cot_cliente').load('detalles/cliente_agregar_cot.php', function(){cerrar_loader();});">
					<span class="fas fa-plus"></span>Nueva Cotizacion
				</button>
			</div>
		</div>
		<table width="100%" class="table text-dark table-sm" id="tabla_cotizacion">
			<thead>
				<tr class="text-center">
					<th class="p-1">Cod</th>
					<th class="p-1">Cliente</th>
					<th class="table-plus text-dark datatable-nosort p-1">Servicio</th>
					<th width="100px" class="p-1">Valor</th>
					<th class="p-1">Cotizó</th>
					<th width="100px" class="p-1">Estado</th>
					<th width="30px" class="p-1"></th>
				</tr>
			</thead>
			<tbody class="overflow-auto">
				<?php 
				while ($mostrar=mysqli_fetch_row($result)) 
				{ 
					$cod_cotizacion = $mostrar[0];
					$cod_cliente = $mostrar[1];
					$servicio = $mostrar[2];
					$recurso = $mostrar[3];
					$creador = $mostrar[4];
					$fecha_registro = $mostrar[5];
					$observaciones = $mostrar[6];
					$estado = $mostrar[7];

					$servicio = array();
					if ($mostrar[2] != '')
						$servicio = json_decode($mostrar[2],true);

					$sql_2 = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `telefono`, `direccion`, `fecha_registro` FROM `clientes` WHERE codigo = '$cod_cliente'";
					$result_2=mysqli_query($conexion,$sql_2);
					$ver=mysqli_fetch_row($result_2);

					$cliente = array(
						'codigo' => $ver[0], 
						'id' => $ver[1], 
						'nombre' => $ver[2].' '.$ver[3], 
						'telefono' => $ver[4], 
						'direccion' => $ver[5]
					);

					$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$recurso'";
					$result_e=mysqli_query($conexion,$sql_e);
					$ver_e=mysqli_fetch_row($result_e);
					$nombre_aux = explode(' ', $ver_e[0]);
					$apellido_aux = explode(' ', $ver_e[1]);
					$recurso = $nombre_aux[0].' '.$apellido_aux[0];

					if($estado == 'AGENDADO')
						$estado_button = 'btn-success';
					if($estado == 'PENDIENTE')
						$estado_button = 'btn-danger';
					if($estado == 'REALIZADO')
						$estado_button = 'btn-primary';

					?>
					<tr role="row" class="odd">
						<td class="p-1 text-center"><?php echo str_pad($cod_cotizacion,1,"0",STR_PAD_LEFT) ?></td>
						<td class="p-0 align-middle p-0">
							<?php echo $cliente['nombre'] ?>
							<br>
							<small>Tel:<b><?php echo $cliente['telefono'] ?></b></small>
						</td>
						<td><b><?php echo $servicio['descripcion'] ?></b></td>
						<td class="p-1 text-right"><strong>$<?php echo number_format($servicio['valor'],0,'.','.')?></strong></td>
						<td class="p-1 align-middle"><?php echo $recurso ?></td>
						<td class="p-1 text-center">
							<button class="btn btn-sm <?php echo $estado_button ?> btn-round px-2" id="btn_estado_<?php echo $cod_cotizacion ?>" onclick="cambiar_estado('<?php echo $cod_cotizacion ?>')">
								<?php echo $estado ?>
							</button>
						</td>
						<td class="p-1 text-center">
							<button class="btn btn-sm btn-outline-info btn-round p-1" onclick="imprimir_cotizacion('<?php echo $cod_cotizacion ?>')">
								<i class="material-icons-two-tone">print</i>
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

<script type="text/javascript">
	$(document).ready(function()
	{
		$('#tabla_cotizacion').DataTable(
		{
			responsive: true,
			columnDefs: [
			{ responsivePriority: 1, targets: 0 },
			{ responsivePriority: 5, targets: 1 },
			{ responsivePriority: 1, targets: 2 },
			{ responsivePriority: 3, targets: 3 },
			{ responsivePriority: 4, targets: 4 },
			{ responsivePriority: 2, targets: 5 }
			]
		});
	});
</script>