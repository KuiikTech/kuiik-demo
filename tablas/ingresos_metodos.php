<table width="100%" class="table text-dark table-sm w-100" id="tabla_ingresos_<?php echo $metodo ?>">
	<thead>
		<tr class="text-center">
			<th class="p-1 px-4" width="30px">#</th>
			<th class="p-1">Concepto</th>
			<th class="p-1">Valor</th>
			<th class="p-1">Creó</th>
			<?php
			if($estado == 'ABIERTA')
			{
				?>
				<th class="p-1" width="20px" id="th_more"></th>
				<?php 
			} ?>
		</tr>
	</thead>
	<tbody class="overflow-auto text-dark">
		<?php 
		$total_ingresos = 0;
		$num_item = 1;
		foreach ($ingresos as $i => $ingreso)
		{
			if($ingreso['metodo'] == $metodo)
			{
				$concepto = $ingreso['concepto'];
				$valor = $ingreso['valor'];
				$fecha = $ingreso['fecha'];
				$creador = $ingreso['creador'];

				$total_ingresos += $valor;
				$fecha_ingreso_v = $ingreso['fecha'];

				$fecha_ingreso = strftime("%A, %e %b %Y", strtotime($ingreso['fecha']));
				$fecha_ingreso = ucfirst(iconv("ISO-8859-1","UTF-8",$fecha_ingreso));

				$fecha_ingreso .= date(' | h:i A',strtotime($ingreso['fecha']));

				$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
				$result_e=mysqli_query($conexion,$sql_e);
				$ver_e=mysqli_fetch_row($result_e);
				$nombre_aux = explode(' ', $ver_e[0]);
				$apellido_aux = explode(' ', $ver_e[1]);
				$creador = $nombre_aux[0].' '.$apellido_aux[0];

				$orden = explode('total o parcial orden #', $concepto);
				if(isset($orden[1]))
					$orden = $orden[1];
				else
					$orden = null;

				$codigo_ingreso = $cod_caja.'/'.$i.'/'.$orden.'/'.$fecha_ingreso_v;

				?>
				<tr>
					<td class="p-1 py-0 text-center text-dark"><?php echo $num_item ?></td>
					<td class="p-1 py-0 text-truncate text-dark">
						<?php echo $concepto ?>
					</td>
					<td class="p-1 py-0 text-right text-dark"><strong>$<?php echo number_format($valor,0,'.','.')?></strong></td>
					<td class="p-1 py-0 text-dark text-center">
						<b><?php echo $creador ?></b>
						<br>
						<span class="badge bg-light-primary text-dark"><?php echo $fecha_ingreso ?></span>
					</td>
					<?php
					if($estado == 'ABIERTA' && $orden != null)
					{
						?>
						<td class="p-1 py-0 text-dark text-center" id="td_btn_metodo_<?php echo $i  ?>">
							<a class="pc-head-link dropdown-toggle arrow-none mr-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
								<i class="material-icons-two-tone">more_horiz</i>
							</a>
							<div class="dropdown dropdown-menu dropdown-menu-end pc-h-dropdown">
								<button onclick="document.getElementById('td_btn_metodo_<?php echo $i  ?>').hidden = true;document.getElementById('td_select_metodo_<?php echo $i  ?>').hidden = false;" class="dropdown-item p-1">
									<span>Cambiar Método de Pago</span>
								</button>
								<button data-bs-toggle="modal" data-bs-target="#Modal_Eliminar_Ingreso" onclick="$('#cod_ingreso_eliminar').val('<?php echo $codigo_ingreso ?>');" class="dropdown-item p-1">
									<span>Eliminar</span>
								</button>
							</div>
						</td>
						<td width="200px" class="p-1 py-0 text-dark text-center" id="td_select_metodo_<?php echo $i  ?>" hidden>
							<select class="form-control form-control-sm" name="input_metodo_pago" id="input_metodo_pago" onchange="cambiar_metodo('<?php echo $codigo_ingreso ?>',this.value)">
								<option value="">Selecciona Método</option>
								<option value="Efectivo">Efectivo</option>
								<option value="Tarjeta">Tarjeta</option>
								<option value="Nequi">Nequi</option>
								<option value="Bancolombia">Bancolombia</option>
								<option value="Daviplata">Daviplata</option>
								<option value="Crédito">Crédito</option>
								<option value="Bono">Bono</option>
							</select>
						</td>
						<?php 
					}
					?>
				</tr>
				<?php 
				$num_item ++;
			} 
		}
		if($num_item == 1)
		{
			?>
			<tr>
				<td colspan="5" class="text-center">No existen pagos con <?php echo ucwords($metodo) ?>.</td>
			</tr>
			<?php 
		}
		?>
	</tbody>
</table>
<div class="row float-right mt-3">
	<h3>Total Ingresos: $<?php echo number_format($total_ingresos,0,'.','.') ?></h3>
</div>