<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
session_set_cookie_params(7*24*60*60);
session_start();

$cod_espacio = $_GET['cod_espacio'];

$sql_espacio = "SELECT `codigo`, `nombre`, `items`, `fecha_creacion`, `cod_cliente`, `pagos`, `informacion`, `caja` FROM `espacios` WHERE codigo = '$cod_espacio'";
$result_espacio=mysqli_query($conexion,$sql_espacio);
$mostrar_espacio=mysqli_fetch_row($result_espacio);

$informacion = array();
if($mostrar_espacio[6] != '')
	$informacion = json_decode($mostrar_espacio[6],true);

$items = array();
if(isset($informacion['seguridad']))
	$items = $informacion['seguridad']
?>
<div class="text-center">
	<h5>Seguridad del equipo</h5>
</div>
<div class="table-responsive text-dark text-center py-0 px-1">
	<table width="100%" class="table text-dark table-sm" id="tabla_seguridad">
		<thead>
			<tr class="text-center">
				<th width="30px" class="table-plus text-dark datatable-nosort px-1">#</th>
				<th width="250px" class="px-1"><span class="requerido">*</span>Tipo</th>
				<th class="px-1">Valor</th>
				<th width="10px"></th>
			</tr>
		</thead>
		<tbody class="overflow-auto">
			<?php 
			$num_item = 1;
			foreach ($items as $i => $item)
			{
				$tipo_seguridad = $item['tipo_seguridad'];
				$valor = $item['valor'];
				?>
				<tr role="row" class="odd">
					<td class="text-center p-0 text-muted"><?php echo $num_item ?></td>
					<td class="text-center p-0"><b><?php echo $tipo_seguridad ?></b></td>
					<td class="text-center p-0"><b><?php echo $valor ?></b></td>
					<td class="text-center p-0">
						<a class="btn btn-sm btn-outline-danger btn-round p-0 px-1" onclick="eliminar_item_seguridad(<?php echo $i ?>);">
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
					<select class="form-control form-control-sm" name="input_seguridad" id="input_seguridad" onchange="cambio_select_seguridad(this.value)">
						<option value="">Selec. tipo de seguridad</option>
						<option value="Contraseña">Contraseña</option>
						<option value="PIN">PIN</option>
						<option value="Patron">Patron</option>
						<option value="Contraseña Aplicaciones">Contraseña Aplicaciones</option>
						<option value="Patron Aplicaciones">Patron Aplicaciones</option>
					</select>
				</td>
				<td class="px-1">
					<input type="text" class="form-control form-control-sm" name="input_valor_seguridad" id="input_valor_seguridad" autocomplete="off">
				</td>
				<td>
					<button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_agregar_item_seguridad">Agregar</button>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<!-- Modal QR patron-->
<div class="modal fade" id="Modal_qr_patron" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="overflow-y: scroll;">
	<div class="modal-dialog" role="document" style="width:25% !important">
		<div class="modal-content" id="div_qr_patron"></div>
	</div>
</div>

<script type="text/javascript">

	function cambio_select_seguridad(seleccion)
	{   
		if(seleccion == 'Patron')
		{
			$('#Modal_qr_patron').modal('show');
			document.getElementById('div_loader').style.display = 'block';
			$('#div_qr_patron').load('paginas/detalles/qr_patron.php/?cod_espacio=<?php echo $cod_espacio ?>&tipo=Patron', cerrar_loader());
		}
		if(seleccion == 'Patron Aplicaciones')
		{
			$('#Modal_qr_patron').modal('show');
			document.getElementById('div_loader').style.display = 'block';
			$('#div_qr_patron').load('paginas/detalles/qr_patron.php/?cod_espacio=<?php echo $cod_espacio ?>&tipo=Patron_Aplicaciones', cerrar_loader());
		}
	}

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
			$('#btn_agregar_item_seguridad').click();
	});

	$('#btn_agregar_item_seguridad').click(function()
	{
		document.getElementById('div_loader').style.display = 'block';
		document.getElementById("btn_agregar_item_seguridad").disabled = true;
		input_valor_seguridad = document.getElementById("input_valor_seguridad").value;
		input_seguridad = document.getElementById("input_seguridad").value;
		if(input_seguridad != '' && input_valor_seguridad != '')
		{
			$.ajax({	
				type:"POST",
				data:"cod_espacio=<?php echo $cod_espacio ?>&input_valor_seguridad=" + input_valor_seguridad+"&input_seguridad=" + input_seguridad,
				url:"procesos/agregar_seguridad_servicio.php",
				success:function(r)
				{
					datos=jQuery.parseJSON(r);
					if(datos['consulta'] == 1)
					{
						w_alert({ titulo: 'Item agregado con exito', tipo: 'success' });
						$('#tabla_seguridad').load('tablas/seguridad_equipo.php/?cod_espacio=<?php echo $cod_espacio ?>', function(){cerrar_loader();});
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
			if(input_seguridad == '')
			{
				w_alert({ titulo: 'Seleccione el tipo de seguridad', tipo: 'danger' });
				document.getElementById("input_seguridad").focus();
			}
			else if(input_valor_seguridad == '')
			{
				w_alert({ titulo: 'Seleccione el tipo de seguridad', tipo: 'danger' });
				document.getElementById("input_valor_seguridad").focus();
			}
		}

		cerrar_loader();
		document.getElementById("btn_agregar_item_seguridad").disabled = false;
	});

	function eliminar_item_seguridad(num_item)
	{
		document.getElementById('div_loader').style.display = 'block';
		$.ajax({
			type:"POST",
			data:"cod_espacio=<?php echo $cod_espacio ?>&num_item="+num_item,
			url:"procesos/eliminar_seguridad_servicio.php",
			success:function(r)
			{
				datos=jQuery.parseJSON(r);
				if (datos['consulta'] == 1)
				{
					w_alert({ titulo: 'Item eliminado', tipo: 'success' });
					document.getElementById('div_loader').style.display = 'block';
					$('#tabla_seguridad').load('tablas/seguridad_equipo.php/?cod_espacio=<?php echo $cod_espacio ?>', function(){cerrar_loader();});
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