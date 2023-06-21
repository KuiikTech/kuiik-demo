<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
session_set_cookie_params(7*24*60*60);
session_start();

if (isset($_SESSION['items_salida']))
	$items_salida = $_SESSION['items_salida'];
else
	$items_salida = array();
?>
<hr class="my-1">
<div class="text-center">
	<h4>Items para salida</h4>
</div>
<div class="row mx-2">
	<?php 
	foreach ($items_salida as $i => $item)
	{
		$codigo = $item['codigo'];
		$descripcion = $item['descripcion'];
		$categoria = $item['categoria'];
		$cant = $item['cant'];
		$imagen = $item['imagen'];
		?>
		<div class="col-6 col-sm-6 col-md-4 col-lg-3 mb-2 px-1">
			<div class="rounded p-2 shadow border">
				<div class="end-0 top-0">
					<div class="form-check prod-likes">
						<a class="btn btn-danger btn-round p-1" onclick="eliminar_item(<?php echo $i ?>);">
							<i class="material-icons-two-tone text-white">delete</i>
						</a>
					</div>
				</div>
				<div class="pb-0">
					<img src="recursos/product/<?php echo $imagen ?>" alt="prod img" class="img-fluid rounded">
				</div>
				<div class="prod-content">
					<small class="text-uppercase font-weight-bold text-muted"><?php echo $categoria ?></small>
					<div class="text-truncate w-100 h5"><?php echo $descripcion ?></div>
					<div>
						<span class="text-muted f-w-500">Cant: </span><p class="d-inline-block mb-0 h4"><?php echo $cant ?></p> 
					</div>
				</div>
			</div>
		</div>
		<?php 
	} 
	?>
</div>
<hr>
<div class="text-center row">
	<div class="col-sm-6 col-md-6 col-lg-6 col-12 text-center">
		<input type="text" class="form-control form-control-sm" name="manual_barcode" id="manual_barcode" placeholder="Ingrese aquí el código" onFocus="this.select()">
	</div>
	<div class="col-sm-6 col-md-6 col-lg-6 col-12">
		<button type="button" class="btn btn-sm btn-outline-secondary btn-round" data-bs-toggle="modal" data-bs-target="#modal_camara" onclick="abrir_camara('cargar_codigo')" id="btn_abrir_camara">
			<i class="material-icons-two-tone">qr_code_scanner</i> Scannear Código
		</button>
	</div>
</div>

<script type="text/javascript">


	$("#manual_barcode").on('keyup', function (e) {
		var keycode = e.keyCode || e.which;
		if (keycode == 13)
		{
			if(this.value != '')
			{
				if(this.value.length == 13)
					cargar_codigo(this.value);
				else
					w_alert({ titulo: 'El codigo ingresado debe contener 13 dígitos', tipo: 'danger' });
			}
			else
				w_alert({ titulo: 'Ingrese un código', tipo: 'danger' });
			this.focus();
		}
	});


	function eliminar_item(num_item)
	{
		document.getElementById('div_loader').style.display = 'block';
		$.ajax({
			type:"POST",
			data:"num_item="+num_item,
			url:"procesos/eliminar_item_salida.php",
			success:function(r)
			{
				datos=jQuery.parseJSON(r);
				if (datos['consulta'] == 1)
				{
					w_alert({ titulo: 'Item eliminado', tipo: 'success' });
					document.getElementById('div_loader').style.display = 'block';
					$('#tabla_items').load('tablas/items_salida.php', function(){cerrar_loader();});
				}
				else
				{
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
					cerrar_loader();
				}
			}
		});
	}
</script>