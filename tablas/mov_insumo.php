<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();
session_set_cookie_params(7*24*60*60);
session_start();

$cod_insumo = $_GET['cod_insumo'];
$pos = $_GET['pos'];

$sql = "SELECT `codigo`, `descripcion`, `categoria`, `inventario`, `estado`, `barcode`, `fecha_registro` FROM `insumos` WHERE codigo = '$cod_insumo'";
$result=mysqli_query($conexion,$sql);
$mostrar=mysqli_fetch_row($result);

$inventario = array();
if ($mostrar[6] != '')
	$inventario = json_decode($mostrar[6],true);


$movimientos = array();
if(isset($inventario[$pos]))
{
	if ($inventario[$pos]['movimientos'] != '')
		$movimientos = $inventario[$pos]['movimientos'];
}

?>
<div class="text-center border-top pt-4">
	<h4>Movimientos </h4>
</div>
<div class="table-responsive text-dark text-center py-0 px-1">
	<table width="100%" class="table text-dark table-sm" id="tabla_pagos_trabajo">
		<thead>
			<tr class="text-center">
				<th class="py-1" width="60px" class="table-plus text-dark datatable-nosort px-1">#</th>
				<th class="py-1" class="px-1">Tipo Movimiento</th>
				<th class="py-1">Cantidad</th>
				<th class="py-1">Observaciones</th>
				<th class="py-1">Creador</th>
				<th class="py-1">Fecha</th>
			</tr>
		</thead>
		<tbody class="overflow-auto">
			<?php 
			$total = 0;
			foreach ($movimientos as $i => $movimiento)
			{
				$tipo = $movimiento['Tipo'];
				$cant = $movimiento['Cant'];
				$creador = $movimiento['creador'];
				$observaciones = $movimiento['Observaciones'];
				$fecha = $movimiento['fecha'];

				if($creador != 'Sistema')
				{
					$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
					$result_e=mysqli_query($conexion,$sql_e);
					$ver_e=mysqli_fetch_row($result_e);
					$nombre_aux = explode(' ', $ver_e[0]);
					$apellido_aux = explode(' ', $ver_e[1]);
					$creador = $nombre_aux[0].' '.$apellido_aux[0];
				}

				$text_cant = 'text-danger';

				if($cant >0)
					$text_cant = 'text-success';

				?>
				<tr role="row" class="odd">
					<td class="text-center p-1 text-muted"><?php echo $i ?></td>
					<td class="text-left p-1"><?php echo $tipo ?></td>
					<td class="text-center p-1 <?php echo $text_cant ?>"><?php echo $cant ?></td>
					<td class="text-left p-1"><?php echo $observaciones ?></td>
					<td class="text-left p-1"><b><?php echo $creador ?></b></td>
					<td class="text-left p-1"><?php echo $fecha ?></td>
				</tr>
				<?php 
			} 
			?>
		</tbody>
	</table>
</div>

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

	$('#input_valor_pago').keypress(function(e){
		if(e.keyCode==13)
			$('#btn_agregar_pago_trabajo').click();
	});

	$('#btn_agregar_pago_trabajo').click(function()
	{
		document.getElementById('div_loader').style.display = 'block';
		document.getElementById("btn_agregar_pago_trabajo").disabled = true;
		datos = $('#form_metodo').serialize();
		input_valor_pago = document.getElementById("input_valor_pago").value;
		if(input_valor_pago != '')
		{
			$.ajax({	
				type:"POST",
				data:datos+"&input_valor_pago=" + input_valor_pago,
				url:"procesos/agregar_pago_trabajo.php",
				success:function(r)
				{
					datos=jQuery.parseJSON(r);
					if(datos['consulta'] == 1)
					{
						w_alert({ titulo: 'Item agregado con exito', tipo: 'success' });
						$('#tabla_pagos').load('tablas/pagos_trabajo.php', function(){cerrar_loader();});
						setTimeout("$('#input_metodo').focus()",300);
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
			w_alert({ titulo: 'Ingrese el valor del pago', tipo: 'danger' });
			document.getElementById("input_valor_pago").focus();
		}

		cerrar_loader();
		document.getElementById("btn_agregar_pago_trabajo").disabled = false;
	});

	function eliminar_item_pago(num_item)
	{
		document.getElementById('div_loader').style.display = 'block';
		$.ajax({
			type:"POST",
			data:"num_item="+num_item,
			url:"procesos/eliminar_pago_trabajo.php",
			success:function(r)
			{
				datos=jQuery.parseJSON(r);
				if (datos['consulta'] == 1)
				{
					w_alert({ titulo: 'Item eliminado', tipo: 'success' });
					document.getElementById('div_loader').style.display = 'block';
					$('#tabla_pagos').load('tablas/pagos_trabajo.php', function(){cerrar_loader();});
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

				cerrar_loader();
			}
		});
	}
</script>