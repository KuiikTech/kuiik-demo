<div class="table-responsive text-dark text-center py-0 px-1">
	<table width="100%" class="table text-dark table-sm w-100" id="tabla_inventario_<?php echo $codigo ?>">
		<thead>
			<tr class="text-center">
				<th class="p-1 px-4">#</th>
				<th class="p-1">Stock</th>
				<th class="p-1">Valor Venta (<?php echo $unidades ?>)</th>
				<th class="p-1">Inicial</th>
				<th class="p-1">Costo (<?php echo $unidades ?>)</th>
				<th class="p-1">Creador</th>
				<th class="p-1"></th>
			</tr>
		</thead>
		<tbody class="overflow-auto text-dark">
			<?php 
			$total_inventario = 0;
			$num_item = 1;
			foreach ($inventario as $i => $insumo)
			{
				$costo = $insumo['costo'];
				$valor_venta = $insumo['valor_venta'];
				$creador = $insumo['creador'];
				$cant_inicial = $insumo['cant_inicial'];
				$stock = $insumo['stock'];

				if($stock>0)
				{
					$movimientos = array();
					if ($insumo['movimientos'] != '')
						$movimientos = $insumo['movimientos'];

					$total_inventario += $stock;

					$fecha_registro = strftime("%A, %e %b %Y", strtotime($insumo['fecha_registro']));
					$fecha_registro = ucfirst(iconv("ISO-8859-1","UTF-8",$fecha_registro));

					$fecha_registro .= date(' | h:i A',strtotime($insumo['fecha_registro']));

					$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
					$result_e=mysqli_query($conexion,$sql_e);
					$ver_e=mysqli_fetch_row($result_e);
					$nombre_aux = explode(' ', $ver_e[0]);
					$apellido_aux = explode(' ', $ver_e[1]);
					$creador = $nombre_aux[0].' '.$apellido_aux[0];

					?>
					<tr>
						<td class="p-1 py-0 text-center text-dark"><?php echo $num_item ?></td>
						<td class="p-1 py-0 text-truncate text-dark h3"><?php echo $stock ?></td>
						<td class="p-1 py-0 text-right text-dark"><strong>$<?php echo number_format($valor_venta,0,'.','.')?></strong></td>
						<td class="p-1 py-0 text-truncate text-dark"><?php echo $cant_inicial ?></td>
						<td class="p-1 py-0 text-right text-dark"><strong>$<?php echo number_format($costo,0,'.','.')?></strong></td>
						<td class="p-1 py-0 text-dark text-truncate">
							<b><?php echo $creador ?></b>
							<br>
							<span class="badge bg-light-primary text-dark"><?php echo $fecha_registro ?></span>
						</td>
						<td class="p-1 py-0 text-center text-dark">
							<button class="btn btn-sm btn-outline-primary btn-round p-1" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_operacion').load('tablas/mov_insumo.php/?cod_insumo=<?php echo $codigo ?>&pos=<?php echo $i ?>', function(){cerrar_loader();});">
								<i class="material-icons-two-tone">pending_actions</i>
							</button>
						</td>
					</tr>
					<?php 
					$num_item ++;
				}
			}
			if($num_item == 1)
			{
				?>
				<tr>
					<td colspan="7" class="text-center p-1">No existen inventario.</td>
				</tr>
				<?php 
			}
			?>
			<tr id="tr_add_stock">
				<td class="p-1">
					<a class="p-1" href="javascript:document.getElementById('tr_nuevo_stock').hidden = false;document.getElementById('tr_add_stock').hidden = true;">
						<span class="fas fa-plus"></span>
					</a>
				</td>
			</tr>
			<tr id="tr_nuevo_stock" hidden="">
				<td class="text-center p-1">
					<a class="p-1" href="javascript:document.getElementById('tr_nuevo_stock').hidden = true;document.getElementById('tr_add_stock').hidden = false;">
						<i class="material-icons-two-tone">remove</i>
					</a>
				</td>
				<td class="p-1"></td>
				<td class="p-1">
					<input type="text" class="form-control form-control-sm moneda text-right" name="input_valor_venta" id="input_valor_venta" >
				</td>
				<td class="p-1">
					<input type="text" class="form-control form-control-sm text-center" name="input_stock_inicial" id="input_stock_inicial" >
				</td>
				<td class="p-1">
					<input type="text" class="form-control form-control-sm moneda text-right" name="input_costo" id="input_costo" >
				</td>
				<td class="p-1" colspan="2">
					<button class="btn btn-sm btn-outline-primary btn-round p-1" id="btn_agregar_stock">
						<i class="material-icons-two-tone">save</i> Guardar
					</button>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div class="row m-0 p-0">
	<h3>Total Inventario: <?php echo $total_inventario ?></h3>
</div>

<div class="row m-0 p-0" id="div_operacion"></div>

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

	$('#input_costo').keypress(function(e){
		if(e.keyCode==13)
			$('#btn_agregar_stock').click();
	});

	$('#btn_agregar_stock').click(function()
	{
		document.getElementById('div_loader').style.display = 'block';
		document.getElementById("btn_agregar_stock").disabled = true;
		input_valor_venta = document.getElementById("input_valor_venta").value;
		input_stock_inicial = document.getElementById("input_stock_inicial").value;
		input_costo = document.getElementById("input_costo").value;
		if(input_valor_venta != '' && input_stock_inicial != '' && input_costo != '')
		{
			$.ajax({	
				type:"POST",
				data:"cod_insumo=<?php echo $codigo ?>&&input_valor_venta=" + input_valor_venta+"&input_stock_inicial=" + input_stock_inicial+"&input_costo=" + input_costo,
				url:"procesos/agregar_stock_insumo.php",
				success:function(r)
				{
					datos=jQuery.parseJSON(r);
					if(datos['consulta'] == 1)
					{
						w_alert({ titulo: 'Stock agregado correctamente', tipo: 'success' });
						$('#div_modal_insumo').load('detalles/detalles_insumo.php/?cod_insumo=<?php echo $codigo ?>', function(){cerrar_loader();});
						stock_old = parseInt(document.getElementById("td_stock_<?php echo $codigo ?>").innerHTML);

						document.getElementById("td_stock_<?php echo $codigo ?>").innerHTML = parseInt(stock_old) + parseInt(input_stock_inicial);
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
			if(input_valor_venta == '')
			{
				w_alert({ titulo: 'Ingrese el valor de venta del insumo', tipo: 'danger' });
				document.getElementById("input_valor_venta").focus();
			}
			else if(input_stock_inicial == '')
			{
				w_alert({ titulo: 'Ingrese la cantidad de insumo', tipo: 'danger' });
				document.getElementById("input_stock_inicial").focus();
			}
			else if(input_costo == '')
			{
				w_alert({ titulo: 'Ingrese el costo del insumo', tipo: 'danger' });
				document.getElementById("input_costo").focus();
			}
		}

		cerrar_loader();
		document.getElementById("btn_agregar_stock").disabled = false;
	});

</script>