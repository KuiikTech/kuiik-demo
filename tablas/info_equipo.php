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

$cod_espacio = $_GET['cod_espacio'];

$sql_espacio = "SELECT `codigo`, `nombre`, `items`, `fecha_creacion`, `cod_cliente`, `pagos`, `informacion`, `caja` FROM `espacios` WHERE codigo = '$cod_espacio'";
$result_espacio=mysqli_query($conexion,$sql_espacio);
$mostrar_espacio=mysqli_fetch_row($result_espacio);

$informacion = array();
if($mostrar_espacio[6] != '')
	$informacion = json_decode($mostrar_espacio[6],true);

$info_equipo = array();
if(isset($informacion['equipo']))
{	
	if(isset($informacion['lista_info']))
	{
		$info_equipo = $informacion['lista_info'];
		?>
		<div class="row m-0 text-center p-0 px-1 pb-3">
			<?php 
			$num_item = 1;
			$total = 0;
			foreach ($info_equipo as $i => $item)
			{
				$nombre = $item['nombre'];
				$tipo = $item['tipo'];
				$valor = $item['valor'];

				$type_input = '';
				if($tipo == 'Texto')
					$type_input = 'text';
				if($tipo == 'Número')
					$type_input = 'number';
				if($tipo == 'Fecha')
					$type_input = 'date';
				if($tipo == 'Fecha/Hora')
					$type_input = 'datetime';
				if($tipo == 'Hora')
					$type_input = 'time';
				?>
				<div class="row m-0 p-0 px-1">
					<div class="col-3 text-left p-0 px-1" colspan="2"> - <b><?php echo $nombre ?></b>:</div>
					<div class="col-5 text-center p-0">
						<input type="<?php echo $type_input ?>" class="form-control form-control-sm" id="input_info_<?php echo $i ?>" name="input_info_<?php echo $i ?>" value="<?php echo $valor ?>" onchange="guardar_info_equipo('<?php echo $cod_espacio ?>','<?php echo $i ?>',this.value)" autocomplete="off">
					</div>
				</div>
				<?php 
				$num_item ++;
			} 
			?>
		</div>
		<?php 
	}
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

	$('#input_observacion').keypress(function(e){
		if(e.keyCode==13)
			$('#btn_agregar_info_servicio').click();
	});

	function guardar_info_equipo(cod_espacio,item,valor)
	{
		document.getElementById('div_loader').style.display = 'block';
		if(valor != '')
		{
			$.ajax({	
				type:"POST",
				data:"cod_espacio=<?php echo $cod_espacio ?>&item=" + item+"&valor=" + valor,
				url:"procesos/agregar_info_equipo_add.php",
				success:function(r)
				{
					datos=jQuery.parseJSON(r);
					if(datos['consulta'] == 1)
					{
						w_alert({ titulo: 'Info agregada con exito', tipo: 'success' });
						$('#tabla_info').load('tablas/info_equipo.php/?cod_espacio=<?php echo $cod_espacio ?>', function(){cerrar_loader();});
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
			w_alert({ titulo: 'Ingrese la info', tipo: 'danger' });
			document.getElementById("input_info_"+item).focus();
		}

		cerrar_loader();
	}

	function eliminar_info(num_item)
	{
		document.getElementById('div_loader').style.display = 'block';
		$.ajax({
			type:"POST",
			data:"cod_espacio=<?php echo $cod_espacio ?>&num_item="+num_item,
			url:"procesos/eliminar_info_servicio.php",
			success:function(r)
			{
				datos=jQuery.parseJSON(r);
				if (datos['consulta'] == 1)
				{
					w_alert({ titulo: 'Info eliminada', tipo: 'success' });
					document.getElementById('div_loader').style.display = 'block';
					$('#tabla_info').load('tablas/info_equipo.php/?cod_espacio=<?php echo $cod_espacio ?>', function(){cerrar_loader();});
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

	function cambio_tipo_info(seleccion)
	{
		var tipo = seleccion.split('/');
		tipo = tipo[1];
		
		if(tipo == undefined)
			document.getElementById('input_valor_info').hidden = true;
		else
			document.getElementById('input_valor_info').hidden = false;
		if(tipo == 'Texto')
			document.getElementById('input_valor_info').type = 'text';
		if(tipo == 'Número')
			document.getElementById('input_valor_info').type = 'number';
		if(tipo == 'Fecha')
			document.getElementById('input_valor_info').type = 'date';
		if(tipo == 'Fecha/Hora')
			document.getElementById('input_valor_info').type = 'datetime';
		if(tipo == 'Hora')
			document.getElementById('input_valor_info').type = 'time';
	}


</script>