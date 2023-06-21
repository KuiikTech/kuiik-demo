<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

session_set_cookie_params(7 * 24 * 60 * 60);
session_start();
if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];

	$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto`, `color`, `costos` FROM `usuarios` WHERE codigo='$usuario'";
	$result_e = mysqli_query($conexion, $sql_e);
	$ver_e = mysqli_fetch_row($result_e);

	$cedula = $ver_e[1];
	$costos_user = $ver_e[9];

	$nombre_usuario = $ver_e[2] . ' ' . $ver_e[3];
	$rol = $ver_e[5];

	if ($ver_e[7] == '')
		$url_avatar = 'user.svg';
	else
		$url_avatar = $ver_e[7];

	$cod_servicio = $_GET['cod_servicio'];

	$sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro`, `local` FROM `servicios` WHERE codigo = '$cod_servicio'";
	$result = mysqli_query($conexion, $sql);
	$mostrar = mysqli_fetch_row($result);

	$informacion = array();
	$items = array();
	$repuestos = array();
	$accesorios = array();
	$pagos = array();

	if ($mostrar[4] != '') {
		$informacion = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[4]);
		$informacion = str_replace('	', ' ', $informacion);
		$informacion = json_decode($informacion, true);
	}
	if ($mostrar[1] != '') {
		$items = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[1]);
		$items = str_replace('	', ' ', $items);
		$items = json_decode($items, true);
	}

	if ($mostrar[5] != '') {
		$repuestos = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[5]);
		$repuestos = str_replace('	', ' ', $repuestos);
		$repuestos = json_decode($repuestos, true);
	}

	if ($mostrar[6] != '') {
		$accesorios = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[6]);
		$accesorios = str_replace('	', ' ', $accesorios);
		$accesorios = json_decode($accesorios, true);
	}

	if ($mostrar[3] != '')
		$pagos = json_decode($mostrar[3], true);
	$cod_cliente = $mostrar[2];
	$tecnico = $mostrar[8];

	if ($mostrar[10] != null) {
		$fecha_hora_entrega = date("Y-m-d h:i A", strtotime($mostrar[10]));
		$fecha_entrega = date("Y-m-d", strtotime($fecha_hora_entrega));
		$hora_entrega = date("h:i A", strtotime($fecha_hora_entrega));

		$fecha_entrega_input = date("Y-m-d", strtotime($mostrar[10]));
		$hora_entrega_input = date("H:i", strtotime($mostrar[10]));
	} else {
		$fecha_entrega = '<b class="text-info">SIN ASIGNAR</b>';
		$hora_entrega = '<b class="text-info">SIN ASIGNAR</b>';

		$fecha_entrega_input = '';
		$hora_entrega_input = '';
	}
	$fecha_registro = date('d-m-Y h:i A', strtotime($mostrar[11]));
	$estado = $mostrar[9];

	$items_seguridad = array();
	if (isset($informacion['seguridad']))
		$items_seguridad = $informacion['seguridad'];

	$fotos = array();
	if (isset($informacion['fotos']))
		$fotos = $informacion['fotos'];

	$local = $mostrar[12];
	$creador = $mostrar[7];

	$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
	$result_e = mysqli_query($conexion, $sql_e);
	$ver_e = mysqli_fetch_row($result_e);
	if ($ver_e != null) {
		$nombre_aux = explode(' ', $ver_e[0]);
		$apellido_aux = explode(' ', $ver_e[1]);
		$creador = $nombre_aux[0] . ' ' . $apellido_aux[0];
	}

	if ($informacion['tipo'] == 'Orden')
		$informacion['tipo'] = 'Orden de servicio';

	if ($informacion['tipo'] == 'Garantía') {
		$servicio_asociado = $informacion['servicio_asociado'];
		$informacion['tipo'] = '<b class="text-warning">' . $informacion['tipo'] . '</b><small><b> (Servicio # ' . str_pad($servicio_asociado, 5, "0", STR_PAD_LEFT) . ')</b></small>';
	}

	if (!isset($informacion['observaciones']))
		$informacion['observaciones'] = array();

	$observaciones_tecnico = array();

	if (!isset($informacion['observaciones_tecnico'])) {
		$observaciones_tecnico = '<small class="fst-italic text-secondary">No se han ingresado observaciones por parte del técnico</small>';
		$informacion['observaciones_tecnico'] = array();
	}

	$observaciones_ticket = array();

	if (!isset($informacion['observaciones_ticket'])) {
		$observaciones_ticket = '<small class="fst-italic text-secondary">No se han ingresado observaciones para el ticket</small>';
		$informacion['observaciones_ticket'] = array();
	}

	if (!isset($informacion['solucion']))
		$informacion['solucion'] =  '<b class="text-warning">Sin asignar</b>';
	else {
		if ($informacion['solucion'] == '')
			$informacion['solucion'] =  '<b class="text-warning">Sin asignar</b>';
	}

	$sol_si = '';
	$sol_no = '';

	$info_equipo = array();
	if (isset($informacion['info_equipo']))
		$info_equipo = $informacion['info_equipo'];

	if ($informacion['solucion']  == 'REPARADO') {
		$informacion['solucion'] = '<b class="text-success">REPARADO</b>';
		$sol_si = 'selected';
	}
	if ($informacion['solucion']  == 'NO REPARADO') {
		$informacion['solucion'] = '<b class="text-danger">NO REPARADO</b>';
		$sol_no = 'selected';
	}

	$sin_repuestos = '';
	if ($estado == 'ENTREGADO') {
		if (!isset($informacion['sin_repuestos']))
			$informacion['sin_repuestos'] = '';
		else {
			if ($informacion['sin_repuestos'] == 'true')
				$sin_repuestos = 'SIN REPUESTOS';
		}
	} else {
		if (!isset($informacion['sin_repuestos']))
			$informacion['sin_repuestos'] = '';
		else {
			if ($informacion['sin_repuestos'] == 'true')
				$informacion['sin_repuestos'] = 'Checked';
			else
				$informacion['sin_repuestos'] = '';
		}
	}


	if (isset($informacion['total_servicios']))
		$total_servicios = $informacion['total_servicios'];
	else
		$total_servicios = 0;

	$cod_equipo = $informacion['equipo'];

	$sql_equipo = "SELECT `codigo`, `nombre`, `estado`, `fecha_creacion`, `creador` FROM `tipo_equipos` WHERE codigo = '$cod_equipo'";
	$result_equipo = mysqli_query($conexion, $sql_equipo);
	$ver_equipo = mysqli_fetch_row($result_equipo);

	if ($ver_equipo != null)
		$informacion['equipo'] = $ver_equipo[1];

	if ($mostrar[2] != '') {
		$cliente = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[2]);
		$cliente = str_replace('	', ' ', $cliente);
		$cliente = json_decode($cliente, true);
	}
	$cod_cliente = $cliente['codigo'];

	$sql_cliente = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro` FROM `clientes` WHERE `codigo`='$cod_cliente'";
	$result_cliente = mysqli_query($conexion, $sql_cliente);
	$cliente_2 = $result_cliente->fetch_object();

	if ($cliente_2 != null) {
		$cliente = json_encode($cliente_2, JSON_UNESCAPED_UNICODE);
		$cliente = json_decode($cliente, true);
	} else {
		$cliente['direccion'] = '';
		$cliente['correo'] = '';
	}

	if ($estado == 'PENDIENTE')
		$bg_estado = 'bg-danger';
	else if ($estado == 'TERMINADO')
		$bg_estado = 'bg-success';
	else
		$bg_estado = 'bg-info';

	$nombre_tec = '<b class="text-danger">No asignado</b>';
	$selecionado = '';
	if ($mostrar[8] != null && $mostrar[8] != '0') {
		$cod_tecnico = $mostrar[8];

		$sql_tec = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo = '$cod_tecnico'";
		$result_tec = mysqli_query($conexion, $sql_tec);
		$mostrar_tec = mysqli_fetch_row($result_tec);

		$nombre_tec = $mostrar_tec[2] . ' ' . $mostrar_tec[3];
	} else
		$selecionado = 'selected';

	$sql_mov = "SELECT `codigo`, `cod_servicio`, `informacion`, `operacion`, `usuario`, `fecha_registro` FROM `respaldo_info` WHERE cod_servicio = '$cod_servicio' order by fecha_registro ASC";
	$result_mov = mysqli_query($conexion, $sql_mov);

?>
	<div class="modal-header p-1 row m-0 mb-1">
		<h4 class="col mb-0">Detalles del servicio <span class="badge bg-dark text-white">Código: <?php echo str_pad($cod_servicio, 5, "0", STR_PAD_LEFT) ?></span></h4>
		<div class="btn-float-right col">
			<h4 class="mb-0"><span class="badge <?php echo $bg_estado ?>"><?php echo $estado ?></span></h4>
			<div class="dropdown font-sans-serif btn-reveal-trigger">
				<button class="btn btn-link text-600 btn-sm dropdown-toggle dropdown-caret-none text-info" type="button" id="dropdown-weather-update" data-bs-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--2"></span></button>
				<div class="dropdown-menu dropdown-menu-end border py-2" aria-labelledby="dropdown-weather-update">
					<?php
					if (!isset($informacion['servicio_asociado'])) {
					?>
						<a class="dropdown-item" href="javascript:agregar_garantia('<?php echo $cod_servicio ?>')">Agregar Garantía</a>
					<?php
					}
					if ($estado == 'PENDIENTE' && $rol == 'Administrador') {
					?>
						<a class="dropdown-item text-danger" href="javascript:anular_servicio('<?php echo $cod_servicio ?>')">Anular</a>
					<?php
					}
					?>
				</div>
			</div>
			<div class="dropdown font-sans-serif btn-reveal-trigger">
				<button class="btn btn-link text-600 btn-sm dropdown-toggle dropdown-caret-none" type="button" id="dropdown-weather-update" data-bs-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false"><span class="fas fa-list fs--2"></span></button>
				<div class="dropdown-menu dropdown-menu-end border py-2" aria-labelledby="dropdown-weather-update" style="min-width: 40rem !important;">
					<span class="text-danger">MOVIMIENTOS</span>
					<?php
					while ($mostrar_mov = mysqli_fetch_row($result_mov)) {
						$creador_mov = $mostrar_mov[4];

						$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador_mov'";
						$result_e = mysqli_query($conexion, $sql_e);
						$ver_e = mysqli_fetch_row($result_e);
						if ($ver_e != null) {
							$nombre_aux = explode(' ', $ver_e[0]);
							$apellido_aux = explode(' ', $ver_e[1]);
							$creador_mov = $nombre_aux[0] . ' ' . $apellido_aux[0];
						}
						echo '<p class="text-dark m-0">- <b>' . $mostrar_mov[3] . '</b>[' . $mostrar_mov[5] . '] - <b>' . $creador_mov . '</b></p>';
					}
					?>

				</div>
			</div>
		</div>
	</div>
	<div class="modal-body row p-2 text-dark">
		<div class="col-12 col-sm-12 col-md-8 col-lg-8 border-left border-3 order-2 px-1">
			<div class="row m-0 p-0">
				<div class="col-12 col-sm-12 col-md-8 col-lg-8 border-left border-3 px-1">
					<div class="row m-0 px-1">
						<label class="m-0 col-3 col-sm-3 col-md-3 col-lg-3 py-0 fw-bold">Tipo:</label>
						<div class="col-9 col-sm-9 col-md-9 scol-lg-9"><?php echo $informacion['tipo'] ?></div>
					</div>
					<div class="row m-0 px-1">
						<label class="m-0 col-3 col-sm-3 col-md-3 col-lg-3 py-0 fw-bold">Equipo:</label>
						<div class="col-9 col-sm-9 col-md-9 scol-lg-9"><?php echo $informacion['equipo'] ?></div>
					</div>
					<div class="row m-0 px-1">
						<?php
						$info_equipo = array();
						if (isset($informacion['equipo'])) {
							if (isset($informacion['lista_info'])) {
								$info_equipo = $informacion['lista_info'];
						?>
								<div class="row m-0 text-center p-0 px-1 pb-3 lh-1">
									<?php
									$num_item = 1;
									$total = 0;
									foreach ($info_equipo as $i => $item) {
										$nombre = $item['nombre'];
										$tipo = $item['tipo'];
										$valor = $item['valor'];

										$type_input = '';
										if ($tipo == 'Texto')
											$type_input = 'text';
										if ($tipo == 'Número')
											$type_input = 'number';
										if ($tipo == 'Fecha')
											$type_input = 'date';
										if ($tipo == 'Fecha/Hora')
											$type_input = 'datetime';
										if ($tipo == 'Hora')
											$type_input = 'time';
									?>
										<div class="row m-0 p-0 px-1">
											<div class="col-auto text-left p-0 px-1" colspan="2"> - <b><?php echo $nombre ?></b>:</div>
											<div class="col-5 text-left p-0" <?php if ($estado != 'ENTREGADO' && $estado != 'ANULADO') { ?>ondblclick="document.getElementById('div_input_info_<?php echo $i ?>').hidden = false;this.hidden = true;" <?php } ?>><?php echo $valor ?></div>
											<?php
											if ($estado != 'ENTREGADO' && $estado != 'ANULADO') {
											?>
												<div class="col-5 text-left p-0" hidden id="div_input_info_<?php echo $i ?>">
													<input type="<?php echo $type_input ?>" class="form-control form-control-sm" id="input_info_<?php echo $i ?>" name="input_info_<?php echo $i ?>" value="<?php echo $valor ?>" onchange="guardar_info_equipo_s('<?php echo $cod_servicio ?>','<?php echo $i ?>',this.value)" autocomplete="off">
												</div>
											<?php
											} ?>
										</div>
									<?php
										$num_item++;
									}
									?>
								</div>
						<?php
							}
						}
						?>
					</div>
				</div>
				<div class="col-12 col-sm-12 col-md-4 col-lg-4 p-0">
					<div id="carousel_fotos" class="carousel slide" data-bs-ride="carousel" style="height: 150px;">
						<div class="carousel-indicators">
							<?php
							if (count($fotos) > 0) {
								$pos_f = 1;
								foreach ($fotos as $f => $foto) {
							?>
									<button type="button" data-bs-target="#carousel_fotos" data-bs-slide-to="<?php echo $f - 1 ?>" class="<?php if ($pos_f == 1) echo 'active'; ?>" aria-current="true" aria-label="<?php echo $f ?>"></button>
							<?php
									$pos_f++;
								}
							}
							?>
						</div>
						<div class="carousel-inner" style="height: 150px;">
							<?php
							if (count($fotos) > 0) {
								$pos_f = 1;
								foreach ($fotos as $f => $foto) {
									$foto_s = 'https://rancho1.witsoft.co/fotos_servicios/' . $foto['nombre'];
									if (!file_exists($foto_s))
										$foto_s = 'fotos_servicios/' . $foto['nombre'];
							?>
									<div class="carousel-item <?php if ($pos_f == 1) echo 'active'; ?>">
										<a href="#" title="Eliminar Imagen" class="text-danger p-0 px-1 m-1" style="position: fixed;z-index: 2;" onclick="$('#cod_servicio_eliminar').val(<?php echo $cod_servicio ?>);$('#item_eliminar').val(<?php echo $f ?>);$('#Modal_Eliminar_Imagen').modal('show');">
											<span class="fa fa-times"></span>
										</a>
										<div onclick="$('#Modal_Ver_Foto').modal('show');document.getElementById('contenedor_foto').src = '<?php echo $foto_s ?>';">
											<img src="<?php echo $foto_s ?>" class="d-block w-100" alt="...">
										</div>
									</div>
							<?php
									$pos_f++;
								}
							}
							?>
						</div>
						<button class="carousel-control-prev" type="button" data-bs-target="#carousel_fotos" data-bs-slide="prev">
							<span class="carousel-control-prev-icon" aria-hidden="true"></span>
							<span class="visually-hidden">Previous</span>
						</button>
						<button class="carousel-control-next" type="button" data-bs-target="#carousel_fotos" data-bs-slide="next">
							<span class="carousel-control-next-icon" aria-hidden="true"></span>
							<span class="visually-hidden">Next</span>
						</button>
					</div>
					<div class="row text-center mt-1">
						<div class="col">
							<button class="btn btn-sm btn-outline-primary btn-round p-1" onclick="$('#cod_servicio_upload').val(<?php echo $cod_servicio ?>);$('#Modal_Subir_Foto').modal('show');">
								Subir Foto
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="row m-0 p-1">
				<label class="m-0 col-auto col-sm-auto col-md-auto col-lg-auto py-0 fw-bold text-truncate">Observaciones: </label>
				<div class="col-auto col-sm-auto col-md-auto col-lg-auto">
					<?php
					$observaciones = $informacion['observaciones'];

					foreach ($observaciones as $i => $obs) {
						$creador_obs = $obs['creador'];
						$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador_obs'";
						$result_e = mysqli_query($conexion, $sql_e);
						$ver_e = mysqli_fetch_row($result_e);
						if ($ver_e != null) {
							$nombre_aux = explode(' ', $ver_e[0]);
							$apellido_aux = explode(' ', $ver_e[1]);
							$creador_obs = $nombre_aux[0]; //.' '.$apellido_aux[0];
						}

						$fecha_obs = date('d-m-Y h:i A', strtotime($obs['fecha']));
					?>
						<div class="row m-0 border-bottom">
							<div class="col m-0 px-1 lh-1"><?php echo $obs['obs'] ?></div>
							<div class="col-auto m-0 px-1 text-center border-start border-2 lh-1">
								<?php
								echo $obs['local'] . '<br><b>' . $creador_obs . '</b><br><small>' . $fecha_obs . '</small>';
								?>
							</div>
						</div>
					<?php
					}
					if ($estado != 'ENTREGADO' && $estado != 'ANULADO') {
					?>
						<div class="row m-0 mt-1">
							<div class="input-group mb-2">
								<input class="form-control form-control-sm" id="input_observaciones" name="input_observaciones" placeholder="Descripción del servicio, Observaciones para la ejecución">
								<button class="btn btn-sm btn-outline-primary btn-round" id="btn_guardar_obs"><span class="fa fa-save"></span> Guardar</button>
							</div>
						</div>
					<?php
					}
					?>
				</div>

			</div>
			<div class=" border-top border-3"></div>
			<div class="row m-0 px-1 pt-2">
				<label class="m-0 col-3 col-sm-3 col-md-3 col-lg-3 py-0 fw-bold text-truncate">Fecha Registro: </label>
				<div class="col-9 col-sm-9 col-md-9 scol-lg-9"><?php echo $fecha_registro ?></div>
			</div>
			<div class="row m-0 px-1">
				<label class="m-0 col-3 col-sm-3 col-md-3 col-lg-3 py-0 fw-bold text-truncate">Recepción: </label>
				<div class="col-9 col-sm-9 col-md-9 scol-lg-9"><?php echo $local . '(' . $creador . ')' ?></div>
			</div>
			<div class="row m-0 px-1">
				<label class="m-0 col-3 col-sm-3 col-md-3 col-lg-3 py-0 fw-bold text-truncate">Fecha Posible Entrega:<i class="feather icon-star-on"></i> </label>
				<div class="col-9 col-sm-9 col-md-9 scol-lg-9 fw-bold" <?php if ($estado != 'ENTREGADO' && $estado != 'ANULADO') { ?>ondblclick="document.getElementById('div_input_fecha').hidden = false;this.hidden = true;" <?php } ?>><?php echo $fecha_entrega ?></div>
				<?php
				if ($estado != 'ENTREGADO' && $estado != 'ANULADO') {
				?>
					<div class="col-9 col-sm-9 col-md-9 scol-lg-9 fw-bold" id="div_input_fecha" hidden="">
						<div class="input-group">
							<input type="date" class="form-control form-control-sm" name="input_fecha_e" id="input_fecha_e" autocomplete="off" placeholder="Selecciona una fecha" value="<?php echo $fecha_entrega_input ?>">
							<button class="btn btn-sm btn-outline-primary btn-round" id="btn_guardar_fecha"><span class="fa fa-save"></span> Guardar</button>
						</div>
					</div>
				<?php
				}
				?>
			</div>
			<div class="row m-0 px-1">
				<label class="m-0 col-3 col-sm-3 col-md-3 col-lg-3 py-0 fw-bold text-truncate">Hora Posible Entrega: </label>
				<div class="col-9 col-sm-9 col-md-9 scol-lg-9 fw-bold" <?php if ($estado != 'ENTREGADO' && $estado != 'ANULADO') { ?>ondblclick="document.getElementById('div_input_hora').hidden = false;this.hidden = true;" <?php } ?>><?php echo $hora_entrega ?></div>
				<?php
				if ($estado != 'ENTREGADO' && $estado != 'ANULADO') {
				?>
					<div class="col-9 col-sm-9 col-md-9 scol-lg-9 fw-bold" id="div_input_hora" hidden="">
						<div class="input-group">
							<input type="time" class="form-control form-control-sm" name="input_hora_e" id="input_hora_e" autocomplete="off" placeholder="Selecciona una hora" value="<?php echo $hora_entrega_input ?>">
							<button class="btn btn-sm btn-outline-primary btn-round" id="btn_guardar_hora"><span class="fa fa-save"></span> Guardar</button>
						</div>
					</div>
				<?php
				}
				?>
			</div>
			<div class="row m-0 px-1">
				<label class="m-0 col-3 col-sm-3 col-md-3 col-lg-3 py-0 fw-bold text-truncate">Técnico: </label>
				<div class="col-9 col-sm-9 col-md-9 scol-lg-9" <?php if ($estado != 'ENTREGADO' && $estado != 'ANULADO') { ?>ondblclick="document.getElementById('div_input_tecnico').hidden = false;this.hidden = true;" <?php } ?>><?php echo $nombre_tec ?></div>
				<?php
				if ($estado != 'ENTREGADO' && $estado != 'ANULADO') {
				?>
					<div class="col-9 col-sm-9 col-md-9 scol-lg-9 fw-bold" id="div_input_tecnico" hidden="">
						<div class="input-group">
							<select class="form-control form-control-sm" id="input_tecnico_serv" name="input_tecnico_serv">
								<option value="" <?php echo $selecionado ?>>Sin Asignar</option>
								<?php
								$sql_usuarios = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo != 1 AND estado = 'ACTIVO' AND rol = 'Técnico'";
								$result_usuarios = mysqli_query($conexion, $sql_usuarios);
								while ($mostrar_usuarios = mysqli_fetch_row($result_usuarios)) {
									$nombre_usuario = $mostrar_usuarios[2] . ' ' . $mostrar_usuarios[3];
									if ($tecnico == $mostrar_usuarios[0])
										$selecionado = 'selected';
									else
										$selecionado = '';
								?>
									<option value="<?php echo $mostrar_usuarios[0] ?>" <?php echo $selecionado ?>><?php echo $nombre_usuario ?></option>
								<?php
								}
								?>
							</select>
							<button class="btn btn-sm btn-outline-primary btn-round" id="btn_guardar_tecnico"><span class="fa fa-save"></span> Guardar</button>
						</div>
					</div>
				<?php
				}
				?>
			</div>
			<div class="row mx-0 px-1 pt-2 border-top border-3" id="tabla_items">
				<div class="text-center">
					<h5>Items del servicio</h5>
				</div>
				<div class="table-responsive text-dark text-center p-0">
					<table width="100%" class="table text-dark table-sm" id="tabla_daños">
						<thead>
							<tr class="text-center">
								<th width="30px" class="table-plus text-dark datatable-nosort px-1">#</th>
								<th width="250px" class="px-1">Daño</th>
								<th class="px-1">Observaciones</th>
								<th width="10px"></th>
							</tr>
						</thead>
						<tbody class="overflow-auto">
							<?php
							$num_item = 1;
							foreach ($items as $i => $item) {
								$daño = $item['daño'];
								$observaciones = $item['observaciones'];

								$sql_daño = "SELECT `codigo`, `nombre`, `estado`, `fecha_creacion`, `creador` FROM `tipo_daños` WHERE codigo = '$daño'";
								$result_daño = mysqli_query($conexion, $sql_daño);
								$ver_daño = mysqli_fetch_row($result_daño);

								if ($ver_daño != null)
									$daño = $ver_daño[1];
							?>
								<tr role="row" class="odd">
									<td class="text-center p-0 text-muted"><?php echo $num_item ?></td>
									<td class="text-center p-0"><b><?php echo $daño ?></b></td>
									<td class="text-left p-0"><b><?php echo $observaciones ?></b></td>
									<td class="text-center p-0">
										<a class="btn btn-sm btn-outline-danger btn-round p-0 px-1" hidden onclick="eliminar_item(<?php echo $i ?>);">
											<span class="fa fa-trash"></span>
										</a>
									</td>
								</tr>
							<?php
								$num_item++;
							}
							?>
							<tr hidden>
								<td class="text-center"><?php echo $num_item ?></td>
								<td class="px-1">
									<select class="form-control form-control-sm" name="input_daño" id="input_daño">
										<option value="">Selecciona el tipo de daño</option>
										<?php
										$sql_tipo_daños = "SELECT `codigo`, `nombre`, `estado`, `fecha_creacion`, `creador` FROM `tipo_daños` WHERE estado = 'ACTIVO'";
										$result_tipo_daños = mysqli_query($conexion, $sql_tipo_daños);
										while ($mostrar_tipo_daños = mysqli_fetch_row($result_tipo_daños)) {
											$nombre = $mostrar_tipo_daños[1];
										?>
											<option value="<?php echo $mostrar_tipo_daños[0] ?>"><?php echo $nombre ?></option>
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
			</div>

			<div class="row mx-0 px-1 pt-2 border-top border-3" id="tabla_items">
				<div class="text-center">
					<h5>Seguridad del equipo</h5>
				</div>
				<div class="table-responsive text-dark text-center p-0">
					<table width="100%" class="table text-dark table-sm" id="tabla_seguridad">
						<thead>
							<tr class="text-center">
								<th width="30px" class="table-plus text-dark datatable-nosort px-1">#</th>
								<th width="250px" class="px-1"><span class="requerido">*</span>Tipo</th>
								<th class="px-1">Valor</th>
								<th width="10px" hidden></th>
							</tr>
						</thead>
						<tbody class="overflow-auto">
							<?php
							$num_item = 1;
							foreach ($items_seguridad as $i => $item_s) {
								$tipo_seguridad = $item_s['tipo_seguridad'];
								$valor = $item_s['valor'];
							?>
								<tr role="row" class="odd">
									<td class="text-center p-0 text-muted"><?php echo $num_item ?></td>
									<td class="text-center p-0"><b><?php echo $tipo_seguridad ?></b></td>
									<td class="text-center p-0"><b><?php echo $valor ?></b></td>
									<td class="text-center p-0" hidden>
										<a class="btn btn-sm btn-outline-danger btn-round p-0 px-1" onclick="eliminar_item_seguridad(<?php echo $i ?>);">
											<span class="fa fa-trash"></span>
										</a>
									</td>
								</tr>
							<?php
								$num_item++;
							}

							if ($estado != 'ENTREGADO' && $estado != 'ANULADO') {
							?>
								<tr>
									<td class="text-center"><?php echo $num_item ?></td>
									<td class="px-1">
										<select class="form-control form-control-sm" name="input_seguridad_2" id="input_seguridad_2" onchange="cambio_select_seguridad_s(this.value,'<?php echo $cod_servicio ?>')">
											<option value="">Selec. tipo de seguridad</option>
											<option value="Contraseña">Contraseña</option>
											<option value="PIN">PIN</option>
											<option value="Patron">Patron</option>
											<option value="Contraseña Aplicaciones">Contraseña Aplicaciones</option>
											<option value="Patron Aplicaciones">Patron Aplicaciones</option>
										</select>
									</td>
									<td class="px-1">
										<input type="text" class="form-control form-control-sm" name="input_valor_seguridad_2" id="input_valor_seguridad_2" autocomplete="off">
									</td>
									<td>
										<button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_agregar_item_seguridad">Agregar</button>
									</td>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>

			<div class="row m-0 p-1 border-top border-3">
				<div class="col">
					<div class="row m-0 pt-1">
						<div class="text-center row">
							<h5 class="text-info mb-0 col">Repuestos </h5>
							<?php
							if ($estado != 'ENTREGADO' && $estado != 'ANULADO') {
							?>
								<div class="form-check form-switch col-auto mb-0">
									<input class="form-check-input form-check-input-danger col-auto" id="input_sin_repuestos" type="checkbox" onchange="guardar_info('sin_repuestos='+this.checked)" <?php echo $informacion['sin_repuestos'] ?> />
									<label class="form-check-label text-danger col-auto mb-0" for="input_sin_repuestos">Sin repuestos</label>
								</div>
							<?php
							} else {
							?>
								<h5 class="text-danger col-auto mb-0" for="input_sin_repuestos"><?php echo $sin_repuestos ?></h5>
							<?php
							} ?>
						</div>
						<div class="table-responsive text-dark text-center p-0">
							<table class="table text-dark table-sm" width="100%">
								<thead>
									<tr>
										<th class="p-1"></th>
										<th class="p-1"><strong>Producto</strong></th>
										<th width="60px" class="p-1 text-center"><strong>Costo</strong></th>
										<th width="40px" class="p-1 text-center"><strong>Cant</strong></th>
										<th width="75px" class="p-1 text-center"><strong>Total</strong></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$total_repuestos = 0;
									foreach ($repuestos as $i => $repuesto) {
										$cod_repuesto = $repuesto['codigo'];
										$cant = $repuesto['cant'];
										$descripcion_str = $repuesto['descripcion'];
										if (isset($repuesto['costo_unitario'])) {
											if ($rol == 'Administrador' || $rol == 'Técnico') {
												$costo = $repuesto['costo_unitario'];
												$costo_total = $cant * $costo;
												$total_repuestos += $costo_total;

												$costo = number_format($costo, 0, '.', '.');
												$costo_total = '$' . number_format($costo_total, 0, '.', '.');
											} else {
												$costo_total = '---';
												$costo = '---';
											}
										} else {
											$costo_total = '---';
											$costo = '---';
										}

										if($costos_user != 'true')
										{
											$costo = '---';
											$costo_total = '---';
										}

										if ($repuesto['cod_proveedor'] == 0) {
											$proveedor = 'Sin proveedor';
										} else {
											$proveedor = $repuesto['cod_proveedor'];
											$sql = "SELECT `codigo`, `nombre`, `telefono`, `ciudad`, `fecha_registro` FROM `proveedores` WHERE codigo = '$proveedor' order by nombre ASC";
											$result = mysqli_query($conexion, $sql);
											$mostrar = mysqli_fetch_row($result);

											if ($mostrar != null)
												$proveedor = ucwords(strtolower($mostrar[1]));
										}

									?>
										<tr title="Proveedor: <?php echo $proveedor ?>">
											<td width="5px" class="text-center py-1 px-0">
												<a class="p-0 text-primary" href="javascript:eliminar_repuesto('<?php echo $i ?>','<?php echo $cod_servicio ?>')">
													<span class="fa fa-times text-danger f-16"></span>
												</a>
											</td>
											<td class="p-1 text-truncate"><?php echo $descripcion_str ?></td>
											<td class="text-right p-1"><?php echo $costo; ?></td>
											<td class="text-center p-1">
												<b><?php echo $cant ?></b>
											</td>
											<td class="text-right p-1"><strong><?php echo $costo_total ?></strong></td>
										</tr>
									<?php
									}
									?>
								</tbody>
							</table>
						</div>
						<?php
						if ($estado != 'ENTREGADO' && $estado != 'ANULADO') {
						?>
							<div class="row clearfix mx-1">
								<input type="text" class="form-control form-control-sm" id="busqueda_repuesto" name="busqueda_repuesto" autocomplete="off" placeholder="Busqueda de repuestos" onKeyUp="mostrar_busqueda_repuestos('<?php echo $cod_servicio ?>');">
							</div>
							<hr class="my-1">
							<div class="conatiner px-0" id="div_tabla_repuestos"></div>
						<?php
						}
						?>
					</div>
				</div>
				<div class="col">
					<div class="row m-0 pt-1">
						<div class="text-center">
							<h5 class="text-warning mb-0">Accesorios</h5>
						</div>
						<div class="table-responsive text-dark text-center p-0">
							<table class="table text-dark table-sm w-100">
								<thead>
									<tr>
										<th class="p-1"></th>
										<th class="p-1"><strong>Producto</strong></th>
										<th width="40px" class="p-1 text-center"><strong>Cant</strong></th>
										<th width="60px" class="p-1 text-center"><strong>Valor</strong></th>
										<th width="75px" class="p-1 text-center"><strong>Total</strong></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$total_accesorios = 0;
									foreach ($accesorios as $i => $accesorio) {
										$cod_accesorio = $accesorio['codigo'];
										$cant = $accesorio['cant'];
										$descripcion_str = $accesorio['descripcion'];
										$valor_unitario = $accesorio['valor_unitario'];

										$descuento_acc = 0;

										if (isset($accesorio['decuento']))
											$descuento_acc = $accesorio['decuento'];

										$valor_total = $cant * ($valor_unitario - $descuento_acc);
										$total_accesorios += $valor_total;

										$valor_unitario = number_format($valor_unitario, 0, '.', '.');
										$valor_total = '$' . number_format($valor_total, 0, '.', '.');

									?>
										<tr>
											<td width="20px" class="text-center py-1 px-0">
												<a class="p-0 text-primary" href="javascript:eliminar_accesorio('<?php echo $i ?>','<?php echo $cod_servicio ?>')">
													<span class="fa fa-times text-danger f-16"></span>
												</a>
											</td>
											<td class="p-1"><?php echo $descripcion_str ?></td>
											<td class="text-center p-1">
												<b><?php echo $cant ?></b>
											</td>
											<td class="text-right p-1"><?php echo $valor_unitario; ?></td>
											<td class="text-right p-1"><strong><?php echo $valor_total ?></strong></td>
										</tr>
										<?php
										if ($estado == 'PENDIENTE' || $estado == 'TERMINADO') {
										?>
											<tr>
												<td width="20px" class="text-center py-1 px-0"></td>
												<td class="p-1 text-muted text-right"><small>Descuento</small></td>
												<td class="text-right p-1" colspan="2">
													<input type="text" class="form-control form-control-sm moneda text-right" name="input_descuento_<?php echo $i ?>" id="input_descuento_<?php echo $i ?>" value="<?php echo number_format($descuento_acc, 0, '.', '.') ?>" onchange="guardar_descuento_acc('<?php echo $i ?>',this.value)">
												</td>
												<td class="text-right p-1"></td>
											</tr>
									<?php
										} else
											echo '$' . number_format($descuento_acc, 0, '.', '.');
									}
									?>
								</tbody>
							</table>
						</div>
						<?php
						if ($estado != 'ENTREGADO' && $estado != 'ANULADO') {
						?>
							<div class="row clearfix mx-1">
								<input type="text" class="form-control form-control-sm" id="busqueda_accesorio" name="busqueda_accesorio" autocomplete="off" placeholder="Busqueda de accesorios" onKeyUp="mostrar_busqueda_accesorios('<?php echo $cod_servicio ?>');">
							</div>
							<hr class="my-1">
							<div class="conatiner px-0" id="div_tabla_accesorios"></div>
						<?php
						}
						?>
					</div>
				</div>
			</div>
			<div class="row m-0 px-1">
				<label class="m-0 col-12 col-sm-12 col-md-3 col-lg-3 py-0 fw-bold text-truncate">Observaciones Técnico: </label>
				<div class="col-12 col-sm-12 col-md-9 col-lg-9 px-0">
					<?php
					$observaciones_tecnico = $informacion['observaciones_tecnico'];

					foreach ($observaciones_tecnico as $i => $obs) {
						$creador_obs = $obs['creador'];
						$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador_obs'";
						$result_e = mysqli_query($conexion, $sql_e);
						$ver_e = mysqli_fetch_row($result_e);
						if ($ver_e != null) {
							$nombre_aux = explode(' ', $ver_e[0]);
							$apellido_aux = explode(' ', $ver_e[1]);
							$creador_obs = $nombre_aux[0] . ' ' . $apellido_aux[0];
						}

						$fecha_obs = date('d-m-Y h:i A', strtotime($obs['fecha']));
					?>
						<div class="row m-0 border-bottom">
							<div class="col m-0 px-1"><?php echo $obs['obs'] ?></div>
							<div class="col-auto m-0 px-1 text-center border-start border-2 lh-1">
								<?php
								echo $obs['local'] . '<br><b>' . $creador_obs . '</b><br>' . $fecha_obs;
								?>
							</div>
						</div>
					<?php
					}
					if ($estado != 'ENTREGADO' && $estado != 'ANULADO') {
					?>
						<div class="row m-0 mt-1">
							<div class="input-group mb-2 px-0">
								<textarea class="form-control form-control-sm" id="input_observaciones_tec" name="input_observaciones_tec" placeholder="Observaciones durante la ejecución del servicio"></textarea>
								<button class="btn btn-sm btn-outline-primary btn-round" id="btn_guardar_obs_tec"><span class="fa fa-save"></span> Guardar</button>
							</div>
						</div>
					<?php
					}
					?>
				</div>
			</div>

			<div class="row m-0 px-1">
				<label class="m-0 col-12 col-sm-12 col-md-3 col-lg-3 py-0 fw-bold text-truncate">Observaciones Ticket: </label>
				<div class="col-12 col-sm-12 col-md-9 col-lg-9 px-0">
					<?php
					$observaciones_ticket = $informacion['observaciones_ticket'];

					foreach ($observaciones_ticket as $i => $obs) {
						$creador_obs = $obs['creador'];
						$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador_obs'";
						$result_e = mysqli_query($conexion, $sql_e);
						$ver_e = mysqli_fetch_row($result_e);
						if ($ver_e != null) {
							$nombre_aux = explode(' ', $ver_e[0]);
							$apellido_aux = explode(' ', $ver_e[1]);
							$creador_obs = $nombre_aux[0] . ' ' . $apellido_aux[0];
						}

						$fecha_obs = date('d-m-Y h:i A', strtotime($obs['fecha']));
					?>
						<div class="row m-0 border-bottom">
							<div class="col m-0 px-1"><?php echo $obs['obs'] ?></div>
							<div class="col-auto m-0 px-1 text-center border-start border-2 lh-1">
								<?php
								echo $obs['local'] . '<br><b>' . $creador_obs . '</b><br>' . $fecha_obs;
								?>
							</div>
						</div>
					<?php
					}
					if ($estado != 'ENTREGADO' && $estado != 'ANULADO') {
					?>
						<div class="row m-0 mt-1">
							<div class="input-group mb-2 px-0">
								<textarea class="form-control form-control-sm" id="input_observaciones_ticket" name="input_observaciones_ticket" placeholder="Observaciones durante la ejecución del servicio"></textarea>
								<button class="btn btn-sm btn-outline-primary btn-round" id="btn_guardar_obs_ticket"><span class="fa fa-save"></span> Guardar</button>
							</div>
						</div>
					<?php
					}
					?>
				</div>
			</div>
		</div>
		<div class="col-12 col-sm-12 col-md-4 col-lg-4 border-3 border-bottom mb-2">
			<div class="text-center row">
				<h4 class="col">Datos de cliente</h4>
			</div>
			<div class="row border-top ml-0 pt-1 border-3 mr-0">
				<p class="row m-0 px-1">
					<span class="col-4 col-sm-4 col-md-6 col-lg-5 text-right text-sm-right text-md-right pr-0 text-truncate">Cédula/NIT: </span>
					<span class="col-8 col-sm-8 col-md-6 col-lg-7 text-left text-truncate"><b class="text-truncate w-100" id="b_id_cliente"><?php echo $cliente['id'] ?></b></span>
				</p>
				<p class="row m-0 px-1">
					<span class="col-4 col-sm-4 col-md-6 col-lg-5 text-right text-sm-right text-md-right pr-0 text-truncate"> Nombre: </span>
					<span class="col-8 col-sm-8 col-md-6 col-lg-7 text-left text-truncate"><b class="text-truncate w-100" id="b_nombre_cliente"><?php echo $cliente['nombre'] ?></b></span>
				</p>
				<p class="row m-0 px-1">
					<span class="col-4 col-sm-4 col-md-6 col-lg-5 text-right text-sm-right text-md-right pr-0 text-truncate"> Teléfono: </span>
					<span class="col-8 col-sm-8 col-md-6 col-lg-7 text-left text-truncate"><b class="text-truncate w-100" id="b_telefono_cliente"><?php echo $cliente['telefono'] ?></b></span>
				</p>
				<?php
				if ($cliente['direccion'] != '') {
				?>
					<p class="row m-0 px-1">
						<span class="col-4 col-sm-4 col-md-6 col-lg-5 text-right text-sm-right text-md-right pr-0 text-truncate"> Dirección: </span>
						<span class="col-8 col-sm-8 col-md-6 col-lg-7 text-left text-truncate"><b class="text-truncate w-100" id="b_direccion_cliente"><?php echo $cliente['direccion'] ?></b></span>
					</p>
				<?php
				}
				if ($cliente['correo'] != '') {
				?>
					<p class="row m-0 px-1">
						<span class="col-4 col-sm-4 col-md-6 col-lg-5 text-right text-sm-right text-md-right pr-0 text-truncate"> Correo: </span>
						<span class="col-8 col-sm-8 col-md-6 col-lg-7 text-left text-truncate"><b class="text-truncate w-100" id="b_correo_cliente"><?php echo $cliente['correo'] ?></b></span>
					</p>
				<?php
				}
				?>
			</div>
			<div class="row mx-0 px-1 px-sm-0 border-top border-3 pt-3" id="tabla_pagos">
				<div class="text-center">
					<h5>Pagos/Abonos</h5>
				</div>
				<div class="table-responsive text-dark text-center p-0">
					<table class="table text-dark table-sm w-100" id="tabla_pagos_servicio">
						<thead>
							<tr class="text-center">
								<th width="30px" class="table-plus text-dark datatable-nosort px-1">#</th>
								<th class="px-1">Método</th>
								<th>Valor</th>
								<th width="80px">Local</th>
								<th width="20px">Caja</th>
								<th width="80px">Creador</th>
								<th width="30px"></th>
							</tr>
						</thead>
						<tbody class="overflow-auto">
							<?php
							$num_item = 1;
							$total_pagos = 0;
							foreach ($pagos as $i => $item) {
								$tipo = $item['tipo'];
								$valor = $item['valor'];
								$local = $item['local'];
								$caja = $item['caja'];
								$creador = $item['creador'];

								$fecha_pago = date('d-m-Y h:i A', strtotime($item['fecha']));

								$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
								$result_e = mysqli_query($conexion, $sql_e);
								$ver_e = mysqli_fetch_row($result_e);
								if ($ver_e != null) {
									$nombre_aux = explode(' ', $ver_e[0]);
									$apellido_aux = explode(' ', $ver_e[1]);
									$creador = $nombre_aux[0];	//.' '.$apellido_aux[0];
								}

								$total_pagos += $valor;
							?>
								<tr role="row" class="odd" title="<?php echo $fecha_pago ?>">
									<td class="text-center p-0 text-muted"><?php echo $num_item ?></td>
									<td class="text-center p-0"><?php echo $tipo ?></td>
									<td class="text-right p-0"><b>$<?php echo number_format($valor, 0, '.', '.') ?></b></td>
									<td class="text-center p-0"><small><?php echo $local ?></small></td>
									<td class="text-center p-0"><?php echo $caja ?></td>
									<td class="text-center p-0"><?php echo $creador ?></td>
									<?php
									if (isset($item['nombre_imagen'])) {
										$url_imagen = 'https://rancho1.witsoft.co/paginas/soportes_transferencias/' . $item['nombre_imagen'];
										if (!file_exists($url_imagen))
											$url_imagen = 'paginas/soportes_transferencias/' . $item['nombre_imagen'];
									?>
										<td class="text-center p-0">
											<a href="#" title="Ver soporte" class="btn btn-sm btn-outline-info btn-round p-0 px-1" onclick="$('#Modal_Ver_Foto').modal('show');document.getElementById('contenedor_foto').src = '<?php echo $url_imagen ?>';">
												<span class="fa fa-image"></span>
											</a>
										<?php
									} else if ($tipo == 'Bancolombia' || $tipo == 'Nequi' || $tipo == 'Daviplata' || $tipo == 'Tarjeta') {
										?>
										<td class="text-center p-0">
											<button type="button" title="Subir soporte de transferencia" class="btn btn-sm btn-outline-info btn-round p-0 px-1" onclick="$('#cod_servicio_upload_transferencia').val(<?php echo $cod_servicio ?>);$('#item_subir').val(<?php echo $i ?>);$('#Modal_Subir_Soporte').modal('show');">
												<i class="fas fa-upload"></i>
											</button>
										</td>
									<?php
									}
									?>
								</tr>
							<?php
								$num_item++;
							}
							$text_saldo = 'text-danger';

							$saldo = $total_servicios + $total_accesorios - $total_pagos;

							if ($saldo == 0)
								$text_saldo = 'text-success';

							if ($estado != 'ENTREGADO' && $estado != 'ANULADO') {
							?>
								<tr>
									<td class="text-center p-1 text-muted"><?php echo $num_item ?></td>
									<td class="text-center" colspan="2">
										<select class="form-control form-control-sm" id="input_metodo_pago" name="input_metodo_pago">
											<option value="">Seleccione uno...</option>
											<option value="Efectivo">Efectivo</option>
											<option value="Tarjeta">Tarjeta</option>
											<option value="Nequi">Nequi</option>
											<option value="Bancolombia">Bancolombia</option>
											<option value="Daviplata">Daviplata</option>
											<option value="Devolución">Devolución</option>
											<option value="Crédito">Crédito</option>
										</select>
									</td>
									<td class="text-center" colspan="2">
										<input type="text" class="form-control form-control-sm moneda" id="input_valor_pago" name="input_valor_pago" placeholder="Valor" autocomplete="off">
									</td>
									<td class="text-center">
										<button type="button" class="btn btn-sm btn-outline-success btn-round p-0 px-1" id="btn_agregar_pago_servicio">+</button>
									</td>
								</tr>

							<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>


			<div class="row border-top m-0 pt-1 border-3">
				<p class="row m-0 p-0 h5">
					<span class="col-7 col-sm-7 col-md-7 col-lg-7 text-right text-sm-right text-md-right pr-0 text-truncate">
						<?php
						if ($estado != 'ENTREGADO' && $estado != 'ANULADO') {
						?>
							<small><a class="p-0 text-primary" href="javascript:document.getElementById('span_input_total').hidden = false;document.getElementById('span_total').hidden = true;document.getElementById('a_edit_total').hidden = true;" id="a_edit_total">
									<span class="fa fa-edit text-warning"></span>
								</a></small>
						<?php
						}
						?>
						Total Servicios: </span>
					<span class="col-5 col-sm-5 col-md-5 col-lg-5 text-right text-truncate" id="span_total"><b class="text-truncate w-100">$<?php echo number_format($total_servicios, 0, '.', '.') ?></b></span>
					<span class="col-5 col-sm-5 col-md-5 col-lg-5 text-right text-truncate row m-0 px-1" id="span_input_total" hidden>
						<input type="text" name="input_nuevo_total" id="input_nuevo_total" class="form-control form-control-sm moneda text-right col" value="<?php echo number_format($total_servicios, 0, '.', '.') ?>">
						<button type="button" class="btn btn-sm btn-outline-success btn-round p-0 px-1 col-auto" id="btn_guardar_total">Guardar</button>
					</span>
				</p>
				<p class="row m-0 p-0 h5">
					<span class="col-7 col-sm-7 col-md-7 col-lg-7 text-right text-sm-right text-md-right pr-0 text-truncate">Total Accesorios: </span>
					<span class="col-5 col-sm-5 col-md-5 col-lg-5 text-right text-truncate"><b class="text-truncate w-100">$<?php echo number_format($total_accesorios, 0, '.', '.') ?></b></span>
				</p>

				<?php
				if ($saldo != 0) {
					$color_moneda = '';
					if ($saldo < 0)
						$color_moneda = 'text-success';
					else
						$color_moneda = 'text-danger';
				?>
					<p class="row m-0 p-0 h5">
						<span class="col-7 col-sm-7 col-md-7 col-lg-7 text-right text-sm-right text-md-right pr-0 text-truncate">Total Abonos: </span>
						<span class="col-5 col-sm-5 col-md-5 col-lg-5 text-right text-truncate"><b class="text-truncate w-100">$<?php echo number_format($total_pagos, 0, '.', '.') ?></b></span>
					</p>
					<p class="row m-0 p-0 h5">
						<span class="col-7 col-sm-7 col-md-7 col-lg-7 text-right text-sm-right text-md-right pr-0 text-truncate"> Saldo: </span>
						<span class="col-5 col-sm-5 col-md-5 col-lg-5 text-right text-truncate"><b class="text-truncate w-100 <?php echo $color_moneda ?>">$<?php echo number_format($saldo, 0, '.', '.') ?></b></span>
					</p>
				<?php
				} else {
				?>
					<p class="row m-0 p-0 h4">
						<span class="badge bg-success">CANCELADO</span>
					</p>
				<?php
				}
				?>
			</div>
		</div>
	</div>

	<div class="modal-footer p-1">
		<div class="col m-0">
			<div class="row m-0 px-1">
				<button type="button" class="btn btn-sm btn-outline-info btn-round col-auto" data-bs-dismiss="modal" onclick="imprimir_ticket_servicio('<?php echo $cod_servicio ?>')">Imprimir ticket</button>
				<?php
				if ($estado == 'PENDIENTE') {
				?>
					<div class="col-auto px-0" ondblclick="document.getElementById('div_input_solucion').hidden = false;this.hidden = true;">
						<label>Solución: </label> <?php echo $informacion['solucion'] ?>
					</div>
					<div class="col-auto px-0" id="div_input_solucion" hidden>
						<div class="row">
							<label for="input_solucion" class="col-auto">Solución: </label>
							<select class="form-control form-control-sm col" id="input_solucion" name="input_solucion" onchange="guardar_info('solucion='+this.value)">
								<option value="">Seleccione una...</option>
								<option value="REPARADO" <?php echo $sol_si ?>>REPARADO</option>
								<option value="NO REPARADO" <?php echo $sol_no ?>>NO REPARADO</option>
							</select>
						</div>
					</div>
				<?php
				} else {
				?>
					<div class="col-auto px-0">
						<label>Solución: </label> <?php echo $informacion['solucion'] ?>
					</div>
				<?php
				}
				?>
			</div>
		</div>
		<div class="col-auto m-0 text-right" id="div_confirmacion_estado" hidden>
			<span>Cambiar a <b class="text-danger" id="estado_cambio"></b></span>
			<button type="button" class="btn btn-sm btn-outline-secondary btn-round" onclick="confirmacion_estado('',true)" style="cursor: pointer;">
				CANCELAR
			</button>
			<button type="button" class="btn btn-sm btn-info btn-round" onclick="guardar_info('estado='+document.getElementById('estado_cambio').innerText)" style="cursor: pointer;">
				CONFIRMAR
			</button>
		</div>
		<div class="col-auto m-0 text-right" id="div_btn_estado">
			<?php
			if ($estado == 'PENDIENTE') {
			?>
				<button type="button" class="btn btn-sm btn-outline-success btn-round" onclick="confirmacion_estado('TERMINADO')" style="cursor: pointer;">
					Terminado
				</button>
				<button type="button" class="btn btn-sm btn-outline-primary btn-round" onclick="confirmacion_estado('ENTREGADO')" style="cursor: pointer;">
					Entregado
				</button>
				<button type="button" class="btn btn-sm btn-outline-info btn-round" onclick="confirmacion_estado('EN ESPERA')" style="cursor: pointer;">
					En Espera
				</button>
			<?php
			} else if ($estado == 'TERMINADO') {
			?>
				<button type="button" class="btn btn-sm btn-outline-danger btn-round" onclick="confirmacion_estado('PENDIENTE')" style="cursor: pointer;">
					Pendiente
				</button>
				<button type="button" class="btn btn-sm btn-outline-primary btn-round" onclick="confirmacion_estado('ENTREGADO')" style="cursor: pointer;">
					Entregado
				</button>
				<button type="button" class="btn btn-sm btn-outline-info btn-round" onclick="confirmacion_estado('EN ESPERA')" style="cursor: pointer;">
					En Espera
				</button>
			<?php
			}

			if ($estado == 'ENTREGADO' and $rol == 'Administrador') {
			?>
				<button type="button" class="btn btn-sm btn-outline-danger btn-round" onclick="confirmacion_estado('PENDIENTE')" style="cursor: pointer;">
					Pendiente
				</button>
			<?php
			}
			if ($estado == 'EN ESPERA') {
			?>
				<button type="button" class="btn btn-sm btn-outline-danger btn-round" onclick="confirmacion_estado('PENDIENTE')" style="cursor: pointer;">
					Pendiente
				</button>
			<?php
			}
			?>
			<button type="button" class="btn btn-sm btn-secondary btn-round ml-5" data-bs-dismiss="modal" id="close_Modal_ver_servicio">Cerrar</button>
		</div>
	</div>

	<!-- Modal QR patron-->
	<div class="modal fade" id="Modal_qr_patron" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="overflow-y: scroll;">
		<div class="modal-dialog" role="document" style="width:25% !important">
			<div class="modal-content" id="div_qr_patron"></div>
		</div>
	</div>


	<!-- Modal Subir Foto-->
	<div class="modal fade" id="Modal_Subir_Foto" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="overflow-y: scroll;">
		<div class="modal-dialog" role="document">
			<div class="modal-content">

				<div class="modal-header text-center">
					<h3 class="modal-title">Cargar Foto</h3>
				</div>

				<div class="modal-body">
					<form id="frm_foto" enctype="multipart/form-data">
						<input type="number" name="cod_servicio_upload" id="cod_servicio_upload" hidden="">
						<div class="row">
							<div class="col">
								<div class="custom-file">
									<label class="form-label" for="archivo_foto">Seleccione un archivo (png, jpeg, jpg)</label>
									<input class="form-control form-control-sm" name="archivo_foto[]" id="archivo_foto[]" type="file" accept="image/*" multiple="" />
								</div>
								<div class="progress progress-sm mb-3">
									<div id="progress_bar_upload" class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
							</div>
							<div class="col-auto my-auto">
								<button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_subir">Subir</button>
							</div>
						</div>
					</form>
				</div>

				<div class="modal-body">
					<button type="button" class="btn btn-sm btn-secondary btn-round" id="btn_cancelar_subir" onclick="$('#Modal_Subir_Foto').modal('toggle');">Cancelar</button>
				</div>

			</div>
		</div>
	</div>

	<!-- Modal Subir Soporte-->
	<div class="modal fade" id="Modal_Subir_Soporte" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="overflow-y: scroll;">
		<div class="modal-dialog" role="document">
			<div class="modal-content">

				<div class="modal-header text-center">
					<h3 class="modal-title">Cargar Soporte</h3>
				</div>

				<div class="modal-body">
					<form id="frm_soporte" enctype="multipart/form-data">
						<input type="number" name="cod_servicio_upload_transferencia" id="cod_servicio_upload_transferencia" hidden="">
						<input type="number" name="item_subir" id="item_subir" hidden="">
						<div class="row">
							<div class="col">
								<div class="custom-file">
									<label class="form-label" for="archivo_soporte">Seleccione un archivo (png, jpeg, jpg)</label>
									<input class="form-control form-control-sm" name="archivo_soporte" id="archivo_soporte" type="file" accept="image/*" />
								</div>
								<div class="progress progress-sm mb-3">
									<div id="progress_bar_upload_transferencia" class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
							</div>
							<div class="col-auto my-auto">
								<button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_subir_soporte">Subir</button>
							</div>
						</div>
					</form>
				</div>

				<div class="modal-body">
					<button type="button" class="btn btn-sm btn-secondary btn-round" id="btn_cancelar_subir_soporte" onclick="$('#Modal_Subir_Soporte').modal('toggle');">Cancelar</button>
				</div>

			</div>
		</div>
	</div>

	<!-- Modal Ver foto-->
	<div class="modal fade" id="Modal_Ver_Foto" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content" id="div_modal_ver_soporte">
				<img class="container py-2" src="" id="contenedor_foto">
				<div class="modal-footer">
					<button type="button" class="btn btn-sm btn-secondary btn-round" onclick="$('#Modal_Ver_Foto').modal('toggle');">Cerrar</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Eliminar Imagen-->
	<div class="modal fade" id="Modal_Eliminar_Imagen" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header text-center p-2">
					<h5 class="modal-title">Seguro desea eliminar esta Imagen?</h5>
				</div>
				<div class="modal-body">
					<input type="number" name="cod_servicio_eliminar" id="cod_servicio_eliminar" hidden="">
					<input type="number" name="item_eliminar" id="item_eliminar" hidden="">
					<div class="row">
						<div class="col text-center">
							<button type="button" class="btn btn-sm btn-secondary btn-round btn-block px-5" onclick="$('#Modal_Eliminar_Imagen').modal('toggle');" id="close_Modal_Eliminar_Imagen">NO</button>
						</div>
						<div class="col text-center">
							<button type="button" class="btn btn-sm btn-outline-primary btn-round btn-block" id="btnEliminarImagen">SI, Eliminar</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$('input.moneda').keyup(function(event) {
			if (event.which >= 37 && event.which <= 40) {
				event.preventDefault();
			}
			$(this).val(function(index, value) {
				return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
			});
		});

		$('#input_valor').keypress(function(e) {
			if (e.keyCode == 13)
				$('#btn_agregar_item_servicio').click();
		});
		$('#input_valor_pago').keypress(function(e) {
			if (e.keyCode == 13)
				$('#btn_agregar_pago_servicio').click();
		});

		$('#btn_guardar_obs').click(function() {
			datos = 'observaciones=' + document.getElementById("input_observaciones").value;
			guardar_info(datos);
		});

		$('#btn_guardar_total').click(function() {
			datos = 'total_servicios=' + document.getElementById("input_nuevo_total").value;
			guardar_info(datos);
		});

		$('#btn_guardar_obs_tec').click(function() {
			datos = 'observaciones_tecnico=' + document.getElementById("input_observaciones_tec").value;
			guardar_info(datos);
		});

		$('#btn_guardar_obs_ticket').click(function() {
			datos = 'observaciones_ticket=' + document.getElementById("input_observaciones_ticket").value;
			guardar_info(datos);
		});

		$('#btn_guardar_fecha').click(function() {
			datos = 'fecha=' + document.getElementById("input_fecha_e").value;
			guardar_info(datos);
		});

		$('#btn_guardar_hora').click(function() {
			datos = 'hora=' + document.getElementById("input_hora_e").value;
			guardar_info(datos);
		});

		$('#btn_guardar_tecnico').click(function() {
			datos = 'tecnico=' + document.getElementById("input_tecnico_serv").value;
			guardar_info(datos);
		});

		$('#btn_agregar_item_servicio').click(function() {
			document.getElementById('div_loader').style.display = 'block';
			document.getElementById("btn_agregar_item_servicio").disabled = true;
			input_cant = document.getElementById("input_cant").value;
			input_valor = document.getElementById("input_valor").value;
			input_descripcion = document.getElementById("input_descripcion").value;
			if (input_cant != '' && input_valor != '' && input_descripcion != '') {
				$.ajax({
					type: "POST",
					data: datos + "&cod_servicio=<?php echo $cod_servicio ?>" + "&input_cant=" + input_cant + "&input_valor=" + input_valor + "&input_descripcion=" + input_descripcion,
					url: "procesos/agregar_item_servicio_2.php",
					success: function(r) {
						datos = jQuery.parseJSON(r);
						if (datos['consulta'] == 1) {
							w_alert({
								titulo: 'Item agregado con exito',
								tipo: 'success'
							});
							document.getElementById('div_loader').style.display = 'block';
							$('#div_modal_servicio').load('detalles/detalles_servicio.php/?cod_servicio=<?php echo $cod_servicio ?>', function() {
								cerrar_loader();
							});
							notificacion_transacciones();
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
				if (input_cant == '') {
					w_alert({
						titulo: 'Ingrese la cantidad del item',
						tipo: 'danger'
					});
					document.getElementById("input_cant").focus();
				} else if (input_valor == '') {
					w_alert({
						titulo: 'Ingrese el valor del item',
						tipo: 'danger'
					});
					document.getElementById("input_valor").focus();
				} else if (input_descripcion == '') {
					w_alert({
						titulo: 'Escriba la descripción del item',
						tipo: 'danger'
					});
					document.getElementById("input_descripcion").focus();
				}
			}

			cerrar_loader();
			document.getElementById("btn_agregar_item_servicio").disabled = false;
		});

		function cambio_select_seguridad_s(seleccion, cod_servicio) {
			if (seleccion == 'Patron') {
				$('#Modal_qr_patron').modal('show');
				document.getElementById('div_loader').style.display = 'block';
				$('#div_qr_patron').load('paginas/detalles/qr_patron.php/?cod_servicio=' + cod_servicio + '&tipo=Patron', cerrar_loader());
			}
			if (seleccion == 'Patron Aplicaciones') {
				$('#Modal_qr_patron').modal('show');
				document.getElementById('div_loader').style.display = 'block';
				$('#div_qr_patron').load('paginas/detalles/qr_patron.php/?cod_servicio=' + cod_servicio + '&tipo=Patron_Aplicaciones', cerrar_loader());
			}
		}


		$('#btn_agregar_item_seguridad').click(function() {
			document.getElementById('div_loader').style.display = 'block';
			document.getElementById("btn_agregar_item_seguridad").disabled = true;
			input_valor_seguridad = document.getElementById("input_valor_seguridad_2").value;
			input_seguridad = document.getElementById("input_seguridad_2").value;
			if (input_seguridad != '' && input_valor_seguridad != '') {
				$.ajax({
					type: "POST",
					data: "cod_servicio=<?php echo $cod_servicio ?>&input_valor_seguridad=" + input_valor_seguridad + "&input_seguridad=" + input_seguridad,
					url: "procesos/agregar_seguridad_servicio.php",
					success: function(r) {
						datos = jQuery.parseJSON(r);
						if (datos['consulta'] == 1) {
							w_alert({
								titulo: 'Item agregado con exito',
								tipo: 'success'
							});
							mostrar_servicio(<?php echo $cod_servicio ?>);
						} else {
							w_alert({
								titulo: datos['consulta'],
								tipo: 'danger'
							});
							if (datos['consulta'] == 'Reload') {
								document.getElementById('div_login').style.display = 'block';
								cerrar_loader();
								
							}
							if (datos['consulta'] == 'Reload') {
								document.getElementById('div_login').style.display = 'block';
								cerrar_loader();
								
							}
						}
						cerrar_loader();
						document.getElementById("btn_agregar_item_seguridad").disabled = false;
					}
				});
			} else {
				if (input_seguridad == '') {
					w_alert({
						titulo: 'Seleccione el tipo de seguridad',
						tipo: 'danger'
					});
					document.getElementById("input_seguridad").focus();
				} else if (input_valor_seguridad == '') {
					w_alert({
						titulo: 'Seleccione el tipo de seguridad',
						tipo: 'danger'
					});
					document.getElementById("input_valor_seguridad").focus();
				}
				cerrar_loader();
				document.getElementById("btn_agregar_item_seguridad").disabled = false;
			}
		});

		$('#btn_agregar_pago_servicio').click(function() {
			document.getElementById('div_loader').style.display = 'block';
			document.getElementById("btn_agregar_pago_servicio").disabled = true;
			input_metodo_pago = document.getElementById("input_metodo_pago").value;
			input_valor_pago = document.getElementById("input_valor_pago").value;
			if (input_metodo_pago != '' && input_valor_pago != '') {
				$.ajax({
					type: "POST",
					data: "cod_servicio=<?php echo $cod_servicio ?>&input_metodo_pago=" + input_metodo_pago + "&input_valor_pago=" + input_valor_pago,
					url: "procesos/agregar_pago_servicio.php",
					success: function(r) {
						datos = jQuery.parseJSON(r);
						if (datos['consulta'] == 1) {
							w_alert({
								titulo: 'Pago agregado con exito',
								tipo: 'success'
							});
							mostrar_servicio(<?php echo $cod_servicio ?>);
						} else {
							w_alert({
								titulo: datos['consulta'],
								tipo: 'danger'
							});
							if (datos['consulta'] == 'Reload') {
								document.getElementById('div_login').style.display = 'block';
								cerrar_loader();
								
							}
							if (datos['consulta'] == 'Reload') {
								document.getElementById('div_login').style.display = 'block';
								cerrar_loader();
								
							}
							cerrar_loader();
							document.getElementById("btn_agregar_pago_servicio").disabled = false;
						}
					}
				});
			} else {
				if (input_metodo_pago == '') {
					w_alert({
						titulo: 'Seleccione el metodo de pago',
						tipo: 'danger'
					});
					document.getElementById("input_metodo_pago").focus();
					cerrar_loader();
					document.getElementById("btn_agregar_pago_servicio").disabled = false;
				} else if (input_valor_pago == '') {
					w_alert({
						titulo: 'Ingrese el valor del pago',
						tipo: 'danger'
					});
					document.getElementById("input_valor_pago").focus();
					cerrar_loader();
					document.getElementById("btn_agregar_pago_servicio").disabled = false;
				}

			}
		});

		function confirmacion_estado(estado, atras = false) {
			if (atras == false) {
				document.getElementById('div_btn_estado').hidden = true;
				document.getElementById('div_confirmacion_estado').hidden = false;
			} else {
				document.getElementById('div_btn_estado').hidden = false;
				document.getElementById('div_confirmacion_estado').hidden = true;
			}
			document.getElementById('estado_cambio').innerHTML = estado;
		}

		function guardar_info(datos) {
			document.getElementById('div_loader').style.display = 'block';
			$.ajax({
				type: "POST",
				data: datos + "&cod_servicio=<?php echo $cod_servicio ?>",
				url: "procesos/guardar_info_servicio.php",
				success: function(r) {
					datos = jQuery.parseJSON(r);
					if (datos['consulta'] == 1) {
						w_alert({
							titulo: 'Información guardada con éxito',
							tipo: 'success'
						});

						$('#div_modal_servicio').load('paginas/detalles/detalles_servicio.php/?cod_servicio=<?php echo $cod_servicio ?>', function() {
							cerrar_loader();
						});
						cargar_servicios_2('Todos');
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

		function eliminar_item(num_item) {
			document.getElementById('div_loader').style.display = 'block';
			$.ajax({
				type: "POST",
				data: "num_item=" + num_item + "&cod_servicio=<?php echo $cod_servicio ?>",
				url: "procesos/eliminar_item_servicio_2.php",
				success: function(r) {
					datos = jQuery.parseJSON(r);
					if (datos['consulta'] == 1) {
						w_alert({
							titulo: 'Item eliminado con éxito',
							tipo: 'success'
						});

						$('#div_modal_servicio').load('detalles/detalles_servicio.php/?cod_servicio=<?php echo $cod_servicio ?>', function() {
							cerrar_loader();
						});
						notificacion_transacciones();
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

		function cambiar_estado_servicio(cod_servicio, estado) {
			document.getElementById('div_loader').style.display = 'block';
			$.ajax({
				type: "POST",
				data: datos + "&cod_servicio=<?php echo $cod_servicio ?>",
				url: "procesos/guardar_info_servicio.php",
				success: function(r) {
					datos = jQuery.parseJSON(r);
					if (datos['consulta'] == 1) {
						w_alert({
							titulo: 'Información guardada con éxito',
							tipo: 'success'
						});

						$('#div_modal_servicio').load('paginas/detalles/detalles_servicio.php/?cod_servicio=<?php echo $cod_servicio ?>', function() {
							cerrar_loader();
						});
						notificacion_transacciones();
						cargar_servicios_2('Todos');
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

		function mostrar_busqueda_repuestos(cod_servicio) {
			var busqueda = document.getElementById("busqueda_repuesto").value;
			busqueda = busqueda.replace(/ /g, "***");
			if (busqueda != '') {
				if (busqueda.length > 2) {
					document.getElementById('div_loader').style.display = 'block';
					$('#div_tabla_repuestos').load('paginas/vistas_pdv/pdv_repuestos.php/?consulta=' + busqueda + '&cod_servicio=' + cod_servicio, function() {
						cerrar_loader();
					});
				} else {
					document.getElementById('div_loader').style.display = 'block';
					$('#div_tabla_repuestos').load('paginas/vistas_pdv/pdv_repuestos.php/?consulta0=' + busqueda + '&cod_servicio=' + cod_servicio, function() {
						cerrar_loader();
					});
				}
			}
		}


		function mostrar_busqueda_accesorios(cod_servicio) {
			var busqueda = document.getElementById("busqueda_accesorio").value;
			busqueda = busqueda.replace(/ /g, "***");
			if (busqueda != '') {
				if (busqueda.length > 2) {
					document.getElementById('div_loader').style.display = 'block';
					$('#div_tabla_accesorios').load('paginas/vistas_pdv/pdv_accesorios.php/?consulta=' + busqueda + '&cod_servicio=' + cod_servicio, function() {
						cerrar_loader();
					});
				} else {
					document.getElementById('div_loader').style.display = 'block';
					$('#div_tabla_accesorios').load('paginas/vistas_pdv/pdv_accesorios.php/?consulta0=' + busqueda + '&cod_servicio=' + cod_servicio, function() {
						cerrar_loader();
					});
				}
			}
		}

		function agregar_repuesto(cod_producto, num_inventario, cod_servicio, cant) {
			document.getElementById('div_loader').style.display = 'block';
			if (cant != '' && cant > 0) {
				$.ajax({
					type: "POST",
					data: "cod_producto=" + cod_producto + "&cod_servicio=" + cod_servicio + "&num_inventario=" + num_inventario + "&cant=" + cant,
					url: "procesos/agregar_repuesto_servicio.php",
					success: function(r) {
						datos = jQuery.parseJSON(r);
						if (datos['consulta'] == 1) {
							w_alert({
								titulo: 'Repuesto agregado correctamente',
								tipo: 'success'
							});
							mostrar_servicio(cod_servicio);
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
			} else {
				w_alert({
					titulo: 'Ingrese una cantidad válida. Mayor o igual a 1',
					tipo: 'danger'
				});
				cerrar_loader();
			}

			$('#Modal_Cantidad_Producto').toggle();
			$('.modal-backdrop').remove();
			document.querySelector("body").style.overflow = "auto";
		}

		function eliminar_repuesto(num_item, cod_servicio) {
			document.getElementById('div_loader').style.display = 'block';
			$.ajax({
				type: "POST",
				data: "num_item=" + num_item + "&cod_servicio=" + cod_servicio,
				url: "procesos/eliminar_repuesto_servicio.php",
				success: function(r) {
					datos = jQuery.parseJSON(r);
					if (datos['consulta'] == 1) {
						w_alert({
							titulo: 'Repuesto eliminado correctamente',
							tipo: 'success'
						});
						mostrar_servicio(cod_servicio);
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

			$('#Modal_Cantidad_Producto').toggle();
			$('.modal-backdrop').remove();
			document.querySelector("body").style.overflow = "auto";
		}

		function eliminar_accesorio(num_item, cod_servicio) {
			document.getElementById('div_loader').style.display = 'block';
			$.ajax({
				type: "POST",
				data: "num_item=" + num_item + "&cod_servicio=" + cod_servicio,
				url: "procesos/eliminar_accesorio_servicio.php",
				success: function(r) {
					datos = jQuery.parseJSON(r);
					if (datos['consulta'] == 1) {
						w_alert({
							titulo: 'Repuesto eliminado correctamente',
							tipo: 'success'
						});
						mostrar_servicio(cod_servicio);
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

			$('#Modal_Cantidad_Producto').toggle();
			$('.modal-backdrop').remove();
			document.querySelector("body").style.overflow = "auto";
		}

		function agregar_accesorio(cod_producto, num_inventario, cod_servicio, cant) {
			document.getElementById('div_loader').style.display = 'block';
			if (cant != '' && cant > 0) {
				$.ajax({
					type: "POST",
					data: "cod_producto=" + cod_producto + "&cod_servicio=" + cod_servicio + "&num_inventario=" + num_inventario + "&cant=" + cant,
					url: "procesos/agregar_accesorio_servicio.php",
					success: function(r) {
						datos = jQuery.parseJSON(r);
						if (datos['consulta'] == 1) {
							w_alert({
								titulo: 'Repuesto agregado correctamente',
								tipo: 'success'
							});
							mostrar_servicio(cod_servicio);
						} else {
							if (datos['consulta'] == 'Reload')
								location.reload();
							else {
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
					}
				});
			} else {
				w_alert({
					titulo: 'Ingrese una cantidad válida. Mayor o igual a 1',
					tipo: 'danger'
				});
				cerrar_loader();
			}

			$('#Modal_Cantidad_Producto').toggle();
			$('.modal-backdrop').remove();
			document.querySelector("body").style.overflow = "auto";
		}

		function guardar_info_equipo_s(cod_servicio, item, valor) {
			document.getElementById('div_loader').style.display = 'block';
			if (valor != '') {
				$.ajax({
					type: "POST",
					data: "cod_servicio=<?php echo $cod_servicio ?>&item=" + item + "&valor=" + valor,
					url: "procesos/cambiar_info_equipo.php",
					success: function(r) {
						datos = jQuery.parseJSON(r);
						if (datos['consulta'] == 1) {
							w_alert({
								titulo: 'Info agregada con exito',
								tipo: 'success'
							});
							mostrar_servicio(<?php echo $cod_servicio ?>);
						} else {
							w_alert({
								titulo: datos['consulta'],
								tipo: 'danger'
							});
							if (datos['consulta'] == 'Reload') {
								document.getElementById('div_login').style.display = 'block';
								cerrar_loader();
								
							}
						}
					}
				});
			} else {
				w_alert({
					titulo: 'Ingrese la info',
					tipo: 'danger'
				});
				document.getElementById("input_info_" + item).focus();
			}

			cerrar_loader();
		}

		function guardar_descuento_acc(item, valor) {
			document.getElementById('div_loader').style.display = 'block';
			if (valor != '') {
				$.ajax({
					type: "POST",
					data: "cod_servicio=<?php echo $cod_servicio ?>&item=" + item + "&valor=" + valor,
					url: "procesos/guardar_descuento_acc.php",
					success: function(r) {
						datos = jQuery.parseJSON(r);
						if (datos['consulta'] == 1) {
							w_alert({
								titulo: 'Descuento guardado con exito',
								tipo: 'success'
							});
							mostrar_servicio(<?php echo $cod_servicio ?>);
						} else {
							w_alert({
								titulo: datos['consulta'],
								tipo: 'danger'
							});
							if (datos['consulta'] == 'Reload') {
								document.getElementById('div_login').style.display = 'block';
								cerrar_loader();
								
							}
						}
					}
				});
			} else {
				w_alert({
					titulo: 'Ingrese el valor del descuento',
					tipo: 'danger'
				});
				document.getElementById("input_descuento_" + item).focus();
			}

			cerrar_loader();
		}

		function anular_servicio(cod_servicio) {
			document.getElementById('div_loader').style.display = 'block';
			$.ajax({
				type: "POST",
				data: "cod_servicio=" + cod_servicio,
				url: "procesos/anular_servicio.php",
				success: function(r) {
					datos = jQuery.parseJSON(r);
					if (datos['consulta'] == 1) {
						w_alert({
							titulo: 'Servicio Anulado con exito',
							tipo: 'success'
						});
						mostrar_servicio(<?php echo $cod_servicio ?>);
					} else {
						w_alert({
							titulo: datos['consulta'],
							tipo: 'danger'
						});
						if (datos['consulta'] == 'Reload') {
							document.getElementById('div_login').style.display = 'block';
							cerrar_loader();
							
						}
					}
				}
			});

			cerrar_loader();
		}

		function agregar_garantia(cod_servicio) {
			document.getElementById('div_loader').style.display = 'block';
			$.ajax({
				type: "POST",
				data: "cod_servicio=" + cod_servicio,
				url: "procesos/agregar_garantia_servicio.php",
				success: function(r) {
					datos = jQuery.parseJSON(r);
					if (datos['consulta'] == 1) {
						cod_garantia = datos['cod_garantia'];
						w_alert({
							titulo: 'Garantía Creada con exito',
							tipo: 'success'
						});
						mostrar_servicio(cod_garantia);
					} else {
						w_alert({
							titulo: datos['consulta'],
							tipo: 'danger'
						});
						if (datos['consulta'] == 'Reload') {
							document.getElementById('div_login').style.display = 'block';
							cerrar_loader();
							
						}
					}
				}
			});

			cerrar_loader();
		}

		// Subir Foto

		var barra_estado = document.getElementById('progress_bar_upload');

		$('#btn_subir').click(function() {
			document.getElementById("btn_subir").disabled = true;
			barra_estado.classList.remove('bg-success');
			barra_estado.classList.add('bg-info');

			var datos = new FormData($("#frm_foto")[0]);

			var peticion = new XMLHttpRequest();

			peticion.upload.addEventListener("progress", barra_progreso, false);
			peticion.addEventListener("load", proceso_completo, false);
			peticion.addEventListener("error", error_carga, false);
			peticion.addEventListener("abort", carga_abortada, false);

			peticion.open("POST", "procesos/subir_foto.php");
			peticion.send(datos);
		});

		function barra_progreso(event) {
			barra_estado.style.width = '0';
			porcentaje = Math.round((event.loaded / event.total) * 100);
			barra_estado.style.width = porcentaje + '%';
		}

		function proceso_completo(event) {
			datos = jQuery.parseJSON(event.target.responseText);
			if (datos['consulta'] == 1) {
				$('#frm_foto')[0].reset();
				barra_estado.classList.remove('bg-info');
				barra_estado.classList.add('bg-success');

				document.getElementById("btn_subir").disabled = false;
				w_alert({
					titulo: 'Foto cargada Correctamente',
					tipo: 'success'
				});
				document.getElementById('div_loader').style.display = 'block';
				$("#Modal_Subir_Foto").modal('toggle');

				mostrar_servicio(<?php echo $cod_servicio ?>);
			} else {
				if (datos['consulta'] == 'Reload') {
					document.getElementById('div_login').style.display = 'block';
					cerrar_loader();
					
				} else
					w_alert({
						titulo: datos['consulta'],
						tipo: 'danger'
					});

				document.getElementById("btn_subir").disabled = false;
			}
		}

		function error_carga(event) {
			w_alert({
				titulo: 'Error al cargar el soporte',
				tipo: 'danger'
			});
			document.getElementById("btn_subir").disabled = false;
		}

		function carga_abortada(event) {
			w_alert({
				titulo: 'Carga de soporte cancelada',
				tipo: 'danger'
			});
			document.getElementById("btn_subir").disabled = false;
		}

		// Subir soporte trasnferencia

		var barra_estado_transferencia = document.getElementById('progress_bar_upload_transferencia');

		$('#btn_subir_soporte').click(function() {
			document.getElementById("btn_subir_soporte").disabled = true;
			progress_bar_upload_transferencia.classList.remove('bg-success');
			progress_bar_upload_transferencia.classList.add('bg-info');

			var datos = new FormData($("#frm_soporte")[0]);

			var peticion = new XMLHttpRequest();

			peticion.upload.addEventListener("progress", barra_progreso_2, false);
			peticion.addEventListener("load", proceso_completo_2, false);
			peticion.addEventListener("error", error_carga_2, false);
			peticion.addEventListener("abort", carga_abortada_2, false);

			peticion.open("POST", "procesos/subir_soporte_trasnferencia.php");
			peticion.send(datos);
		});

		function barra_progreso_2(event) {
			barra_estado_transferencia.style.width = '0';
			porcentaje = Math.round((event.loaded / event.total) * 100);
			barra_estado_transferencia.style.width = porcentaje + '%';
		}

		function proceso_completo_2(event) {
			datos = jQuery.parseJSON(event.target.responseText);
			if (datos['consulta'] == 1) {
				$('#frm_foto')[0].reset();
				barra_estado_transferencia.classList.remove('bg-info');
				barra_estado_transferencia.classList.add('bg-success');

				document.getElementById("btn_subir_soporte").disabled = false;
				w_alert({
					titulo: 'Soporte cargado Correctamente',
					tipo: 'success'
				});
				document.getElementById('div_loader').style.display = 'block';
				$("#Modal_Subir_Soporte").modal('toggle');

				mostrar_servicio(<?php echo $cod_servicio ?>);
			} else {
				if (datos['consulta'] == 'Reload') {
					document.getElementById('div_login').style.display = 'block';
					cerrar_loader();
					
				} else
					w_alert({
						titulo: datos['consulta'],
						tipo: 'danger'
					});

				document.getElementById("btn_subir_soporte").disabled = false;
			}
		}

		function error_carga_2(event) {
			w_alert({
				titulo: 'Error al cargar el soporte',
				tipo: 'danger'
			});
			document.getElementById("btn_subir_soporte").disabled = false;
		}

		function carga_abortada_2(event) {
			w_alert({
				titulo: 'Carga de soporte cancelada',
				tipo: 'danger'
			});
			document.getElementById("btn_subir_soporte").disabled = false;
		}

		$('#btnEliminarImagen').click(function() {
			document.getElementById('div_loader').style.display = 'block';
			$item = document.getElementById('item_eliminar').value;
			$.ajax({
				type: "POST",
				data: "cod_servicio=<?php echo $cod_servicio ?>&item=" + $item,
				url: "procesos/eliminar_foto.php",
				success: function(r) {
					datos = jQuery.parseJSON(r);
					if (datos['consulta'] == 1) {
						w_alert({
							titulo: 'Foto eliminada con exito',
							tipo: 'success'
						});
						$('#close_Modal_Eliminar_Imagen').click();
						mostrar_servicio(<?php echo $cod_servicio ?>);
					} else {
						w_alert({
							titulo: datos['consulta'],
							tipo: 'danger'
						});
						if (datos['consulta'] == 'Reload') {
							document.getElementById('div_login').style.display = 'block';
							cerrar_loader();
							
						}
					}
				}
			});
			cerrar_loader();
		});
	</script>


<?php
} else
	header("Location:../../login.php");
?>