<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();
$conexion = $obj->conexion();
session_set_cookie_params(7 * 24 * 60 * 60);
session_start();

$usuario = $_SESSION['usuario_restaurante'];
$caja = $_SESSION['caja_restaurante'];

$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
$result_e = mysqli_query($conexion, $sql_e);
$ver_e = mysqli_fetch_row($result_e);

$nombre = $ver_e[0];
$rol = $ver_e[5];
$cod_mesa = $_GET['cod_mesa'];

$sql_mesa = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura`, `cod_cliente`, `pagos` FROM `mesas` WHERE cod_mesa = '$cod_mesa'";
$result_mesa = mysqli_query($conexion, $sql_mesa);
$mostrar_mesa = mysqli_fetch_row($result_mesa);

$total_mesa = 0;

$cod_cliente = $mostrar_mesa[5];

$sql_clientes = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro` FROM `clientes` WHERE codigo != 0 order by nombre";
$result_clientes = mysqli_query($conexion, $sql_clientes);

$sql_mesas = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura` FROM `mesas` WHERE cod_mesa != '$cod_mesa'";
$result_mesas = mysqli_query($conexion, $sql_mesas);

$disabled_saldo = 'disabled';
$text_saldo = 'text-danger';

$cliente = array(
	'codigo' => '',
	'id' => '',
	'nombre' => '',
	'telefono' => '',
);

$tipo = "Regular";
$vip = '';

if ($cod_cliente != '') {
	$sql = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro`, `tipo`, `info` FROM `clientes` WHERE codigo = '$cod_cliente'";
	$result = mysqli_query($conexion, $sql);
	$ver = mysqli_fetch_row($result);

	$cliente = array(
		'codigo' => $ver[0],
		'id' => $ver[1],
		'nombre' => $ver[2],
		'telefono' => $ver[3]
	);

	$tipo = $ver[8];

	$info = array();

	if ($ver[9] != '')
		$info = json_decode($ver[9], true);

	if (isset($info['whatsapp']))
		$whatsapp_cliente = $info['whatsapp'];
	else
		$whatsapp_cliente = '';
}

$mensaje = '';

if ($tipo == 'Especial')
	$vip = '<span class="fa fa-tag text-info" title="Cliente especial"></span>';

$pagos = array();
if ($mostrar_mesa[6] != '')
	$pagos = json_decode($mostrar_mesa[6], true);

$sql_whatsapp = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'WhatsApp'";
$result_whatsapp = mysqli_query($conexion, $sql_whatsapp);
$mostrar_whatsapp = mysqli_fetch_row($result_whatsapp);

$whatsapp = $mostrar_whatsapp[2];

$sql_sistema = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Tipo Sistema'";
$result_sistema = mysqli_query($conexion, $sql_sistema);
$mostrar_sistema = mysqli_fetch_row($result_sistema);

$tipo_sistema = $mostrar_sistema[2];

