<div class="table-responsive text-dark text-center py-0 px-1">
	<table class="table text-dark table-sm w-100" id="tabla_inventario_<?php echo $codigo ?>">
		<thead>
			<tr class="text-center">
				<th width="10px" class="p-1">#</th>
				<th class="p-1">Stock</th>
				<?php
				if ($rol == 'Administrador') {
				?>
					<th class="p-1 text-right">Costo</th>
				<?php
				}
				?>
				<th class="p-1 text-right">Valor Público</th>
				<th class="p-1 text-right">Valor Mayor</th>
			</tr>
		</thead>
		<tbody class="overflow-auto text-dark">
			<?php
			$total_inventario = 0;
			$num_item = 1;
			foreach ($inventario as $i => $producto) {
				$costo = $producto['costo'];
				$valor_venta = $producto['valor_venta'];
				if (isset($producto['valor_venta_mayor']))
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
				$fecha_registro = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_registro));

				$fecha_registro .= date(' | h:i A', strtotime($producto['fecha_registro']));

				$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
				$result_e = mysqli_query($conexion, $sql_e);
				$ver_e = mysqli_fetch_row($result_e);

				if ($ver_e != NULL) {
					$nombre_aux = explode(' ', $ver_e[0]);
					$apellido_aux = explode(' ', $ver_e[1]);
					$creador = $nombre_aux[0] . ' ' . $apellido_aux[0];
				} else
					$creador = '?';

				$marca = '';
				$proveedor = '';

				if (isset($producto['marca']))
					$marca = $producto['marca'];

				if (isset($producto['proveedor']))
					$proveedor = $producto['proveedor'];

			?>
				<tr>
					<td class="p-1 py-0 text-center text-dark" rowspan="2"><?php echo $num_item ?></td>
					<td class="p-1 py-0 text-truncate text-dark h3"><?php echo $stock ?></td>
					<?php
					if ($rol == 'Administrador') {
					?>
						<td class="p-1 py-0 text-right text-dark"><b>$<?php echo number_format($costo, 0, '.', '.') ?></b></td>
					<?php
					}
					?>
					<td class="p-1 py-0 text-right text-dark"><b>$<?php echo number_format($valor_venta, 0, '.', '.') ?></b></td>
					<td class="p-1 py-0 text-right text-dark"><b>$<?php echo number_format($valor_venta_mayor, 0, '.', '.') ?></b></td>
				</tr>
				<tr>
					<td hidden=""></td>
					<td class="p-1 py-0 text-truncate text-dark text-left" colspan="2">Proveedor: <b><?php echo $proveedor ?></b></td>
					<td class="p-1 py-0 text-truncate text-dark text-left" colspan="3">Marca: <b><?php echo $marca ?></b></td>
				</tr>
			<?php
				$num_item++;
			}
			if ($num_item == 1) {
			?>
				<tr id="tr_no_existe">
					<td colspan="7" class="text-center p-1">No existen inventario.</td>
				</tr>
			<?php
			}
			?>
		</tbody>
	</table>
</div>
<div class="row m-0 p-0">
	<h3>Total Inventario: <?php echo $total_inventario ?></h3>
</div>