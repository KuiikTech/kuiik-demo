<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$cod_mesa = $_GET['cod_mesa'];
$num_item = $_GET['num_item'];

$sql_mesa = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura` FROM `mesas` WHERE cod_mesa = '$cod_mesa'";
$result_mesa = mysqli_query($conexion, $sql_mesa);
$mostrar_mesa = mysqli_fetch_row($result_mesa);

if ($mostrar_mesa[2] != '')
	$productos_mesa = json_decode($mostrar_mesa[2], true);

?>
<div class="row px-3">
	<table class="table table-sm table-striped">
		<thead>
			<tr>
				<th class="text-center">Descripción</th>
				<th class="text-center" width="75px"></th>
			</tr>
		</thead>
		<tbody>
			<?php
			if ($mostrar_mesa[2] != '') {
				$notas = array();
				if (isset($productos_mesa[$num_item]['notas'])) {
					if ($productos_mesa[$num_item]['notas'] != '')
						$notas = $productos_mesa[$num_item]['notas'];
				}
				if ($notas != '') {
					foreach ($notas as $i => $nota) {
			?>
						<tr>
							<td><?php echo $nota ?></td>
							<td class="text-center">
								<a href="#" onclick="eliminar_nota_cocina('<?php echo $num_item ?>','<?php echo $cod_mesa ?>','<?php echo $i ?>')">
									<span class="fa fa-times f-16 text-danger"></span>
								</a>
							</td>
						</tr>
			<?php
					}
				}
			}
			?>
			<tr>
				<td>
					<input type="text" class="form-control" name="input_nota" id="input_nota">
				</td>
				<td class="text-center">
					<button class="btn btn-outline-primary btn-round p-1" id="btn_agregar_nota" onclick="agregar_nota_cocina('<?php echo $num_item ?>','<?php echo $cod_mesa ?>')">
						<span class="fa fa-check"></span>
					</button>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-outline-secondary btn-round p-1" data-bs-dismiss="modal">Cerrar</button>
</div>

<script type="text/javascript">
	$('#input_nota').keypress(function(e) {
		if (e.keyCode == 13)
			$('#btn_agregar_nota').click();
	});

	setTimeout(function() {
		document.getElementById("input_nota").focus();
	}, 500);

	function agregar_nota_cocina(num_item, cod_mesa) {
		document.getElementById('div_loader').style.display = 'block';
		input_nota = document.getElementById("input_nota").value;
		if (input_nota != '') {
			$.ajax({
				type: "POST",
				data: "num_item=" + num_item + "&cod_mesa=" + cod_mesa + "&input_nota=" + input_nota,
				url: "procesos/agregar_nota_cocina.php",
				success: function(r) {
					datos = jQuery.parseJSON(r);
					if (datos['consulta'] == 1) {
						w_alert({
							titulo: 'Nota agregada correctamente',
							tipo: 'success'
						});
						$('#div_add_nota').load('paginas/detalles/notas.php/?cod_mesa=' + cod_mesa + '&num_item=' + num_item, cerrar_loader());
						document.getElementById("input_nota").focus();
					} else
						w_alert({
							titulo: datos['consulta'],
							tipo: 'danger'
						});
				}
			});
		} else
			w_alert({
				titulo: 'Ingrese la nota',
				tipo: 'danger'
			});
	}

	function eliminar_nota_cocina(num_item, cod_mesa, num_nota) {
		$.ajax({
			type: "POST",
			data: "num_item=" + num_item + "&cod_mesa=" + cod_mesa + "&num_nota=" + num_nota,
			url: "procesos/eliminar_nota_cocina.php",
			success: function(r) {
				datos = jQuery.parseJSON(r);
				if (datos['consulta'] == 1) {
					w_alert({
						titulo: 'Nota Eliminada correctamente',
						tipo: 'success'
					});
					document.getElementById('div_loader').style.display = 'block';
					$('#div_add_nota').load('paginas/detalles/notas.php/?cod_mesa=' + cod_mesa + '&num_item=' + num_item, cerrar_loader());
				} else
					w_alert({
						titulo: datos['consulta'],
						tipo: 'danger'
					});
			}
		});
	}
</script>