?>
<div class="card_body">
	<div id="div_cliente">
		<div class="text-center row h5 m-0">
			<h4 class="col mt-2">Datos de cliente</h4>
			<div class="btn-float-right">
				<div class="dropdown font-sans-serif d-inline-block mb-2">
					<a class="btn btn-sm btn-outline-primary btn-round px-2" id="dropdownMenuButton" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-v"></span></a>
					<div class="dropdown-menu dropdown-menu-end py-0" aria-labelledby="dropdownMenuButton">
						<a class="dropdown-item" href="javascript:document.getElementById('div_loader').style.display = 'block';$('#div_cliente').load('paginas/detalles/agregar_cliente.php/?cod_mesa=<?php echo $cod_mesa ?>', function(){cerrar_loader();});">Agregar Cliente</a>
						<a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#Modal_Transferir_Mesa" href="#">Transferir Mesa</a>
						<a class="dropdown-item" href="#" onclick="dividir_cuenta('<?php echo $cod_mesa ?>')" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#Modal_dividir_cuenta">Dividir Cuenta</a>
					</div>
				</div>
			</div>
		</div>
		<div class="row m-0 border-top pt-3 border-bottom border-3">
			<input type="text" name="cod_cliente" id="cod_cliente" hidden="" value="<?php echo $cod_cliente ?>">
			<p class="row m-0 pl-0">
				<span class="col-4 col-sm-4 col-md-6 col-lg-5 d-flex justify-content-end pr-0">
					<?php
					if ($cod_cliente != '') {
					?>
						<a class="p-0 text-primary" href="javascript:seleccionar_cliente('')">
							<span class="fa fa-times text-danger f-16"></span>
						</a>
					<?php
					}
					?>
					Cédula/NIT:
				</span>
				<?php
				if ($cod_cliente != '') {
				?>
					<span class="col-8 col-sm-8 col-md-6 col-lg-7 text-left"><b class="text-truncate w-100" id="b_id_cliente"><?php echo $cliente['id'] ?></b></span>
				<?php
				} else {
				?>
					<span class="col-8 col-sm-8 col-md-6 col-lg-7 text-left"><input type="text" class="form-control form-control-sm" name="input_cc_cliente" id="input_cc_cliente" placeholder="Busqueda por C.C/NIT" onkeydown="if(event.key=== 'Enter'){buscar_x_cc(this.value,'<?php echo $cod_mesa ?>')}" autocomplete="off"></span>
				<?php
				}
				?>
			</p>
			<p class="row m-0 pl-0">
				<span class="col-4 col-sm-4 col-md-6 col-lg-5 d-flex justify-content-end pr-0"> Nombre: </span>
				<span class="col-8 col-sm-8 col-md-6 col-lg-7 text-left"><b class="text-truncate w-100" id="b_nombre_cliente"><?php echo $cliente['nombre'] . ' ' . $vip ?></b></span>
			</p>
			<p class="row m-0 pl-0">
				<span class="col-4 col-sm-4 col-md-6 col-lg-5 d-flex justify-content-end pr-0">
					<a class="p-1" href="javascript:document.getElementById('div_search').hidden = false;document.getElementById('a_plus').hidden = true;" id="a_plus">
						<span class="fa fa-search f-16 text-success"></span>
					</a>
					Teléfono: </span>
				<span class="col-8 col-sm-8 col-md-6 col-lg-7 text-left"><b class="text-truncate w-100" id="b_telefono_cliente"><?php echo $cliente['telefono'] ?></b></span>
			</p>
		</div>
	</div>
	<div class="form-group my-2" id="div_search" hidden="">
		<div class="row m-0 p-1">
			<a class="p-1 col-1 text-center" href="javascript:document.getElementById('div_search').hidden = true;document.getElementById('a_plus').hidden = false;">
				<span class="fa fa-times"></span>
			</a>
			<input type="text" class="form-control form-control-sm col" name="input_busqueda" id="input_busqueda" placeholder="Cédula/NIT - Nombre - Teléfono" autocomplete="off">
			<button class="btn btn-sm btn-outline-primary btn-round col-2" id="btn_buscar_cliente"><span class="fas fa-search"></span></button>
		</div>
	</div>
	<div id="tabla_busqueda_cliente">
		<table class="table text-dark table-sm w-100">
			<thead>
				<tr>
					<th class="p-1"></th>
					<th class="p-1"><strong>Producto</strong></th>
					<th width="100px" class="p-1 text-center"><strong>Valor</strong></th>
					<th width="50px" class="p-1 text-center"><strong>Cant</strong></th>
					<th width="75px" class="p-1 text-center"><strong>Total</strong></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php
				if ($mostrar_mesa[2] != '') {
					$productos_mesa = json_decode($mostrar_mesa[2], true);
					$mensaje = '*CANT*         *DESCRIPCION*          *V. UNITARIO*    *V. TOTAL*<br>';
					foreach ($productos_mesa as $i => $producto) {
						$cod_producto = $producto['codigo'];
						$cant = $producto['cant'];
						$descripcion_str = $producto['descripcion'];
						$valor_unitario = $producto['valor_unitario'];
						$estado = $producto['estado'];
						if ($tipo_sistema == 'Pedidos') {
							$bg_tr = '';
							$bg_dot = '';
							if ($estado == 'PENDIENTE')
								$bg_dot = 'bg-danger';
							if ($estado == 'EN ESPERA') {
								$bg_dot = 'bg-info';
								$bg_tr = 'alert-info-2';
							}
							if ($estado == 'PREPARANDO')
								$bg_dot = 'bg-warning';
							if ($estado == 'DESPACHADO')
								$bg_dot = 'bg-success';
						} else {
							$bg_tr = '';
							$bg_dot = '';
						}

						$valor_total = $cant * $valor_unitario;
						$total_mesa += $valor_total;

						$valor_unitario = number_format($valor_unitario, 0, '.', '.');
						$valor_total = '$' . number_format($valor_total, 0, '.', '.');


						//$mensaje .= '   *'.$cant . '*   ' . str_pad($descripcion_str, 26, " ", STR_PAD_RIGHT) . str_pad('*$'.$valor_unitario, 15, " ", STR_PAD_LEFT) . str_pad($valor_total.'*', 15, " ", STR_PAD_LEFT).'<br>';
				?>
						<tr class="<?php echo $bg_tr ?>" title="Estado: <?php echo $estado ?>">
							<td width="20px" class="text-center py-1 px-0">
								<?php
								if ($tipo_sistema == 'Pedidos') {
									if ($estado == 'EN ESPERA' || $estado == 'PENDIENTE' || $rol == 'Administrador') {
								?>
										<a class="p-0 text-primary" href="javascript:eliminar_item('<?php echo $i ?>','<?php echo $cod_mesa ?>')">
											<span class="fa fa-times text-danger f-16"></span>
										</a>
									<?php
									}
								} else {
									?>
									<a class="p-0 text-primary" href="javascript:eliminar_item('<?php echo $i ?>','<?php echo $cod_mesa ?>')">
										<span class="fa fa-times text-danger f-16"></span>
									</a>
								<?php
								}
								?>
							</td>
							<td class="p-1"><?php echo $descripcion_str ?></td>
							<td class="text-right p-1">
								<?php
								if (($tipo_sistema == 'Pedidos' && $estado == 'EN ESPERA') || $tipo_sistema == 'Ninguno') {
								?>
									<input type="text" class="form-control moneda text-right py-0" id="input_valor_<?php echo $i ?>" name="input_valor_<?php echo $i ?>" value="<?php echo $valor_unitario ?>" onchange="guardar_valor_producto(this.value,'<?php echo $i ?>','<?php echo $cod_mesa ?>')">
								<?php
								} else {
								?>
									<strong><?php echo $valor_unitario ?></strong>
								<?php
								}
								?>
							</td>
							</td>
							<td class="text-center p-1">
								<?php
								if ($tipo_sistema == 'Ninguno') {
								?>
									<input type="text" class="form-control text-center py-0 px-1" id="input_cant_<?php echo $i ?>" name="input_cant_<?php echo $i ?>" value="<?php echo $cant ?>" onchange="guardar_cant_producto(this.value,'<?php echo $i ?>','<?php echo $cod_mesa ?>')">
								<?php
								} else {
								?>
									<b><?php echo $cant ?></b>
								<?php
								}
								?>
							</td>
							<td class="text-right p-1"><strong><?php echo $valor_total ?></strong> </td>
							<td class="p-1">
								<?php
								if ($tipo_sistema == 'Pedidos') {
									if ($estado == 'EN ESPERA') {
										$btn_realizar_pedido = 1;
								?>
										<a href="#" data-bs-toggle="modal" data-bs-target="#Modal_Add_nota" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_add_nota').load('paginas/detalles/notas.php/?cod_mesa=<?php echo $cod_mesa ?>&num_item=<?php echo $i ?>', cerrar_loader());"><span class="fa fa-comment-dots text-dark f-16"></span></a>
									<?php
									} else {
									?>
										<span class="dot <?php echo $bg_dot ?> m-0"></span>
								<?php
									}
								}
								?>
							</td>
						</tr>
				<?php
					}
				}
				?>
				<tr>
					<td width="20px" class="text-center py-1 px-0">
						<span class="fas fa-barcode text-success f-16"></span>
					</td>
					<td class="p-1" colspan="3">
						<input type="number" class="form-control py-0" id="input_codigo" name="input_codigo" autocomplete="off" placeholder="Código de barras " onkeypress="if (event.keyCode == 13) {buscar_producto('<?php echo $cod_mesa ?>',this.value)}" autofocus>
					</td>
					<td></td>
					<td></td>
				</tr>
			</tbody>
		</table>
		<div class="row px-4">
			<?php
			if (isset($btn_realizar_pedido)) {
			?>
				<button class="btn btn-outline-info btn-round px-1" onclick="realizar_pedido('<?php echo $cod_mesa ?>')">Enviar pedido</button>
			<?php
			}
			?>
		</div>
		<table class="table text-dark mb-1">
			<tbody>
				<tr hidden="">
					<td class="p-1">
						<h4>Subtotal</h4>
					</td>
					<td class="p-1 text-right">
						<h3 class="m-0"><?php echo '$' . number_format($total_mesa, 0, '.', '.'); ?></h3>
					</td>
					<input type="number" name="sub_total" id="sub_total" value="<?php echo $total_mesa ?>" hidden="">
				</tr>
				<tr hidden="">
					<td class="p-1">
						<h4>Descuento</h4>
					</td>
					<td class="p-1 text-right"><input type="text" class="form-control moneda text-right input_descuento" name="descuento_cuenta" id="descuento_cuenta" onKeyUp="calcular_total();" autocomplete="off"></td>
				</tr>
				<tr>
					<td class="p-1">
						<h4>TOTAL</h4>
					</td>
					<td class="p-1 text-right">
						<h3 class="m-0" id="total_a_pagar"><?php echo '$' . number_format($total_mesa, 0, '.', '.'); ?></h3>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="row m-0 py-2 text-center">
		<h5 class="mb-0">Agregar metodos de pago</h5>
	</div>
	<div class="row m-0 p-0 px-1">
		<table class="table text-dark table-sm w-100">
			<tbody>
				<?php
				$total_pagos = 0;
				foreach ($pagos as $j => $pago) {
					$valor_pago = $pago['valor'];
					$total_pagos += $valor_pago;
					$fecha_pago = $pago['fecha'];
				?>
					<tr title="<?php echo $fecha_pago ?>">
						<td class="p-1"><?php echo $pago['tipo'] ?></td>
						<td class="text-right p-1 h4">$<?php echo number_format($valor_pago, 0, '.', '.'); ?></td>
						<td width="20px" class="text-center py-1 px-0">
							<?php
							if (isset($pago['eliminable'])) {
								if ($pago['eliminable'] == 'SI') {
							?>
									<a class="p-0 text-danger" href="javascript:eliminar_pago('<?php echo $cod_mesa ?>','<?php echo $j ?>')">
										<span class="fa fa-times"></span>
									</a>
								<?php
								} else {
								?>
									<span class="badge bg-info">R</span>
								<?php
								}
							} else {
								?>
								<a class="p-0 text-danger" href="javascript:eliminar_pago('<?php echo $cod_mesa ?>','<?php echo $j ?>')">
									<span class="fa fa-times"></span>
								</a>
							<?php
							}
							?>
						</td>
					</tr>
					<?php
					if ($pago['tipo'] == 'Efectivo') {
						$recibido = $pago['recibido'] == '' ? 0 : $pago['recibido'];
						$cambio = $recibido - $valor_pago;
					?>
						<tr class="text-center">
							<td colspan="2">
								<div class="row">
									<div class="col px-1 text-right">Recibido</div>
									<div class="col px-1">
										<input type="text" class="form-control form-control-sm moneda" id="input_recibido" name="input_recibido" placeholder="Recibido" onchange="guardar_recibido('<?php echo $cod_mesa ?>',this.value,'<?php echo $j ?>')" value="<?php echo number_format($recibido, 0, '.', '.') ?>">
									</div>
								</div>
							</td>
						</tr class="text-center">
						<tr>
							<td class="text-right text-nowarp px-1 text-info" width="200px" colspan="2">
								Cambio <b class="h5 text-info">$<?php echo number_format($cambio, 0, '.', '.') ?></b>
							</td>
						</tr>
				<?php
					}
				}
				$saldo = $total_mesa - $total_pagos;

				if ($saldo == 0) {
					$disabled_saldo = '';
					$text_saldo = 'text-success';
				}
				?>
				<tr>
					<td class="text-center">
						<select class="form-control form-control-sm" id="input_metodo_pago" name="input_metodo_pago">
							<option value="">Seleccione uno...</option>
							<option value="Efectivo">Efectivo</option>
							<option value="Tarjeta">Tarjeta</option>
							<option value="Nequi">Nequi</option>
							<option value="Bancolombia">Bancolombia</option>
							<option value="Daviplata">Daviplata</option>
							<option value="Crédito">Crédito</option>
							<option value="Descuento">Descuento</option>
						</select>
					</td>
					<td class="text-center">
						<input type="text" class="form-control form-control-sm moneda" id="valor_pago" name="valor_pago" placeholder="Valor">
					</td>
					<td class="text-center">
						<button type="button" class="btn btn-sm btn-outline-danger btn-round" id="btn_agregar_pago">+</button>
					</td>
				</tr>
				<tr>
					<td class="text-right p-1"><b>Total Pagos</b></td>
					<td class="text-right p-1 h4" width="120px"><b>$<?php echo number_format($total_pagos, 0, '.', '.'); ?></b></td>
					<td></td>
				</tr>
				<tr>
					<td class="text-right p-1"><b>Saldo</b></td>
					<td class="text-right p-1 h4 <?php echo $text_saldo ?>" width="120px"><b>$<?php echo number_format($saldo, 0, '.', '.'); ?></b></td>
					<td></td>
				</tr>
			</tbody>
		</table>
	</div>
	<hr class="m-1">
	<div class="row m-0">
		<div class="col">
			<div class="form-floating">
				<textarea class="form-control" placeholder="Leave a comment here" id="observaciones_venta" name="observaciones_venta" style="height: 100px"></textarea>
				<label for="observaciones_venta">Observaciones</label>
			</div>
		</div>
	</div>

	<hr class="m-1">
	<div class="row py-2">
		<div class="col text-center">
			<button type="button" class="btn btn-sm btn-outline-danger btn-round" data-bs-toggle="modal" data-bs-target="#Modal_Cancelar_Venta" onclick="$('#cod_mesa_cancel').val('<?php echo $cod_mesa ?>');">Cancelar Venta</button>
		</div>
		<div class="col text-center">
			<button type="button" class="btn btn-sm btn-outline-primary btn-round" data-bs-toggle="modal" data-bs-target="#Modal_Procesar_Venta" <?php echo $disabled_saldo ?>>Procesar Venta</button>
		</div>
		<?php
		if ($whatsapp == 'SI') {
			if ($cod_cliente != '') {
		?>
				<div class="col text-center">
					<button type="button" class="btn btn-sm btn-success btn-round" data-bs-toggle="modal" data-bs-target="#Modal_Enviar_Cuenta" onclick="$('#cod_mesa_enviar').val('<?php echo $cod_mesa ?>');">
						Enviar Cuenta
						<span class="fab fa-whatsapp"></span>
					</button>
				</div>
		<?php
			}
		}
		?>
	</div>
