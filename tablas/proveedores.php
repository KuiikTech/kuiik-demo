<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();

$sql = "SELECT `codigo`, `nombre`, `telefono`, `ciudad`, `fecha_registro` FROM `proveedores` WHERE estado != 'ELIMINADO' ORDER BY `nombre` ASC";
$result=mysqli_query($conexion,$sql);

$nombre_tabla = 'Proveedores';
?>
<!-- Tabla Proveedores -->
<div class="card">
	<div class="card-body p-2">
		<div class="d-sm-flex align-items-center row m-0 mb-2">
			<div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6">
				<h4 class="card-title"><?php echo $nombre_tabla; ?></h4>
			</div>
			<div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6 text-right">
				<button class="btn btn-sm btn-outline-primary ml-auto btn-round" data-bs-toggle="modal" data-bs-target="#Modal_Nuevo_Proveedor">
					<i class="icon-plus btn-icon-prepend"></i>Nuevo Proveedor
				</button>
			</div>
		</div>
		<table class="table text-dark table-sm" id="tabla_proveedores" width="100%">
			<thead>
				<tr class="text-center">
					<th class="p-1" width="40px">#</th>
					<th class="p-1" width="80px">Cod</th>
					<th class="p-1">Nombre</th>
					<th class="p-1" width="150px">Teléfono</th>
					<th class="p-1" width="150px">Ciudad</th>
					<th class="p-1" width="120px"></th>
				</tr>
			</thead>
			<tbody class="overflow-auto">
				<?php 
				$num_item = 1;
				while ($mostrar=mysqli_fetch_row($result)) 
				{
					$cod_proveedor = $mostrar[0];
					$nombre = ucwords(strtolower($mostrar[1]));
					$telefono = $mostrar[2];
					$ciudad = $mostrar[3];
					?>
					<tr>
						<td class="text-center p-1"><?php echo $num_item ?></td>
						<td class="text-center"><?php echo str_pad($cod_proveedor,3,"0",STR_PAD_LEFT) ?></td>
						<td class="p-1 text-truncate"><?php echo $nombre ?></td>
						<td class="text-center p-1"><?php echo $telefono ?></strong></td>
						<td class="text-center p-1"><?php echo $ciudad ?></strong></td>
						<td class="text-center p-1">
							<button class="btn btn-outline-primary btn-round p-1" hidden data-bs-toggle="modal" data-bs-target="#Modal_Ver" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_modal_proveedor').load('paginas/detalles/detalles_proveedor.php/?cod_proveedor=<?php echo $cod_proveedor ?>', function(){cerrar_loader();});">
								<span class="fa fa-search"></span>
							</button>
							<button class="btn btn-outline-warning btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Editar" onclick="actualizar_proveedor('<?php echo $mostrar[0] ?>')">
								<span class="fa fa-edit"></span>
							</button>
							<button class="btn btn-outline-danger btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Eliminar" onclick="$('#cod_proveedor_delete').val(<?php echo $mostrar[0] ?>);">
								<span class="fa fa-trash"></span>
							</button>
						</td>
					</tr>
					<?php 
					$num_item ++;
				} 
				?>
			</tbody>
		</table>
	</div>
</div>

<!-- Modal Nuevo proveedor-->
<div class="modal fade" id="Modal_Nuevo_Proveedor" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h5 class="modal-title">Agregar Nuevo Proveedor</h5>
			</div>
			<div class="modal-body">
				<form id="frmnuevo" autocomplete="off">
					<div class="row form-group form-group-sm">
						<div class="form-line">
							<label>Nombre:</label>
							<input type="text" class="form-control form-control-sm" id="nombre_proveedor" name="nombre_proveedor">
						</div>
					</div>
					<div class="form-group form-group-sm">
						<div class="form-line">
							<label>Telefono:</label>
							<input type="text" class="form-control form-control-sm" id="telefono_proveedor" name="telefono_proveedor">
						</div>
					</div>
					<div class="form-group form-group-sm">
						<div class="form-line">
							<label>Ciudad:</label>
							<input type="text" class="form-control form-control-sm" id="ciudad_proveedor" name="ciudad_proveedor">
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-sm btn-secondary btn-round" data-bs-dismiss="modal">Cerrar</button>
				<button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btnAgregar">Agregar Proveedor</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Editar proveedor-->
<div class="modal fade" id="Modal_Editar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h5 class="modal-title">Editar Proveedor</h5>
			</div>
			<div class="modal-body">
				<form id="frmnuevo_U" autocomplete="off">
					<input type="text" name="cod_proveedor_U" id="cod_proveedor_U" hidden="">
					<div class="row form-group form-group-sm mb-0">
						<div class="form-line">
							<label>Nombre:</label>
							<input type="text" class="form-control form-control-sm" id="nombre_proveedor_U" name="nombre_proveedor_U">
						</div>
					</div>
					
					<div class="row form-group form-group-sm mb-0">
						<div class="form-line">
							<label>Telefono:</label>
							<input type="text" class="form-control form-control-sm" id="telefono_proveedor_U" name="telefono_proveedor_U">
						</div>
					</div>

					<div class="row form-group form-group-sm mb-0">
						<div class="form-line">
							<label>Ciudad:</label>
							<input type="text" class="form-control form-control-sm" id="ciudad_proveedor_U" name="ciudad_proveedor_U">
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-sm btn-secondary btn-round" data-bs-dismiss="modal">Cerrar</button>
				<button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btnEditar">Editar Proveedor</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Eliminar proveedor-->
