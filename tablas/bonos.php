<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();

$sql = "SELECT `codigo`, `cliente`, `beneficiario`, `valor`, `informacion`, `fecha_vencimiento`, `estado`, `fecha_registro` FROM `bonos` order by estado DESC";
$result=mysqli_query($conexion,$sql);

$nombre_tabla = 'Bonos';
?>
<!-- Tabla Bonos -->
<div class="card">
	<div class="card-body p-2">
		<div class="d-sm-flex align-items-center mb-4">
			<h4 class="card-title col"><?php echo $nombre_tabla; ?></h4>
			<div class="col text-right">
				<button class="btn btn-sm btn-outline-primary ml-auto btn-round" data-bs-toggle="modal" data-bs-target="#Modal_Nuevo_Bono">
					<span class="fas fa-plus"></span>Nuevo Bono
				</button>
			</div>
		</div>
		<table width="100%" class="table text-dark table-sm" id="tabla_bonos">
			<thead>
				<tr class="text-center">
					<th>#</th>
					<th>Cod</th>
					<th>Cliente/Beneficiario</th>
					<th width="70px">Valor</th>
					<th>Servicios</th>
					<th>Fecha Compra</th>
					<th>Vencimiento</th>
					<th>Estado</th>
					<th></th>
				</tr>
			</thead>
			<div class="overflow-auto">
				<tbody class="overflow-auto">
					<?php 
					$num_item = 1;
					while ($mostrar=mysqli_fetch_row($result)) 
					{ 
						$cod_bono = str_pad($mostrar[0],4,"0",STR_PAD_LEFT);
						$cod_cliente = $mostrar[1];
						$cod_beneficiario = $mostrar[2];

						$lista_servicios_bono = array();

						if($mostrar[4] != '')
							$lista_servicios_bono = json_decode($mostrar[4],true);

						$valor_bono = '$ '.number_format($mostrar[3],0,'.','.');
						$estado_bono = $mostrar[6];

						$fecha_compra = date('d-m-Y',strtotime($mostrar[7])).'<br> <b>'.date('h:i a',strtotime($mostrar[7])).'</b>';
						$fecha_ven = date('d-m-Y',strtotime($mostrar[5]));
						if ($estado_bono=='VIGENTE')
							$bg_estado = 'bg-green';
						if ($estado_bono=='VENCIDO')
							$bg_estado = 'bg-red';
						if ($estado_bono=='COBRADO')
							$bg_estado = 'bg-info';

						$sql_cliente = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `telefono`, `direccion`, `fecha_registro` FROM `clientes` WHERE  codigo = '$cod_cliente'";
						$result_cliente=mysqli_query($conexion,$sql_cliente);
						$ver_cliente=mysqli_fetch_row($result_cliente);
						if($ver_cliente != null)
							$cliente = ucwords(mb_strtolower($ver_cliente[2].' '.$ver_cliente[3]));
						else
							$cliente = '';

						$sql_cliente = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `telefono`, `direccion`, `fecha_registro` FROM `clientes` WHERE  codigo = '$cod_beneficiario'";
						$result_cliente=mysqli_query($conexion,$sql_cliente);
						$ver_cliente=mysqli_fetch_row($result_cliente);
						$beneficiario = ucwords(mb_strtolower($ver_cliente[2].' '.$ver_cliente[3]));
						?>
						<tr>
							<td class="text-center"><?php echo $num_item ?></td>
							<td class="text-center"><?php echo $cod_bono ?></td>
							<td>
								C: <?php echo utf8_decode($cliente) ?>
								<br>
								<b>B: <?php echo utf8_decode($beneficiario) ?></b>
							</td>
							<td class="text-center"><h4><?php echo $valor_bono ?></h4></td>
							<td>
								<?php 
								if(isset($lista_servicios_bono['servicios']))
								{
									$servicios_bono = $lista_servicios_bono['servicios'];
									foreach ($servicios_bono as $i => $servicio)
									{
										$codigo = $servicio['codigo'];
										$categoria = $servicio['categoria'];
										$descripcion = $servicio['descripcion'];
										$valor = $servicio['valor'];

										echo '- '.$descripcion.' ($'.number_format($valor,0,'.','.').')<br>';
									}
								}
								?>
							</td>
							<td class="text-center"><?php echo $fecha_compra ?></td>
							<td class="text-center"><?php echo $fecha_ven ?></td>
							<td class="text-center <?php echo $bg_estado ?>" ><?php echo $estado_bono ?></td>
							<td class="text-center">
								<?php 
								if($estado_bono != 'COBRADO')
								{
									?>
									<button class="btn btn-sm btn-outline-warning btn-round p-1" data-bs-toggle="modal" data-bs-target="#modal_posponer" onclick="$('#cod_bono').val('<?php echo $cod_bono ?>');">
										<i class="material-icons-two-tone">more_time</i>
									</button>
									<?php 
								}
								?>
							</td>
						</tr>
						<?php 
						$num_item++;
					} 
					?>
				</tbody>
			</div>
		</table>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function()
	{
		$('#tabla_bonos').DataTable(
		{
			responsive: true,
			columnDefs: [
			{ responsivePriority: 2, targets: 0 },
			{ responsivePriority: 4, targets: 1 },
			{ responsivePriority: 1, targets: 2 },
			{ responsivePriority: 3, targets: 3 },
			{ responsivePriority: 5, targets: 4 },
			{ responsivePriority: 9, targets: 5 },
			{ responsivePriority: 6, targets: 6 },
			{ responsivePriority: 7, targets: 7 },
			{ responsivePriority: 8, targets: 8 }
			]
		});
	});

</script>