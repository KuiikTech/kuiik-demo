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

if (isset($_SESSION['responsable_salida']))
	$responsable_salida = $_SESSION['responsable_salida']['nombre'];
else
	$responsable_salida = 'No ha elegido ningún responsable';
?>
<div class="text-center">
	<h4>Items para salida</h4>
</div>
<div class="table-responsive text-dark text-center py-0 px-1">
	<table width="100%" class="table text-dark table-sm" id="tabla_insumos">
		<thead>
			<tr class="text-center">
				<th class="table-plus text-dark datatable-nosort px-1">Cod</th>
				<th class="px-1">Imagen</th>
				<th width="120px">Cant</th>
				<th>Descripción</th>
			</tr>
		</thead>
		<tbody class="overflow-auto">
			<?php 
			foreach ($items_salida as $i => $item)
			{
				$codigo = $item['codigo'];
				$descripcion = $item['descripcion'];
				$categoria = $item['categoria'];
				$cant = $item['cant'];
				$imagen = $item['imagen'];
				?>
				<tr role="row" class="odd">
					<td class="text-center p-0"><?php echo str_pad($codigo,3,"0",STR_PAD_LEFT) ?></td>
					<td class="align-middle p-0">
						<img src="recursos/product/<?php echo $imagen ?>" alt="contact-img" title="contact-img" class="rounded" height="48">
					</td>
					<td class="text-center p-0"><h4><?php echo $cant ?></h4></td>
					<td class="text-left p-0">
						<b><?php echo $descripcion ?></b>
						<br>
						<span class="badge bg-danger"><?php echo $categoria ?></span>
					</td>
				</tr>
				<?php 
			} 
			?>
		</tbody>
	</table>
</div>
<?php 
if(isset($_SESSION['responsable_salida']))
{
	?>
	<div class="text-center mb-2">
		<span class="badge bg-danger text-uppercase p-2">Confirmación por contraseña</span>
		<h5 class="text-truncate"><?php echo $responsable_salida ?></h5>
		<div class="rounded p-2 progress-bar-striped bg-warning" id="div_contraseña">
			<input type="password" id="input_contraseña" name="input_contraseña" class="form-control form-control-sm" placeholder="Contraseña" autocomplete="new-password">
			<button class="btn btn-secondary btn-sm btn-round mt-2" type="button" id="btn_aceptar">Aceptar</button>
		</div>
		<div class="rounded p-2 progress-bar-striped bg-warning" id="div_pass" hidden="">
			<h1 class="text-dark m-0"><b>* * * *</b></h1>
		</div>
	</div>
	<?php 
}
else
{
	?>
	<div class="text-center mb-2">
		<span class="badge bg-danger text-uppercase p-2">Confirmación por contraseña</span>
		<h5 class="text-truncate"><?php echo $responsable_salida ?></h5>
	</div>
	<?php 
}
?>
<hr>

<script type="text/javascript">
	$('#btn_aceptar').click(function()
	{
		pass = document.getElementById("input_contraseña").value;
		if(pass != '')
		{
			document.getElementById("div_contraseña").hidden = true;
			document.getElementById("div_pass").hidden = false;
			document.getElementById("btn_procesar_salida").disabled = false;
		}
		else
		{
			w_alert({ titulo: 'Ingrese su contraseña', tipo: 'danger' });
			document.getElementById("input_contraseña").focus();
		}
	});
</script>