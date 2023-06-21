<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

session_set_cookie_params(7 * 24 * 60 * 60);
session_start();

$sql = "SELECT `codigo`, `productos`, `proveedor`, `creador`, `estado`, `fecha_registro`, `observaciones` FROM `compras` WHERE estado = 'EN PROCESO' order by fecha_registro DESC";
$result = mysqli_query($conexion, $sql);
$mostrar = mysqli_fetch_row($result);

if ($mostrar != NULL) {
	$productos_compra = array();
	if ($mostrar[1] != '')
		$productos_compra = json_decode($mostrar[1], true);
?>
	<div class="row mx-2">
		<table class="table text-dark table-sm w-100" id="tabla_inventario_<?php echo $codigo ?>">
			<thead>
				<tr class="text-center">
					<th width="10px" class="p-1">#</th>
					<th class="p-1">Producto</th>
					<th width="80px" class="p-1 lh-1">Valor Público</th>
					<th width="80px" class="p-1">Costo</th>
					<th width="120px" class="p-1">Cantidad</th>
					<th width="100px" class="p-1"></th>
				</tr>
			</thead>
			<tbody class="overflow-auto text-dark">
				<?php
				$costo_total = 0;
				$estado_btn_agregar = '';
				foreach ($productos_compra as $i => $item) {
					$codigo = $item['codigo'];
					$descripcion = $item['descripcion'];
					$categoria = $item['categoria'];
					$cant = $item['cant'];

					$valor_venta = $item['valor_venta'];
					$costo = $item['costo'];

					if ($cant != '')
						$editar = 0;
					else
						$editar = 1;
					if ($costo != '') {
						$suma = $cant;
						$costo_total += $suma * $costo;
					}

					if (isset($_GET['item_edit'])) {
						if ($_GET['item_edit'] == $i)
							$editar = 1;
					}
				?>
					<tr>
						<td class="p-1"><?php echo $i ?></td>
						<td class="p-1">
							<a class="text-danger" href="javascript:eliminar_item_compra('<?php echo $i ?>')">
								<span class="fa fa-trash"></span>
							</a>
							<?php echo $descripcion ?>
						</td>
						<td class="p-1 text-right">
							<?php
							if ($valor_venta != '' && $editar == 0)
								echo '$' . number_format($valor_venta, 0, '.', '.');
							else {
								if ($valor_venta != '')
									$valor_venta = number_format($valor_venta, 0, '.', '.');
							?>
								<input type="text" class="form-control form-control-sm moneda text-right" name="input_valor_venta_<?php echo $i ?>" id="input_valor_venta_<?php echo $i ?>" placeholder="Público" autocomplete="off" value="<?php echo $valor_venta ?>">
							<?php
							}
							?>
						</td>
						<td class="p-1 text-right">
							<?php
							if ($costo != '' && $editar == 0)
								echo '$' . number_format($costo, 0, '.', '.');
							else {
								if ($costo != '')
									$costo = number_format($costo, 0, '.', '.');
							?>
								<input type="text" class="form-control form-control-sm moneda text-right" name="input_costo_<?php echo $i ?>" id="input_costo_<?php echo $i ?>" placeholder="Costo" autocomplete="off" value="<?php echo $costo ?>">
							<?php
							}
							?>
						</td>
						<td class="p-1 text-center lh-1">
							<?php
							if ($cant != '' && $editar == 0)
								echo '<b> ' . $cant . '</b><br>';
							else {
							?>
								<input type="text" class="form-control form-control-sm text-center" name="input_cant_inicial_p_<?php echo $i ?>" id="input_cant_inicial_p_<?php echo $i ?>" autocomplete="off" placeholder="Principal" value="<?php echo $cant ?>">
							<?php
							}
							?>
						</td>
						<td class="p-1">
							<?php
							if ($editar == 0) {
							?>
								<button class="btn btn-sm btn-outline-warning btn-round p-0 px-1" onclick="document.getElementById('div_loader').style.display = 'block';$('#tabla_productos_compra').load('tablas/productos_compra.php/?item_edit=<?php echo $i ?>', cerrar_loader());">
									<span class="fa fa-edit"></span> Editar
								</button>
							<?php
							} else {
							?>
								<button class="btn btn-sm btn-outline-primary btn-round p-0 px-1 w-100" onclick="guardar_info_producto('<?php echo $i ?>')">
									<span class="fa fa-save"></span> Guardar
								</button>
							<?php
							}
							?>
						</td>
					</tr>
				<?php
				}
				?>
				<tr class="border-top border-2">
					<td colspan="5" class="text-right h5">Costo Total</td>
					<td colspan="3" class="text-right h5"><strong>$<?php echo number_format($costo_total, 0, '.', '.') ?></strong></td>
				</tr>
			</tbody>
		</table>
	</div>
	<hr>
	<div class="row clearfix mx-1">
		<input type="text" class="form-control form-control-sm" id="busqueda_producto_compra" name="busqueda_producto_compra" autocomplete="off" placeholder="Busqueda de productos" onKeyUp="mostrar_busqueda_productos();">
	</div>
	<hr class="my-1">
	<div class="conatiner px-0" id="div_tabla_productos"></div>

	<script type="text/javascript">
		$('input.moneda').keyup(function(event) {
			if (event.which >= 37 && event.which <= 40) {
				event.preventDefault();
			}
			$(this).val(function(index, value) {
				return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
			});
		});

		function guardar_info_producto(item) {
			cant = document.getElementById("input_cant_inicial_p_" + item).value;
			valor_venta = document.getElementById("input_valor_venta_" + item).value;
			costo = document.getElementById("input_costo_" + item).value;

			if (cant != '' && valor_venta != '' && costo != '') {
				$.ajax({
					type: "POST",
					data: "item=" + item + "&cant=" + cant + "&valor_venta=" + valor_venta + "&costo=" + costo,
					url: "procesos/guardar_datos_compra.php",
					success: function(r) {
						datos = jQuery.parseJSON(r);
						if (datos['consulta'] == 1) {
							w_alert({
								titulo: 'Datos agregados correctamente',
								tipo: 'success'
							});
							document.getElementById('div_loader').style.display = 'block';

							$('#tabla_productos_compra').load('tablas/productos_compra.php', cerrar_loader());
						} else
							w_alert({
								titulo: datos['consulta'],
								tipo: 'danger'
							});
					}
				});
			} else {
				if (valor_venta == '') {
					w_alert({
						titulo: 'Ingrese el valor de venta',
						tipo: 'danger'
					});
					document.getElementById("input_valor_venta_" + item).focus();
				} else if (costo == '') {
					w_alert({
						titulo: 'Ingrese el costo',
						tipo: 'danger'
					});
					document.getElementById("input_costo_" + item).focus();
				} else if (cant == '') {
					w_alert({
						titulo: 'Ingrese la cantidad para la Bodega principal',
						tipo: 'danger'
					});
					document.getElementById("input_cant_inicial_p_" + item).focus();
				}
			}
		}

		function mostrar_busqueda_productos() {
			var busqueda = document.getElementById("busqueda_producto_compra").value;
			busqueda = busqueda.replace(/ /g, "***");
			if (busqueda != '') {
				if (busqueda.length > 2) {
					document.getElementById('div_loader').style.display = 'block';
					$('#div_tabla_productos').load('paginas/vistas_pdv/pdv_compras.php/?consulta=' + busqueda, function() {
						cerrar_loader();
					});
				}
			}
		}

		function agregar_producto_compra(cod_producto) {
			document.getElementById('div_loader').style.display = 'block';
			$.ajax({
				type: "POST",
				data: "cod_producto=" + cod_producto,
				url: "procesos/agregar_producto_compra.php",
				success: function(r) {
					datos = jQuery.parseJSON(r);
					if (datos['consulta'] == 1) {
						w_alert({
							titulo: 'Producto Agregado',
							tipo: 'success'
						});
						document.getElementById('div_loader').style.display = 'block';
						$('#tabla_productos_compra').load('tablas/productos_compra.php', cerrar_loader());
					} else {
						w_alert({
							titulo: datos['consulta'],
							tipo: 'danger'
						});
						cerrar_loader();
					}
				}
			});
		}

		function eliminar_item_compra(num_item) {
			document.getElementById('div_loader').style.display = 'block';
			$.ajax({
				type: "POST",
				data: "num_item=" + num_item,
				url: "procesos/eliminar_item_compra.php",
				success: function(r) {
					datos = jQuery.parseJSON(r);
					if (datos['consulta'] == 1) {
						w_alert({
							titulo: 'Item eliminado',
							tipo: 'success'
						});
						document.getElementById('div_loader').style.display = 'block';
						$('#tabla_productos_compra').load('tablas/productos_compra.php', cerrar_loader());
					} else {
						w_alert({
							titulo: datos['consulta'],
							tipo: 'danger'
						});
						if (datos['consulta'] == 'Reload') {
							document.getElementById('div_login').style.display = 'block';
							cerrar_loader();

						}
						cerrar_loader();
					}
				}
			});
		}
	</script>

<?php
}
?>