</div>

<!-- Modal procesar venta-->
<div class="modal fade" id="Modal_Procesar_Venta" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header text-center p-2">
				<h5 class="modal-title">Seguro desea procesar esta venta?</h5>
			</div>
			<div class="modal-body p-2">
				<input type="text" name="cod_mesa_proc" id="cod_mesa_proc" hidden="" value="<?php echo $cod_mesa ?>">
				<div class="row m-0">
					<button type="button" class="btn btn-sm btn-secondary btn-round col" data-bs-dismiss="modal">NO</button>
					<button type="button" class="btn btn-sm btn-primary btn-round col" id="btn_procesar_venta">SI</button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal cancelar venta-->
<div class="modal fade" id="Modal_Cancelar_Venta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header text-center p-2">
				<h5 class="modal-title">Seguro desea cancelar esta venta?</h5>
			</div>
			<div class="modal-body">
				<input type="text" name="cod_mesa_cancel" id="cod_mesa_cancel" hidden="" value="<?php echo $cod_mesa ?>">
				<div class="row m-0">
					<button type="button" class="btn btn-sm btn-outline-secondary btn-round col" data-bs-dismiss="modal">NO</button>
					<button type="button" class="btn btn-sm btn-outline-primary btn-round col" id="btn_cancelar_venta">SI, Cancelar</button>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