<div class="modal fade" id="Modal_Eliminar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h5 class="modal-title">Seguro desea eliminar este Proveedor?</h5>
			</div>
			<div class="modal-body">
				<input type="number" name="cod_proveedor_delete" id="cod_proveedor_delete" hidden="">
				<div class="row m-0 p-2">
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
		<div class="modal-content" id="div_modal_proveedor">
		</div>
	</div>
</div>

<!-- Modal detalles de factura-->
<div class="modal fade" id="Modal_ticket" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content" id="contenedor_pdf"></div>
	</div>
</div>

<!-- #END# Tabla Proveedores -->
<script type="text/javascript">
	$(document).ready(function()
	{
		$('#tabla_proveedores').DataTable(
		{
			responsive: true,
			columns: [
			{ responsivePriority: 0 },
			{ responsivePriority: 3 },
			{ responsivePriority: 1 },
			{ responsivePriority: 4 },
			{ responsivePriority: 5 },
			{ responsivePriority: 2 }
			]
		});
	});


	$('#btnAgregar').click(function()
	{
		document.getElementById('div_loader').style.display = 'block';
		document.getElementById("btnAgregar").disabled = true;
		datos=$('#frmnuevo').serialize();
		$.ajax({
			type:"POST",
			data:datos,
			url:"procesos/agregar_proveedor.php",
			success:function(r)
			{
				datos=jQuery.parseJSON(r);
				if(datos['consulta'] == 1)
				{
					$('#frmnuevo')[0].reset();
					w_alert({ titulo: 'Proveedor Agregado Correctamente', tipo: 'success' });
					$('#div_contenido').load('tablas/proveedores.php', function(){cerrar_loader();});
					$("#Modal_Nuevo_Proveedor").modal('toggle');
					document.getElementById("btnAgregar").disabled = false;
				}
				else
				{
					w_alert({ titulo: datos['consulta'], tipo: 'danger' });
					if(datos['consulta'] == 'Reload')
					{
						document.getElementById('div_login').style.display = 'block';
						cerrar_loader();
						
					}
					document.getElementById("btnAgregar").disabled = false;
					$('#div_contenido').load('tablas/proveedores.php', function(){cerrar_loader();});
				}
			}
		});
	});

	$('#btnEditar').click(function()
	{
		document.getElementById('div_loader').style.display = 'block';
		document.getElementById("btnEditar").disabled = true;
		datos=$('#frmnuevo_U').serialize();
		$.ajax({
			type:"POST",
			data:datos,
			url:"procesos/actualizar.php",
			success:function(r)
			{
				datos=jQuery.parseJSON(r);
				if(datos['consulta'] == 1)
				{
					$('#frmnuevo_U')[0].reset();
					w_alert({ titulo: 'Proveedor Actualizado Correctamente', tipo: 'success' });
					$('#div_contenido').load('tablas/proveedores.php', function(){cerrar_loader();});
					$("#Modal_Editar").modal('toggle');
					cerrar_loader();
					document.getElementById("btnEditar").disabled = false;
				}
				else
				{
					w_alert({ titulo: datos['consulta'], tipo: 'danger' });
					if(datos['consulta'] == 'Reload')
					{
						document.getElementById('div_login').style.display = 'block';
						cerrar_loader();
						
					}
					document.getElementById("btnEditar").disabled = false;
					cerrar_loader();
				}
			}
		});

	});

	$('#btnEliminar').click(function()
	{
		document.getElementById('div_loader').style.display = 'block';
		cod_proveedor = document.getElementById("cod_proveedor_delete").value;
		$.ajax({
			type:"POST",
			data:"cod_proveedor=" + cod_proveedor,
			url:"procesos/eliminar_proveedor.php",
			success:function(r)
			{
				datos=jQuery.parseJSON(r);
				if(datos['consulta'] == 1)
				{
					w_alert({ titulo: 'Proveedor Eliminado Correctamente', tipo: 'success' });
					$('#div_contenido').load('tablas/proveedores.php', function(){cerrar_loader();});
					$("#Modal_Eliminar").modal('toggle');
				}
				else
				{
					w_alert({ titulo: datos['consulta'], tipo: 'danger' });
					if(datos['consulta'] == 'Reload')
					{
						document.getElementById('div_login').style.display = 'block';
						cerrar_loader();
						
					}
					cerrar_loader();
				}
			}
		});
	});

	function actualizar_proveedor(cod_proveedor)
	{
		document.getElementById("btnEditar").disabled = false;
		$.ajax({
			type:"POST",
			data:"cod_proveedor=" + cod_proveedor,
			url:"procesos/obtener_datos.php",
			success:function(r){
				datos=jQuery.parseJSON(r);
				$('#cod_proveedor_U').val(datos['codigo']);
				$('#nombre_proveedor_U').val(datos['nombre']);
				$('#ciudad_proveedor_U').val(datos['ciudad']);
				$('#telefono_proveedor_U').val(datos['telefono']);
			}
		});
	}

</script>