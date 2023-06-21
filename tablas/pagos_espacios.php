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

$pagos_espacio = array();
if($mostrar_espacio[5] != '')
	$pagos_espacio = json_decode($mostrar_espacio[5],true);

$informacion = array();
if($mostrar_espacio[6] != '')
	$informacion = json_decode($mostrar_espacio[6],true);

if(isset($informacion['total_servicios']))
	$total_servicios = $informacion['total_servicios'];
else
	$total_servicios = 0;

?>
<div class="text-center mt-5">
	<div class="row pb-1">
		<label class="col-sm-7 col-form-label py-1"><h4>Total servicios:<span class="requerido">*</span></h4></label>
		<div class="col-sm-5">
			<input type="text" class="form-control form-control-sm moneda text-right" id="total_servicios" name="total_servicios" placeholder="Total en servicios" onchange="guardar_info_add('total_servicios',this.value)" value="<?php echo number_format($total_servicios,0,'.','.') ?>" autocomplete="off">
		</div>
	</div>
</div>
<div class="text-center">
	<hr>
	<h4>Pagos / Abonos</h4>
</div>
<div class="table-responsive text-dark text-center py-0 px-1">
	<table width="100%" class="table text-dark table-sm" id="tabla_pagos_espacio">
		<thead>
			<tr class="text-center">
				<th width="30px" class="table-plus text-dark datatable-nosort px-1">#</th>
				<th style="min-width: 150px;" class="px-1">Método</th>
				<th style="min-width: 150px;">Valor</th>
				<th width="30px"></th>
			</tr>
		</thead>
		<tbody class="overflow-auto">
			<?php 
			$num_item = 1;
			$total_pagos = 0;
			foreach ($pagos_espacio as $i => $item)
			{
				$tipo = $item['tipo'];
				$valor = $item['valor'];

				$total_pagos += $valor;
				?>
				<tr role="row" class="odd">
					<td class="text-center p-1 text-muted"><?php echo $num_item ?></td>
					<td class="text-left p-1"><?php echo $tipo ?></td>
					<td class="text-right p-1"><b>$<?php echo number_format($valor,0,'.','.') ?></b></td>
					<td class="text-center p-1">
						<a class="btn btn-sm btn-outline-danger btn-round p-0 px-1" onclick="eliminar_item_pago(<?php echo $i ?>);">
							<span class="fa fa-trash"></span>
						</a>
					</td>
				</tr>
				<?php 
				$num_item ++;
			} 
			$text_saldo = 'text-danger';

			$saldo = $total_servicios - $total_pagos;

			if($saldo == 0)
				$text_saldo = 'text-success';
			?>
			<tr>
				<td class="text-center p-1 text-muted"><?php echo $num_item ?></td>
				<td class="text-center">
					<select class="form-control form-control-sm" id="input_metodo_pago" name="input_metodo_pago">
						<option value="">Seleccione uno...</option>
						<option value="Efectivo">Efectivo</option>
						<option value="Tarjeta">Tarjeta</option>
						<option value="Nequi">Nequi</option>
						<option value="Bancolombia">Bancolombia</option>
						<option value="Daviplata">Daviplata</option>
						<option value="Crédito">Crédito</option>
					</select>
				</td>
				<td class="text-center">
					<input type="text" class="form-control form-control-sm moneda" id="input_valor_pago" name="input_valor_pago" placeholder="Valor" autocomplete="off">
				</td>
				<td class="text-center">
					<button type="button" class="btn btn-sm btn-outline-success btn-round p-0 px-1" id="btn_agregar_pago_espacio">+</button>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="text-right p-1"><b>Total Pagos</b></td>
				<td class="text-right p-1 h4" width="120px"><b>$<?php echo number_format($total_pagos,0,'.','.'); ?></b></td>
				<td></td>
			</tr>
			<tr>
				<td colspan="2" class="text-right p-1"><b>Saldo</b></td>
				<td class="text-right p-1 h4 <?php echo $text_saldo ?>" width="120px"><b>$<?php echo number_format($saldo,0,'.','.'); ?></b></td>
				<td></td>
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

	$('#input_valor_pago').keypress(function(e){
		if(e.keyCode==13)
			$('#btn_agregar_pago_espacio').click();
	});

	$('#btn_agregar_pago_espacio').click(function()
	{
		document.getElementById('div_loader').style.display = 'block';
		document.getElementById("btn_agregar_pago_espacio").disabled = true;
		input_metodo_pago = document.getElementById("input_metodo_pago").value;
		input_valor_pago = document.getElementById("input_valor_pago").value;
		if(input_metodo_pago != '' && input_valor_pago != '')
		{
			$.ajax({	
				type:"POST",
				data:"cod_espacio=<?php echo $cod_espacio ?>&input_metodo_pago=" + input_metodo_pago+"&input_valor_pago=" + input_valor_pago,
				url:"procesos/agregar_pago_espacio.php",
				success:function(r)
				{
					datos=jQuery.parseJSON(r);
					if(datos['consulta'] == 1)
					{
						w_alert({ titulo: 'Pago agregado con exito', tipo: 'success' });
						$('#tabla_pagos').load('tablas/pagos_espacios.php/?cod_espacio=<?php echo $cod_espacio ?>', function(){cerrar_loader();});
						setTimeout("$('#input_metodo').focus()",300);
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
			if(input_metodo_pago == '')
			{
				w_alert({ titulo: 'Seleccione el metodo de pago', tipo: 'danger' });
				document.getElementById("input_metodo_pago").focus();
			}
			else if(input_valor_pago == '')
			{
				w_alert({ titulo: 'Ingrese el valor del pago', tipo: 'danger' });
				document.getElementById("input_valor_pago").focus();
			}
		}

		cerrar_loader();
		document.getElementById("btn_agregar_pago_espacio").disabled = false;
	});

	function eliminar_item_pago(item)
	{
		document.getElementById('div_loader').style.display = 'block';
		$.ajax({
			type:"POST",
			data:"cod_espacio=<?php echo $cod_espacio ?>&item="+item,
			url:"procesos/eliminar_pago_espacio.php",
			success:function(r)
			{
				datos=jQuery.parseJSON(r);
				if (datos['consulta'] == 1)
				{
					w_alert({ titulo: 'Item eliminado', tipo: 'success' });
					document.getElementById('div_loader').style.display = 'block';
					$('#tabla_pagos').load('tablas/pagos_espacios.php/?cod_espacio=<?php echo $cod_espacio ?>', function(){cerrar_loader();});
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