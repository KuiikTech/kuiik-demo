<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();
session_set_cookie_params(7*24*60*60);
session_start();

$cod_espacio = $_GET['cod_espacio'];

$sql_espacio = "SELECT `codigo`, `nombre`, `items`, `fecha_creacion`, `cod_cliente`, `pagos`, `informacion`, `caja` FROM `espacios` WHERE codigo = '$cod_espacio'";
$result_espacio=mysqli_query($conexion,$sql_espacio);
$mostrar_espacio=mysqli_fetch_row($result_espacio);

$items = array();
if($mostrar_espacio[2] != '')
	$items = json_decode($mostrar_espacio[2],true);

?>
<div class="text-center">
	<h5>Daños del equipo</h5>
</div>
<div class="table-responsive text-dark text-center py-0 px-1">
	<table width="100%" class="table text-dark table-sm" id="tabla_daños">
		<thead>
			<tr class="text-center">
				<th width="10px" class="table-plus text-dark datatable-nosort px-1">#</th>
				<th width="auto" class="px-1"><span class="requerido">*</span>Daño</th>
				<th class="px-1">Observaciones</th>
				<th width="10px"></th>
			</tr>
		</thead>
		<tbody class="overflow-auto">
			<?php 
			$num_item = 1;
			$total = 0;
			foreach ($items as $i => $item)
			{
				$daño = $item['daño'];
				$observaciones = $item['observaciones'];

				$sql_daño = "SELECT `codigo`, `nombre`, `estado`, `fecha_creacion`, `creador` FROM `tipo_daños` WHERE codigo = '$daño'";
				$result_daño=mysqli_query($conexion,$sql_daño);
				$ver_daño=mysqli_fetch_row($result_daño);

				if($ver_daño != null)
					$daño = $ver_daño[1];
				?>
				<tr role="row" class="odd">
					<td class="text-center p-0 text-muted"><?php echo $num_item ?></td>
					<td class="text-center p-0"><b><?php echo $daño ?></b></td>
					<td class="text-left p-0"><b><?php echo $observaciones ?></b></td>
					<td class="text-center p-0">
						<a class="btn btn-sm btn-outline-danger btn-round p-0 px-1" onclick="eliminar_item(<?php echo $i ?>);">
							<span class="fa fa-trash"></span>
						</a>
					</td>
				</tr>
				<?php 
				$num_item ++;
			} 
			?>
			<tr>
				<td class="text-center"><?php echo $num_item ?></td>
				<td class="px-1">
					<select class="form-control form-control-sm" name="input_daño" id="input_daño">
						<option value="">Selec. tipo de daño</option>
						<?php 
						$sql_tipo_daños = "SELECT `codigo`, `nombre`, `estado`, `fecha_creacion`, `creador` FROM `tipo_daños` WHERE estado = 'ACTIVO'";
						$result_tipo_daños=mysqli_query($conexion,$sql_tipo_daños);
						while ($mostrar_tipo_daños=mysqli_fetch_row($result_tipo_daños))
						{
							$nombre = $mostrar_tipo_daños[1];
							if($tipo_equipo == $mostrar_tipo_daños[0])
								$selecionado = 'selected';
							else
								$selecionado = '';
							?>
							<option value="<?php echo $mostrar_tipo_daños[0] ?>" <?php echo $selecionado ?>><?php echo $nombre ?></option>
							<?php 
						}
						?>
					</select>
				</td>
				<td class="px-1">
					<textarea class="form-control form-control-sm" name="input_observacion" id="input_observacion" autocomplete="off" rows="1" placeholder="Observaciones del daño"></textarea>
				</td>
				<td>
					<button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_agregar_item_servicio">Agregar</button>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<script type="text/javascript">

	$('input.moneda').keyup(function(event)
	{
		if(event.which >= 37 && event.which <= 40)
		{
			event.preventDefault();
		}
		$(this).val(function(index, value)
		{
			return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
		});
	});

	$('#input_observacion').keypress(function(e){
		if(e.keyCode==13)
			$('#btn_agregar_item_servicio').click();
	});

	$('#btn_agregar_item_servicio').click(function()
	{
		document.getElementById('div_loader').style.display = 'block';
		document.getElementById("btn_agregar_item_servicio").disabled = true;
		input_observacion = document.getElementById("input_observacion").value;
		input_daño = document.getElementById("input_daño").value;
		if(input_daño != '')
		{
			$.ajax({	
				type:"POST",
				data:"cod_espacio=<?php echo $cod_espacio ?>&input_observacion=" + input_observacion+"&input_daño=" + input_daño,
				url:"procesos/agregar_item_servicio.php",
				success:function(r)
				{
					datos=jQuery.parseJSON(r);
					if(datos['consulta'] == 1)
					{
						w_alert({ titulo: 'Item agregado con exito', tipo: 'success' });
						$('#tabla_items').load('tablas/items_servicio.php/?cod_espacio=<?php echo $cod_espacio ?>', function(){cerrar_loader();});
					}
					else
					{
						w_alert({ titulo: datos['consulta'], tipo: 'danger' });
						if(datos['consulta'] == 'Reload')
						{
							document.getElementById('div_login').style.display = 'block';
cerrar_loader();
							
						}
						if(datos['consulta'] == 'Reload')
						{
							document.getElementById('div_login').style.display = 'block';
cerrar_loader();
							
						}
					}
				}
			});
		}
		else
		{
			w_alert({ titulo: 'Seleccione el daño', tipo: 'danger' });
			document.getElementById("input_daño").focus();
		}

		cerrar_loader();
		document.getElementById("btn_agregar_item_servicio").disabled = false;
	});

	function eliminar_item(num_item)
	{
		document.getElementById('div_loader').style.display = 'block';
		$.ajax({
			type:"POST",
			data:"cod_espacio=<?php echo $cod_espacio ?>&num_item="+num_item,
			url:"procesos/eliminar_item_servicio.php",
			success:function(r)
			{
				datos=jQuery.parseJSON(r);
				if (datos['consulta'] == 1)
				{
					w_alert({ titulo: 'Item eliminado', tipo: 'success' });
					document.getElementById('div_loader').style.display = 'block';
					$('#tabla_items').load('tablas/items_servicio.php/?cod_espacio=<?php echo $cod_espacio ?>', function(){cerrar_loader();});
				}
				else
				{
					w_alert({ titulo: datos['consulta'], tipo: 'danger' });
					if(datos['consulta'] == 'Reload')
					{
						document.getElementById('div_login').style.display = 'block';
cerrar_loader();
						
					}
					if(datos['consulta'] == 'Reload')
					{
						document.getElementById('div_login').style.display = 'block';
cerrar_loader();
						
					}
				}

				cerrar_loader();
			}
		});
	}
</script>