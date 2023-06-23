<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();
$conexion = $obj->conexion();

$sql = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `foto`, `telefono`, `rol`, `fecha_registro`, `estado` FROM `usuarios` WHERE  cedula != 2023 AND estado != 'ELIMINADO'";
$result = mysqli_query($conexion, $sql);

$nombre_tabla = 'Usuarios';
?>
<!-- Tabla Usuarios -->
<div class="card">
	<div class="card-body p-2">
		<div class="d-sm-flex align-items-center row m-0 mb-2">
			<div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6">
				<h4 class="card-title"><?php echo $nombre_tabla; ?></h4>
			</div>
			<div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6 text-right">
				<button class="btn btn-sm btn-outline-primary ml-auto btn-round" data-bs-toggle="modal" data-bs-target="#Modal_Nuevo_Usuario">
					<i class="icon-plus btn-icon-prepend"></i>Nuevo Usuario
				</button>
			</div>
		</div>
		<table class="table text-dark table-sm" id="tabla_usuarios" width="100%">
			<thead>
				<tr class="text-center">
					<th class="p-1" width="80">Cod</th>
					<th class="p-1" width="150px">Cédula</th>
					<th class="p-1">Nombre</th>
					<th class="p-1" width="150px">Teléfono</th>
					<th class="p-1" width="200px">Rol</th>
					<th class="p-1" width="150px">Estado</th>
					<th class="p-1" width="150px"></th>
				</tr>
			</thead>
			<tbody class="overflow-auto">
				<?php
				while ($mostrar = mysqli_fetch_row($result)) {
					$cod_usuario = $mostrar[0];
					$imagen_usuario = $mostrar[5];
					$cedula = $mostrar[1];
					$nombre = ucwords(strtolower($mostrar[2] . ' ' . $mostrar[3]));
					$telefono = $mostrar[6];
					$rol = $mostrar[7];
					$estado = $mostrar[9];

					if ($estado == 'ACTIVO')
						$estado_button = 'btn-success';
					if ($estado == 'BLOQUEADO')
						$estado_button = 'btn-danger';
				?>
					<tr>
						<td class="text-center"><?php echo str_pad($mostrar[0], 3, "0", STR_PAD_LEFT) ?></td>
						<td class="text-center p-1"><?php echo $cedula ?></td>
						<td class="p-1 text-truncate"><?php echo $nombre ?></td>
						<td class="text-center p-1"><?php echo $telefono ?></strong></td>
						<td class="text-center p-1"><?php echo $rol ?></strong></td>
						<td class="text-center p-1">
							<button class="btn btn-sm <?php echo $estado_button ?> btn-round px-2" id="btn_estado_<?php echo $cod_usuario ?>" onclick="cambiar_estado('<?php echo $cod_usuario ?>')">
								<?php echo $estado ?>
							</button>
						</td>
						<td class="text-center p-1">
							<button class="btn btn-outline-primary btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Ver" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_modal_usuario').load('paginas/detalles/detalles_usuario.php/?cod_usuario=<?php echo $cod_usuario ?>', function(){cerrar_loader();});">
								<span class="fa fa-search"></span>
							</button>
							<button class="btn btn-outline-warning btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Editar" onclick="actualizar_usuario('<?php echo $mostrar[0] ?>')">
								<span class="fa fa-edit"></span>
							</button>
							<button class="btn btn-outline-danger btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Eliminar" onclick="$('#cod_usuario_delete').val(<?php echo $mostrar[0] ?>);">
								<span class="fa fa-trash"></span>
							</button>
							<button class="btn btn-outline-info btn-round p-1" onclick="permisos_usuarios('<?php echo $cod_usuario ?>')">
								<span class="fa fa-key"></span>
							</button>
							<button class="btn btn-outline-secondary btn-round p-1" onclick="reiniciar_pass('<?php echo $cod_usuario ?>')">
								<span class="fa fa-unlock-alt"></span>
							</button>
						</td>
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>
	</div>
</div>

<!-- Modal Nuevo usuario-->
<div class="modal fade" id="Modal_Nuevo_Usuario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h5 class="modal-title">Agregar Nuevo Usuario</h5>
			</div>
			<div class="modal-body">
				<form id="frmnuevo" autocomplete="off">
					<div class="row form-group">
						<div class="form-line col-6">
							<label>Identificación:</label>
							<input type="number" class="form-control form-control-sm" id="identificacion_usuario" name="identificacion_usuario">
						</div>
					</div>
					<div class="row form-group form-group-sm">
						<div class="form-line col">
							<label>Nombre:</label>
							<input type="text" class="form-control form-control-sm" id="nombre_usuario" name="nombre_usuario">
						</div>
						<div class="form-line col">
							<label>Apellido:</label>
							<input type="text" class="form-control form-control-sm" id="apellido_usuario" name="apellido_usuario">
						</div>
					</div>
					<div class="form-group form-group-sm">
						<div class="form-line">
							<label>Telefono:</label>
							<input type="text" class="form-control form-control-sm" id="telefono_usuario" name="telefono_usuario">
						</div>
					</div>

					<div class="form-group form-group-sm">
						<div class="form-line">
							<label>Rol:</label>
							<select class="form-control form-control-sm" id="rol_usuario" name="rol_usuario">
								<option value="">Seleciona el rol</option>
								<option value="Administrador">Administrador</option>
								<option value="Mesero">Mesero</option>
								<option value="Horno">Horno</option>
								<option value="Cocina">Cocina</option>
								<option value="Bar">Bar</option>
								<option value="Usuario">Usuario</option>
							</select>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-sm btn-secondary btn-round" data-bs-dismiss="modal">Cerrar</button>
				<button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btnAgregar">Agregar Usuario</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Editar usuario-->
<div class="modal fade" id="Modal_Editar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h5 class="modal-title">Editar Usuario</h5>
			</div>
			<div class="modal-body">
				<form id="frmnuevo_U" autocomplete="off">
					<input type="text" name="cod_usuario_U" id="cod_usuario_U" hidden="">
					<div class="row form-group form-group-sm mb-0">
						<label>Identificación:</label>
						<input type="text" class="form-control form-control-sm" id="identificacion_usuario_U" name="identificacion_usuario_U">
					</div>
					<div class="row form-group form-group-sm mb-0">
						<div class="form-line">
							<label>Nombre:</label>
							<input type="text" class="form-control form-control-sm" id="nombre_usuario_U" name="nombre_usuario_U">
						</div>
					</div>
					<div class="row form-group form-group-sm mb-0">
						<div class="form-line">
							<label>Apellido:</label>
							<input type="text" class="form-control form-control-sm" id="apellido_usuario_U" name="apellido_usuario_U">
						</div>
					</div>
					<div class="row form-group form-group-sm mb-0">
						<div class="form-line">
							<label>Telefono:</label>
							<input type="text" class="form-control form-control-sm" id="telefono_usuario_U" name="telefono_usuario_U">
						</div>
					</div>

					<div class="row form-group form-group-sm mb-0">
						<div class="form-line">
							<label>Rol:</label>
							<select class="form-control form-control-sm" id="rol_usuario_U" name="rol_usuario_U">
								<option value="">Seleciona el rol</option>
								<option value="Administrador">Administrador</option>
								<option value="Mesero">Mesero</option>
								<option value="Horno">Horno</option>
								<option value="Cocina">Cocina</option>
								<option value="Bar">Bar</option>
								<option value="Usuario">Usuario</option>
							</select>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-sm btn-secondary btn-round" data-bs-dismiss="modal">Cerrar</button>
				<button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btnEditar">Editar Usuario</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Eliminar usuario-->
<div class="modal fade" id="Modal_Eliminar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h5 class="modal-title">Seguro desea eliminar este Usuario?</h5>
			</div>
			<div class="modal-body">
				<input type="number" name="cod_usuario_delete" id="cod_usuario_delete" hidden="">
				<div class="row">
					<button type="button" class="btn btn-sm btn-secondary btn-round col" data-bs-dismiss="modal">NO</button>
					<button type="button" class="btn btn-sm btn-outline-primary btn-round col" id="btnEliminar">SI, Eliminar</button>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- Modal Ver-->
<div class="modal fade" id="Modal_Ver" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="div_modal_usuario">
		</div>
	</div>
</div>

<!-- Modal detalles de factura-->
<div class="modal fade" id="Modal_ticket" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content" id="contenedor_pdf"></div>
	</div>
</div>

<!-- Modal confirmacion pagar-->
<div class="modal fade" id="Modal_confirmacion_pagar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow-y: scroll;">
	<div class="row">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content shadow-lg">
				<div class="modal-header text-center p-2 bg-danger">
					<h5 class="modal-title text-white">Está seguro de pagar la comisión al usuario?</h5>
				</div>
				<div class="modal-body p-2">
					<div class="row m-0">
						<input type="number" name="cod_usuario_confirm" id="cod_usuario_confirm" hidden="">
						<button type="button" class="btn btn-sm btn-outline-secondary btn-round col m-1" onclick="$('#Modal_confirmacion_pagar').modal('toggle');document.getElementById('Modal_Ver').classList.add('show');" id="btn_close_confirm">NO</button>
						<button type="button" class="btn btn-sm btn-outline-primary btn-round col m-1" id="btn_pagar_comision">SI</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- #END# Tabla Usuarios -->
<script type="text/javascript">
	$(document).ready(function() {
		$('#tabla_usuarios').DataTable({
			responsive: true,
			columns: [{
					responsivePriority: 0
				},
				{
					responsivePriority: 3
				},
				{
					responsivePriority: 1
				},
				{
					responsivePriority: 7
				},
				{
					responsivePriority: 4
				},
				{
					responsivePriority: 5
				},
				{
					responsivePriority: 2
				}
			]
		});
	});


	$('#btnAgregar').click(function() {
		document.getElementById('div_loader').style.display = 'block';
		document.getElementById("btnAgregar").disabled = true;
		datos = $('#frmnuevo').serialize();
		$.ajax({
			type: "POST",
			data: datos,
			url: "procesos/agregar.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				if (datos['consulta'] == 1) {
					$('#frmnuevo')[0].reset();
					w_alert({
						titulo: 'Usuario Agregado Correctamente',
						tipo: 'success'
					});
					$('#div_contenido').load('tablas/usuarios.php', function() {
						cerrar_loader();
					});
					$("#Modal_Nuevo_Usuario").modal('toggle');
					document.getElementById("btnAgregar").disabled = false;
				} else {
					w_alert({
						titulo: datos['consulta'],
						tipo: 'danger'
					});
					if (datos['consulta'] == 'Reload') {
						document.getElementById('div_login').style.display = 'block';
						cerrar_loader();
						
					}
					document.getElementById("btnAgregar").disabled = false;
					$('#div_contenido').load('tablas/usuarios.php', function() {
						cerrar_loader();
					});
				}
			}
		});
	});

	$('#btnEditar').click(function() {
		document.getElementById('div_loader').style.display = 'block';
		document.getElementById("btnEditar").disabled = true;
		datos = $('#frmnuevo_U').serialize();
		$.ajax({
			type: "POST",
			data: datos,
			url: "procesos/actualizar.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				if (datos['consulta'] == 1) {
					$('#frmnuevo_U')[0].reset();
					w_alert({
						titulo: 'Usuario Actualizado Correctamente',
						tipo: 'success'
					});
					$('#div_contenido').load('tablas/usuarios.php', function() {
						cerrar_loader();
					});
					$("#Modal_Editar").modal('toggle');
					cerrar_loader();
					document.getElementById("btnEditar").disabled = false;
				} else {
					w_alert({
						titulo: datos['consulta'],
						tipo: 'danger'
					});
					if (datos['consulta'] == 'Reload') {
						document.getElementById('div_login').style.display = 'block';
						cerrar_loader();
						
					}
					document.getElementById("btnEditar").disabled = false;
					cerrar_loader();
				}
			}
		});

	});

	$('#btnEliminar').click(function() {
		document.getElementById('div_loader').style.display = 'block';
		cod_usuario = document.getElementById("cod_usuario_delete").value;
		$.ajax({
			type: "POST",
			data: "cod_usuario=" + cod_usuario,
			url: "procesos/eliminar.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				if (datos['consulta'] == 1) {
					w_alert({
						titulo: 'Usuario Eliminado Correctamente',
						tipo: 'success'
					});
					$('#div_contenido').load('tablas/usuarios.php', function() {
						cerrar_loader();
					});
					$("#Modal_Eliminar").modal('toggle');
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
	});

	function reiniciar_pass(cod_usuario) {
		document.getElementById('div_loader').style.display = 'block';
		$.ajax({
			type: "POST",
			data: "cod_usuario=" + cod_usuario,
			url: "procesos/reiniciar_pass.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				if (datos['consulta'] == 1) {
					w_alert({
						titulo: 'Contraseña reiniciada Correctamente',
						tipo: 'success'
					});
					$('#div_contenido').load('tablas/usuarios.php', function() {
						cerrar_loader();
					});
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

	$('#btn_pagar_comision').click(function() {
		document.getElementById('div_loader').style.display = 'block';
		cod_usuario = document.getElementById("cod_usuario_confirm").value;
		$.ajax({
			type: "POST",
			data: "cod_usuario=" + cod_usuario,
			url: "procesos/pagar_comisiones.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				if (datos['consulta'] == 1) {
					w_alert({
						titulo: 'Pago realizado Correctamente',
						tipo: 'success'
					});
					$("#btn_close_confirm").click();
					document.getElementById('div_loader').style.display = 'block';
					$('#div_modal_usuario').load('paginas/detalles/detalles_usuario.php/?cod_usuario=' + cod_usuario, function() {
						cerrar_loader();
					});
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
	});

	function actualizar_usuario(cod_usuario) {
		document.getElementById("btnEditar").disabled = false;
		$.ajax({
			type: "POST",
			data: "cod_usuario=" + cod_usuario,
			url: "procesos/obtener_datos.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				$('#cod_usuario_U').val(datos['cod_usuario']);
				$('#nombre_usuario_U').val(datos['nombre']);
				$('#identificacion_usuario_U').val(datos['cedula']);
				$('#apellido_usuario_U').val(datos['apellido']);
				$('#telefono_usuario_U').val(datos['telefono']);
				$('#rol_usuario_U').val(datos['rol']).trigger('change');
			}
		});
	}

	function cambiar_estado(cod_usuario) {
		$.ajax({
			type: "POST",
			data: "cod_usuario=" + cod_usuario,
			url: "procesos/cambiar_estado.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				if (datos['consulta'] == 1) {
					document.getElementById('btn_estado_' + cod_usuario).innerHTML = datos['estado'];
					document.getElementById('btn_estado_' + cod_usuario).classList.remove("btn-success");
					document.getElementById('btn_estado_' + cod_usuario).classList.remove("btn-danger");
					if (datos['estado'] == 'ACTIVO')
						document.getElementById('btn_estado_' + cod_usuario).classList.add("btn-success");
					else
						document.getElementById('btn_estado_' + cod_usuario).classList.add("btn-danger");
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
	}

	function permisos_usuarios(cod_usuario) {
		document.getElementById('div_loader').style.display = 'block';
		$('#div_contenido').load('paginas/permisos_usuarios.php/?cod_usuario=' + cod_usuario, function() {
			cerrar_loader();
		});
		document.title = 'Permisos | Restaurante | Kuiik';
		$('.active').removeClass("active")
	}
</script>