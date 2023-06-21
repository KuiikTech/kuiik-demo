<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
session_set_cookie_params(7*24*60*60);
session_start();

$cod_caja=$_GET['cod_caja'];

$egresos = array();

$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `ingresos`, `egresos`, `base`, `creador`, `cajero`, `finalizador`, `estado`, `base_sig` FROM `caja` WHERE codigo = '$cod_caja'";
$result=mysqli_query($conexion,$sql);
$mostrar=mysqli_fetch_row($result);

$estado = $mostrar[10];
if($estado == 'ABIERTA' || $estado == 'CERRADA')
{
	if ($mostrar[5] != '')
		$egresos = json_decode($mostrar[5],true);
}

?>
<div class="table-responsive text-dark text-center py-0 px-1">
	<table width="100%" class="table text-dark table-sm" id="tabla_egresos">
		<thead>
			<tr class="text-center">
				<th class="p-1">#</th>
				<th class="p-1">Concepto</th>
				<th width="120px" class="p-1">Valor</th>
				<th width="100px" class="p-1">Fecha</th>
				<th class="p-1">Creador</th>
			</tr>
		</thead>
		<tbody class="overflow-auto text-dark">
			<?php 
			$total_egresos = 0;
			$num_item = 1;
			foreach ($egresos as $i => $egreso)
			{
				$fecha_egreso = strftime("%A, %e %b %Y", strtotime($egreso['fecha']));
				$fecha_egreso = ucfirst(iconv("ISO-8859-1","UTF-8",$fecha_egreso));

				$fecha_egreso .= date(' | h:i A',strtotime($egreso['fecha']));

				$creador = $egreso['creador'];

				$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
				$result_e=mysqli_query($conexion,$sql_e);
				$ver_e=mysqli_fetch_row($result_e);
				$nombre_aux = explode(' ', $ver_e[0]);
				$apellido_aux = explode(' ', $ver_e[1]);
				$creador = $nombre_aux[0].' '.$apellido_aux[0];
				?>
				<tr>
					<td class="p-1 text-center"><?php echo $num_item ?></td>
					<td class="p-1 text-left text-truncate"><?php echo $egreso['concepto'] ?></td>
					<td class="p-1 text-right"><strong>$<?php echo number_format($egreso['valor'],0,'.','.')?></strong></td>
					<td class="p-1 text-center"><?php echo $fecha_egreso ?></td>
					<td class="p-1 text-center"><?php echo $creador ?></td>
				</tr>
				<?php 
				$total_egresos += $egreso['valor'];
				$num_item ++;
			} 
			?>
		</tbody>
	</table>
	<div class="row text-right mt-3">
		<h3>Total Egresos: $<?php echo number_format($total_egresos,0,'.','.') ?></h3>
	</div>
</div>

<?php 
if($estado == 'ABIERTA')
{
	?> 
	<h5 class="text-center">Agregar egreso</h5>
	<table width="100%" class="table text-dark table-sm w-100">
		<tr>
			<td class="p-1">
				<input type="text" class="form-control form-control-sm" name="input_concepto_egreso" id="input_concepto_egreso" autocomplete="off" placeholder="Concepto del egreso">
			</td>
			<td width="150px" class="p-1">
				<input type="text" class="form-control form-control-sm moneda text-right" name="input_valor_egreso" id="input_valor_egreso" autocomplete="off" placeholder="Valor del egreso">
			</td>
			<td width="100px" class="text-center p-1">
				<button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_agregar_egreso">Agregar</button>
			</td>
		</tr>
	</table>
	<?php 
}
?>


<script type="text/javascript">

	$('input.moneda').keyup(function(event)
	{
		if(event.which >= 37 && event.which <= 40)
		{
			event.preventDefault();
		}
		$(this).val(function(index, value)
		{
			return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
		});
	});
	
	$('#btn_agregar_egreso').click(function()
	{
		document.getElementById('div_loader').style.display = 'block';
		document.getElementById("btn_agregar_egreso").disabled = true;
		concepto_egreso = document.getElementById("input_concepto_egreso").value;
		valor_egreso = document.getElementById("input_valor_egreso").value;
		if(valor_egreso != '' && concepto_egreso != '')
		{
			$.ajax({
				type:"POST",
				data:"cod_caja=<?php echo $cod_caja ?>"+"&valor_egreso="+valor_egreso+"&concepto_egreso="+concepto_egreso,
				url:"procesos/agregar_egreso.php",
				success:function(r)
				{
					datos=jQuery.parseJSON(r);
					if (datos['consulta'] == 1)
					{
						document.getElementById('div_loader').style.display = 'block';
						$('#div_tabla_egresos').load('tablas/egresos.php/?cod_caja=<?php echo $cod_caja ?>', function(){cerrar_loader();});
						document.getElementById('div_loader').style.display = 'block';
						$('#div_labels_caja').load('detalles/labels_caja.php/?cod_caja=<?php echo $cod_caja ?>', function(){cerrar_loader();});
						w_alert({ titulo: 'Egreso agregado correctamente', tipo: 'success' });
					}
					else
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
				}
			});
		}
		else
		{
			if(input_concepto_egreso == '')
			{
				w_alert({ titulo: 'Escriba el concepto del egreso', tipo: 'danger' });
				document.getElementById('input_concepto_egreso').focus();
			}
			else
			{
				w_alert({ titulo: 'Ingrese el valor del egreso', tipo: 'danger' });
				document.getElementById('input_valor_egreso').focus();
			}
		}
		cerrar_loader();
		document.getElementById("btn_agregar_egreso").disabled = false;
	});
</script>