if ($whatsapp == 'SI') {
?>
	<!-- Modal enviar cuenta-->
	<div class="modal fade" id="Modal_Enviar_Cuenta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header text-center p-2">
					<h5 class="modal-title">Enviar cuenta a <span class="fab fa-whatsapp text-success"></span> <?php echo $cliente['telefono'] ?>?</h5>
				</div>
				<div class="modal-body">
					<div class="row m-0">
						<p>
							<?php
							$mensaje = 'Señor(a): *' . $cliente['nombre'] . '*.<br> <br> El total de su compra es: ' . str_pad('*$' . number_format($total_mesa, 0, '.', '.') . '*', 15, " ", STR_PAD_LEFT) . '<br>';
							$mensaje_html  = 'Señor(a): <b>' . $cliente['nombre'] . '</b>,<br> <br> El total de su compra es: <b>$' . number_format($total_mesa, 0, '.', '.') . '</b><br>';
							echo $mensaje_html;
							?>
							<br>
							<br>
							<b>Nota:</b> Se enviará un mensaje con el valor de la cuenta a <span class="fab fa-whatsapp text-success"></span><b> <?php echo $cliente['telefono'] ?></b>.
						</p>
					</div>
					<div class="row m-0">
						<?php
						if ($whatsapp_cliente == 'Verificado') {
						?>
							<button type="button" class="btn btn-sm btn-outline-secondary btn-round col-4 m-auto" data-bs-dismiss="modal">NO</button>
							<button type="button" class="btn btn-sm btn-outline-success btn-round col-4 m-auto" onclick="enviar_whatsapp('<?php echo $cliente['codigo'] ?>','text','<?php echo $mensaje ?>')">SI, Enviar <span class="fab fa-whatsapp"></button>
						<?php
						} else {
						?>
							<button type="button" class="btn btn-sm btn-outline-success btn-round col-6 m-auto" onclick="enviar_whatsapp('<?php echo $cliente['codigo'] ?>','template','bienvenido')">Enviar mensaje de VERIFICACIÓN <span class="fab fa-whatsapp"></button>
						<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
}
?>

<!-- Modal agregar nota-->
<div class="modal fade" id="Modal_Add_nota" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h3 class="modal-title">Notas para cocina</h3>
			</div>
			<div class="modal-body p-1" id="div_add_nota"></div>
		</div>
	</div>
</div>

<!-- Modal cantidad producto-->
<div class="modal fade" id="Modal_Cantidad_Producto" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h5 class="modal-title">Ingrese la cantidad de producto</h5>
			</div>
			<div class="modal-body m-0 p-2">
				<input type="text" name="cod_mesa_pedido" id="cod_mesa_pedido" hidden="">
				<input type="text" name="cod_producto_pedido" id="cod_producto_pedido" hidden="">
				<div class="row m-0 p-2">
					<label class="text-center col p-1">
						<h5>Cantidad:</h5>
					</label>
					<input type="number" class="form-control form-control-sm col text-center" id="cantidad_pedido" name="cantidad_pedido" onFocus="this.select()" value="1">
				</div>
				<div class="row m-0 p-1 mt-4 d-flex justify-content-between">
					<div class="col-6 text-center">
						<button type="button" class="btn btn-outline-secondary btn-round" data-bs-dismiss="modal">CANCELAR</button>
					</div>
					<div class="col-6 text-center">
						<button type="button" class="btn btn-outline-primary btn-round" onclick="agregar_producto()" id="btn_agregar_producto_m">ACEPTAR</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Transferir mesa-->
<div class="modal fade" id="Modal_Transferir_Mesa" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header text-center p-2">
				<h5 class="modal-title">Seguro desea transferir esta mesa?</h5>
			</div>
			<div class="modal-body">
				<input type="text" name="cod_mesa_1" id="cod_mesa_1" hidden="" value="<?php echo $cod_mesa ?>">
				<div class="row m-0 p-1">
					<label class="text-center col-auto py-2 px-3">Mesa nueva:</label>
					<select class="form-control col" id="cod_mesa_2" name="cod_mesa_2">
						<option value="">Seleccione una mesa </option>
						<?php
						$sql_mesas = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura`, `descuentos` FROM `mesas` WHERE cod_mesa != '$cod_mesa'";
						$result_mesas = mysqli_query($conexion, $sql_mesas);
						while ($mostrar_mesas = mysqli_fetch_row($result_mesas)) {
							$estado = $mostrar_mesas[3];
							if ($estado == 'LIBRE') {
								$nombre = $mostrar_mesas[1];
						?>
								<option value="<?php echo $mostrar_mesas[0] ?>"><?php echo $nombre ?></option>
						<?php
							}
						}
						?>
					</select>
				</div>
				<hr>
				<div class="row m-0">
					<button type="button" class="btn btn-sm btn-outline-secondary btn-round col" data-bs-dismiss="modal">NO</button>
					<button type="button" class="btn btn-sm btn-outline-primary btn-round col" id="btn_transferir_mesa">SI, Transferir</button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Dividir Cuenta-->
<div class="modal fade" id="Modal_dividir_cuenta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h3 class="modal-title">Seguro desea procesar esta venta DIVIDIDA?</h3>
			</div>
			<div class="modal-body" id="div_dividir_cuenta"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
	document.getElementById('div_loader').style.display = 'block';
	$('#div_row_mesas').load('paginas/vistas_pdv/pdv_mesas.php', function() {
		cerrar_loader();
	});
	$('.btn-primary').removeClass("btn-primary").addClass("btn-outline-primary");
	setTimeout("$('#btn_mesa_<?php echo $cod_mesa ?>').removeClass('btn-outline-primary').addClass('btn-primary');", 100);

	$('[data-bs-toggle="tooltip"]').tooltip();
	//$(".select2").select2({
	//	dropdownParent: $("#Modal_Procesar_Venta")
	//});

	$('#descripcion_descuento').keypress(function(e) {
		if (e.keyCode == 13)
			$('#btn_agregar_descuento').click();
	});

	$('#valor_descuento').keypress(function(e) {
		if (e.keyCode == 13)
			$('#btn_agregar_descuento').click();
	});

	$('#input_busqueda').keypress(function(e) {
		if (e.keyCode == 13)
			$('#btn_buscar_cliente').click();
	});

	$('#btn_buscar_cliente').click(function() {
		document.getElementById('div_loader').style.display = 'block';
		input_busqueda = document.getElementById("input_busqueda").value;
		input_busqueda = input_busqueda.replace(/ /g, "***");
		if (input_busqueda != '' && input_busqueda.length > 2)
			$('#tabla_busqueda_cliente').load('tablas/tabla_busqueda_cliente.php/?page=1&input_buscar=' + input_busqueda, function() {
				cerrar_loader();
			});
		else {
			w_alert({
				titulo: 'Ingrese al menos 3 caracteres',
				tipo: 'danger'
			});
			document.getElementById("input_busqueda").focus();
		}
		cerrar_loader();
	});

	function modal_cantidad(cod_producto, cod_mesa) {
		$('#cod_producto_pedido').val(cod_producto);
		$('#cod_mesa_pedido').val(cod_mesa);
		$('#cantidad_pedido').val(1);
		setTimeout("document.getElementById('cantidad_pedido').focus();", 500)
	}

	$('#cantidad_pedido').keypress(function(e) {
		if (e.keyCode == 13)
			$('#btn_agregar_producto_m').click();
	});

	function buscar_x_cc(input_busqueda, cod_mesa) {
		document.getElementById('div_loader').style.display = 'block';
		input_busqueda = input_busqueda.replace(/ /g, "***");
		if (input_busqueda != '') {
			$.ajax({
				type: "POST",
				data: "input_busqueda=" + input_busqueda + "&cod_mesa=" + cod_mesa,
				url: "procesos/buscar_x_cc.php",
				success: function(r) {
					datos = jQuery.parseJSON(r);
					if (datos['consulta'] == 1) {
						w_alert({
							titulo: 'Cliente Seleccionado',
							tipo: 'success'
						});
						$('#div_cuenta').load('paginas/vistas_pdv/pdv_cuenta.php/?cod_mesa=<?php echo $cod_mesa ?>', function() {
							cerrar_loader();
						});
					} else
						w_alert({
							titulo: datos['consulta'],
							tipo: 'danger'
						});
					if (datos['consulta'] == 'Reload') {
						document.getElementById('div_login').style.display = 'block';
						cerrar_loader();

					}
				}
			});
		} else {
			w_alert({
				titulo: 'Ingrese la cédula/NIT a buscar',
				tipo: 'danger'
			});
			document.getElementById("input_busqueda").focus();
		}
		cerrar_loader();
	}

	$('input.moneda').keyup(function(event) {
		if (event.which >= 37 && event.which <= 40) {
			event.preventDefault();
		}

		$(this).val(function(index, value) {
			return value
				.replace(/\D/g, "")
				.replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
		});
	});

	$('#btn_procesar_venta').click(function() {
		cod_mesa = document.getElementById("cod_mesa_proc").value;
		observaciones = document.getElementById("observaciones_venta").value;
		$.ajax({
			type: "POST",
			data: 'cod_cliente=<?php echo $cod_cliente ?>&caja=<?php echo $caja ?>' + '&cod_mesa_proc=' + cod_mesa + '&observaciones=' + observaciones,
			url: "procesos/procesar_venta.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				if (datos['consulta'] == 1) {
					w_alert({
						titulo: 'Venta procesada correctamente',
						tipo: 'success'
					});
					$('.modal-backdrop').remove();
					document.querySelector("body").style.overflow = "auto";
					atras();

					if (datos['config_cajon'] == 'Automatico')
						abrir_cajon();

					cod_cliente = "<?php echo $cod_cliente ?>";
					if (cod_cliente != '')
						$('#cod_cliente_fact').val(cod_cliente).trigger('change');

					if (datos['config_imp'] == 'Manual' || datos['config_imp'] == 'Automática') {
						$('#cod_venta_fact').val(datos['cod_venta']);
						if (datos['config_imp'] == 'Automática')
							setTimeout("$('#btn_generar_factura').click();", 500)
						else
							$("#Modal_Generar_Factura").modal('show');
					}
				} else
					w_alert({
						titulo: datos['consulta'],
						tipo: 'danger'
					});
				if (datos['consulta'] == 'Reload') {
					document.getElementById('div_login').style.display = 'block';
					cerrar_loader();

				}
			}
		});
	});

	$('#btn_cancelar_venta').click(function() {
		cod_mesa = document.getElementById("cod_mesa_cancel").value;
		$.ajax({
			type: "POST",
			data: "cod_mesa=" + cod_mesa,
			url: "procesos/cancelar_venta.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				if (datos['consulta'] == 1) {
					location.reload();
				} else
					w_alert({
						titulo: datos['consulta'],
						tipo: 'danger'
					});
				if (datos['consulta'] == 'Reload') {
					document.getElementById('div_login').style.display = 'block';
					cerrar_loader();

				}
			}
		});
	});

	$('#btn_agregar_pago').click(function() {
		document.getElementById('div_loader').style.display = 'block';
		metodo_pago = document.getElementById("input_metodo_pago").value;
		valor_pago = document.getElementById("valor_pago").value;
		$.ajax({
			type: "POST",
			data: "cod_mesa=<?php echo $cod_mesa ?>" + "&metodo_pago=" + metodo_pago + "&valor_pago=" + valor_pago,
			url: "procesos/agregar_pago_mesa.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				if (datos['consulta'] == 1) {
					w_alert({
						titulo: 'Método Agregado',
						tipo: 'success'
					});
					$('#div_cuenta').load('paginas/vistas_pdv/pdv_cuenta.php/?cod_mesa=<?php echo $cod_mesa ?>', function() {
						cerrar_loader();
					});
				} else
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
		});
	});

	$('#btn_transferir_mesa').click(function() {
		cod_mesa_1 = document.getElementById("cod_mesa_1").value;
		cod_mesa_2 = document.getElementById("cod_mesa_2").value;
		$.ajax({
			type: "POST",
			data: "cod_mesa_1=" + cod_mesa_1 + "&cod_mesa_2=" + cod_mesa_2,
			url: "procesos/transferir_cuenta.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				if (datos['consulta'] == 1) {
					w_alert({
						titulo: 'Cuenta Transferida correctamente',
						tipo: 'success'
					});
					$("#Modal_trasnferir").modal('toggle');
					$('.modal-backdrop').remove();
					abrir_mesa(cod_mesa_2);
				} else
					w_alert({
						titulo: datos['consulta'],
						tipo: 'danger'
					});
			}
		});
	});

	function dividir_cuenta(cod_mesa) {
		document.getElementById('div_loader').style.display = 'block';
		$('#div_dividir_cuenta').load('paginas/vistas_pdv/dividir_cuenta.php/?cod_mesa=' + <?php echo $cod_mesa ?>, cerrar_loader());
	}

	function seleccionar_cliente(cod_cliente) {
		document.getElementById('div_loader').style.display = 'block';
		$.ajax({
			type: "POST",
			data: "cod_mesa=<?php echo $cod_mesa ?>" + "&cod_cliente=" + cod_cliente,
			url: "procesos/asignar_cliente_mesa.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				if (datos['consulta'] == 1) {
					if (cod_cliente != '')
						w_alert({
							titulo: 'Cliente Seleccionado',
							tipo: 'success'
						});
					else
						w_alert({
							titulo: 'Cliente Descartado',
							tipo: 'success'
						});
					$('#div_cuenta').load('paginas/vistas_pdv/pdv_cuenta.php/?cod_mesa=<?php echo $cod_mesa ?>', function() {
						cerrar_loader();
					});
				} else
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
		});
	}

	function eliminar_pago(cod_mesa, item) {
		document.getElementById('div_loader').style.display = 'block';
		$.ajax({
			type: "POST",
			data: "cod_mesa=" + cod_mesa + "&item=" + item,
			url: "procesos/eliminar_pago.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				if (datos['consulta'] == 1) {
					w_alert({
						titulo: 'Descuento Eliminado',
						tipo: 'success'
					});
					$('#div_cuenta').load('paginas/vistas_pdv/pdv_cuenta.php/?cod_mesa=<?php echo $cod_mesa ?>', function() {
						cerrar_loader();
					});
				} else
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
		});
	}

	function realizar_pedido(cod_mesa) {
		//document.getElementById('div_loader').style.display = 'block';
		$.ajax({
			type: "POST",
			data: "cod_mesa=" + cod_mesa,
			url: "procesos/realizar_pedido.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				if (datos['consulta'] == 1) {
					w_alert({
						titulo: 'Pedido Realizado',
						tipo: 'success'
					});
					$('#div_cuenta').load('paginas/vistas_pdv/pdv_cuenta.php/?cod_mesa=' + cod_mesa, cerrar_loader());
					imprimir_comanda(datos['textos']);
				} else {
					if (datos['consulta'] == 'Reload')
						location.reload();
					else {
						w_alert({
							titulo: datos['consulta'],
							tipo: 'danger'
						});
						cerrar_loader();
					}
				}
			}
		});
	}

	function guardar_valor_producto(valor, item, cod_mesa) {
		document.getElementById('div_loader').style.display = 'block';
		$.ajax({
			type: "POST",
			data: "valor=" + valor + "&item=" + item + "&cod_mesa=" + cod_mesa,
			url: "procesos/guardar_valor_producto.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				if (datos['consulta'] == 1) {
					w_alert({
						titulo: 'Valor Guardado',
						tipo: 'success'
					});
					$('#div_cuenta').load('paginas/vistas_pdv/pdv_cuenta.php/?cod_mesa=' + cod_mesa, cerrar_loader());
				} else {
					if (datos['consulta'] == 'Reload')
						location.reload();
					else {
						w_alert({
							titulo: datos['consulta'],
							tipo: 'danger'
						});
						cerrar_loader();
					}
				}
			}
		});
	}

	function guardar_cant_producto(cant, item, cod_mesa) {
		document.getElementById('div_loader').style.display = 'block';
		$.ajax({
			type: "POST",
			data: "cant=" + cant + "&item=" + item + "&cod_mesa=" + cod_mesa,
			url: "procesos/guardar_cant_producto.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				if (datos['consulta'] == 1) {
					w_alert({
						titulo: 'Cantidad Guardada',
						tipo: 'success'
					});
					mostrar_productos(datos['cod_categoria'], cod_mesa);
					$('#div_cuenta').load('paginas/vistas_pdv/pdv_cuenta.php/?cod_mesa=' + cod_mesa, cerrar_loader());
				} else {
					if (datos['consulta'] == 'Reload')
						location.reload();
					else {
						w_alert({
							titulo: datos['consulta'],
							tipo: 'danger'
						});
						cerrar_loader();
					}
				}
			}
		});
	}

	function buscar_producto(cod_mesa, busqueda_barcode) {
		document.getElementById('div_loader').style.display = 'block';
		if (busqueda_barcode != '') {
			$.ajax({
				type: "POST",
				data: "busqueda_barcode=" + busqueda_barcode,
				url: "procesos/buscar_producto_barcode.php",
				success: function(r) {
					datos = jQuery.parseJSON(r);
					if (datos['consulta'] == 1) {
						cerrar_loader();
						$('#Modal_Cantidad_Producto').modal('show');
						cod_producto = datos['cod_producto'];
						modal_cantidad(cod_producto, cod_mesa);
					} else {
						if (datos['consulta'] == 'Reload')
							location.reload();
						else {
							w_alert({
								titulo: datos['consulta'],
								tipo: 'danger'
							});
							cerrar_loader();
						}
					}
				}
			});
		}
	}

	function guardar_recibido(cod_mesa, recibido, item) {
		document.getElementById('div_loader').style.display = 'block';
		$.ajax({
			type: "POST",
			data: "cod_mesa=" + cod_mesa + "&recibido=" + recibido + "&item=" + item,
			url: "procesos/guardar_recibido.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				if (datos['consulta'] == 1) {
					w_alert({
						titulo: 'Recibido Guardado',
						tipo: 'success'
					});
					$('#div_cuenta').load('paginas/vistas_pdv/pdv_cuenta.php/?cod_mesa=' + cod_mesa, cerrar_loader());
				} else {
					if (datos['consulta'] == 'Reload')
						location.reload();
					else {
						w_alert({
							titulo: datos['consulta'],
							tipo: 'danger'
						});
						cerrar_loader();
					}
				}
			}
		});
	}
</script>