<div class="table-responsive text-dark text-center py-0 px-1">
	<table class="table text-dark table-sm w-100" id="tabla_inventario_<?php echo $codigo ?>">
		<thead>
			<tr class="text-center">
				<th width="10px" class="p-1">#</th>
				<th class="p-1">Stock</th>
				<th class="p-1">Valor Público</th>
				<th class="p-1">Valor Mayor</th>
				<th class="p-1">Inicial</th>
				<th class="p-1">Costo</th>
				<th class="p-1">Creador</th>
				<th width="80px" class="p-1"></th>
			</tr>
		</thead>
		<tbody class="overflow-auto text-dark">
			<?php 
			$total_inventario = 0;
			$num_item = 1;
			foreach ($inventario as $i => $producto)
			{
				$costo = $producto['costo'];
				$valor_venta = $producto['valor_venta'];
				if(isset($producto['valor_venta_mayor']))
					$valor_venta_mayor = $producto['valor_venta_mayor'];
				else
					$valor_venta_mayor = 0;
				$creador = $producto['creador'];
				$cant_inicial = $producto['cant_inicial'];
				$stock = $producto['stock'];

				$movimientos = array();
				if ($producto['movimientos'] != '')
					$movimientos = $producto['movimientos'];

				$total_inventario += $stock;

				$fecha_registro = strftime("%A, %e %b %Y", strtotime($producto['fecha_registro']));
				$fecha_registro = ucfirst(iconv("ISO-8859-1","UTF-8",$fecha_registro));

				$fecha_registro .= date(' | h:i A',strtotime($producto['fecha_registro']));

				$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
				$result_e=mysqli_query($conexion,$sql_e);
				$ver_e=mysqli_fetch_row($result_e);

				if($ver_e != NULL)
				{
					$nombre_aux = explode(' ', $ver_e[0]);
					$apellido_aux = explode(' ', $ver_e[1]);
					$creador = $nombre_aux[0].' '.$apellido_aux[0];
				}
				else
					$creador = '?';

				$marca = '';
				$proveedor = '';

				if(isset($producto['marca']))
					$marca = $producto['marca'];

				if(isset($producto['proveedor']))
					$proveedor = $producto['proveedor'];

				?>
				<tr>
					<td class="p-1 py-0 text-center text-dark" rowspan="2"><?php echo $num_item ?></td>
					<td class="p-1 py-0 text-truncate text-dark h3"><?php echo $stock ?></td>
					<td class="p-1 py-0 text-right text-dark"><strong>$<?php echo number_format($valor_venta,0,'.','.')?></strong></td>
					<td class="p-1 py-0 text-right text-dark"><strong>$<?php echo number_format($valor_venta_mayor,0,'.','.')?></strong></td>
					<td class="p-1 py-0 text-truncate text-dark"><?php echo $cant_inicial ?></td>
					<td class="p-1 py-0 text-right text-dark"><strong>$<?php echo number_format($costo,0,'.','.')?></strong></td>
					<td class="p-1 py-0 text-dark text-truncate">
						<b><?php echo $creador ?></b>
					</td>
					<td class="p-1 py-0 text-center text-dark" rowspan="2">
						<button class="btn btn-outline-primary btn-round p-1" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_operacion').load('tablas/mov_producto.php/?cod_producto=<?php echo $codigo ?>&pos=<?php echo $i ?>&bodega=<?php echo $bodega ?>', function(){cerrar_loader();});">
							<span class="fa fa-clipboard-list"></span>
						</button>
						<button class="btn btn-outline-warning btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Edit_Stock" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_edit_stock').load('paginas/detalles/editar_stock_producto.php/?cod_producto=<?php echo $codigo ?>&pos=<?php echo $i ?>&bodega=<?php echo $bodega ?>', function(){cerrar_loader();});">
							<span class="fa fa-edit"></span>
						</button>
					</td>
				</tr>
				<tr>
					<td hidden=""></td>
					<td class="p-1 py-0 text-truncate text-dark text-left" colspan="3">Proveedor: <b><?php echo $proveedor ?></b></td>
					<td class="p-1 py-0 text-truncate text-dark text-left" colspan="2">Marca: <b><?php echo $marca ?></b></td>
					<td class="p-1 py-0 text-dark text-truncate">
						<span class="badge bg-light-primary text-dark"><?php echo $fecha_registro ?></span>
					</td>
					<td hidden=""></td>
				</tr>
				<?php 
				$num_item ++;
			}
			if($num_item == 1)
			{
				?>
				<tr id="tr_no_existe">
					<td colspan="7" class="text-center p-1">No existen inventario.</td>
				</tr>
				<?php 
			}
			?>
			<tr id="tr_add_stock" <?php if($bodega != 'Principal'){?>hidden=""<?php } ?>>
				<td class="p-1">
					<a class="p-1" href="javascript:document.getElementById('tr_nuevo_stock').hidden = false;document.getElementById('tr_add_stock').hidden = true;document.getElementById('tr_no_existe').hidden = true;">
						<span class="fa fa-plus"></span>
					</a>
				</td>
			</tr>
			<tr id="tr_nuevo_stock" hidden="">
				<td class="text-center p-1">
					<a class="p-1" href="javascript:document.getElementById('tr_nuevo_stock').hidden = true;document.getElementById('tr_add_stock').hidden = false;document.getElementById('tr_no_existe').hidden = false;">
						<span class="fa fa-times"></span>
					</a>
				</td>
				<td class="p-1">
					<input type="text" class="form-control form-control-sm" name="input_proveedor" id="input_proveedor" placeholder="Proveedor" autocomplete="off">
					<input type="text" class="form-control form-control-sm" name="input_marca" id="input_marca" placeholder="Marca" autocomplete="off">
				</td>
				<td class="p-1">
					<input type="text" class="form-control form-control-sm moneda text-right" name="input_valor_venta" id="input_valor_venta" placeholder="Público" autocomplete="off">
				</td>
				<td class="p-1">
					<input type="text" class="form-control form-control-sm moneda text-right" name="input_valor_venta_mayor" id="input_valor_venta_mayor" placeholder="Mayor" autocomplete="off">
				</td>
				<td class="p-1">
					<input type="text" class="form-control form-control-sm text-center" name="input_stock_inicial" id="input_stock_inicial" autocomplete="off">
				</td>
				<td class="p-1">
					<input type="text" class="form-control form-control-sm moneda text-right" name="input_costo" id="input_costo" placeholder="Costo" autocomplete="off">
				</td>
				<td class="p-1" colspan="2">
					<button class="btn btn-outline-primary btn-round p-1" id="btn_agregar_stock">
						<span class="fa fa-save"></span> Guardar
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
		input_valor_venta_mayor = document.getElementById("input_valor_venta_mayor").value;
		input_stock_inicial = document.getElementById("input_stock_inicial").value;
		input_costo = document.getElementById("input_costo").value;
		input_marca = document.getElementById("input_marca").value;
		input_proveedor = document.getElementById("input_proveedor").value;
		if(input_valor_venta != '' && input_valor_venta_mayor != '' && input_stock_inicial != '' && input_costo != '' && input_proveedor != '' && input_marca != '')
		{
			$.ajax({	
				type:"POST",
				data:"cod_producto=<?php echo $codigo ?>&bodega=<?php echo $bodega ?>&input_valor_venta=" + input_valor_venta+"&input_valor_venta_mayor=" + input_valor_venta_mayor+"&input_stock_inicial=" + input_stock_inicial+"&input_costo=" + input_costo+"&input_proveedor=" + input_proveedor+"&input_marca=" + input_marca,
				url:"procesos/agregar_stock_producto.php",
				success:function(r)
				{
					datos=jQuery.parseJSON(r);
					if(datos['consulta'] == 1)
					{
						w_alert({ titulo: 'Stock agregado correctamente', tipo: 'success' });
						$('#div_modal_producto').load('paginas/detalles/detalles_producto.php/?cod_producto=<?php echo $codigo ?>&bodega=<?php echo $bodega ?>', function(){cerrar_loader();});
						stock_old = parseInt(document.getElementById("td_stock_<?php echo $codigo ?>").innerHTML);

						document.getElementById("td_stock_<?php echo $codigo ?>").innerHTML = parseInt(stock_old) + parseInt(input_stock_inicial);
					}
					else
						w_alert({ titulo: datos['consulta'], tipo: 'danger' });
					if(datos['consulta'] == 'Reload')
					{
						document.getElementById('div_login').style.display = 'block';
						cerrar_loader();
						
					}
				}
			});
		}
		else
		{
			if(input_valor_venta == '')
			{
				w_alert({ titulo: 'Ingrese el valor de venta del producto AL PÚBLICO', tipo: 'danger' });
				document.getElementById("input_valor_venta").focus();
			}
			else if(input_valor_venta_mayor == '')
			{
				w_alert({ titulo: 'Ingrese el valor de venta del producto AL MAYOR', tipo: 'danger' });
				document.getElementById("input_valor_venta_mayor").focus();
			}
			else if(input_stock_inicial == '')
			{
				w_alert({ titulo: 'Ingrese la cantidad de producto', tipo: 'danger' });
				document.getElementById("input_stock_inicial").focus();
			}
			else if(input_costo == '')
			{
				w_alert({ titulo: 'Ingrese el costo del producto', tipo: 'danger' });
				document.getElementById("input_costo").focus();
			}
		}

		cerrar_loader();
		document.getElementById("btn_agregar_stock").disabled = false;
	});

</script>