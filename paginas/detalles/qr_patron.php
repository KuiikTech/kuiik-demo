<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
session_set_cookie_params(7*24*60*60);
session_start();

if(isset($_GET['cod_espacio']))
	$codigo = 'id='.$_GET['cod_espacio'];
if(isset($_GET['cod_servicio']))  
	$codigo = 'ids='.$_GET['cod_servicio'];
$tipo = $_GET['tipo'];

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	if(isset($_SESSION['usuario_restaurante2']))
		$local = 'rancho2';
	else
		$local = 'rancho1';

	?>
	<div class="row text-center m-0 p-2">
		<h4>Acceso a ingreso de patron</h4>
	</div>
	<div class="row text-center m-0 p-2" id="qrcode" style="width:auto; height:auto;background: white;"></div>

	<div class="row m-0 p-2 text-center" style="display: block;">
		<div class="loader text-danger"><div class="bg-info"></div><div class="bg-info"></div><div class="bg-info"></div><div class="bg-info"></div><div class="bg-info"></div><div class="bg-info"></div><div class="bg-info"></div><div class="bg-info"></div><div class="bg-info"></div><div class="bg-info"></div><div class="bg-info"></div><div class="bg-info"></div></div>
	</div>

	<div class="row text-center m-auto p-2">
		<button type="button" class="btn btn-sm btn-outline-danger btn-round" onclick="$('#Modal_qr_patron').modal('toggle');">Cancelar</button>
	</div>

	<script type="text/javascript">
		var qrcode = new QRCode(document.getElementById("qrcode"), {
			width : 200,
			height : 200
		});

		function makeCode () {		
			var url = "http://<?php echo $local ?>.witsoft.co/patternlock.php?<?php echo $codigo ?>&uid=<?php echo $usuario ?>&tipo=<?php echo $tipo ?>";

			qrcode.makeCode(url);
		}

		makeCode();

		var intervalo;
		intervalo=setInterval("cambios()",5000);

		function cambios()
		{
			$.ajax({
				type:"POST",
				url:"procesos/cambios_patron.php",
				success:function(r)
				{
					datos=jQuery.parseJSON(r);
					if(datos['consulta'] == 1)
					{
						$('#Modal_qr_patron').modal('toggle');
						document.getElementById('div_loader').style.display = 'block';
						w_alert({ titulo: 'Patron agregado con exito', tipo: 'success' });
						<?php 
						if(isset($_GET['cod_espacio']))
						{
							$cod_espacio = $_GET['cod_espacio'];
							?>
							$('#tabla_seguridad').load('tablas/seguridad_equipo.php/?cod_espacio=<?php echo $cod_espacio ?>', function(){cerrar_loader();});
							<?php 
						}
						if(isset($_GET['cod_servicio']))  
						{
							$cod_servicio = $_GET['cod_servicio'];
							?>
							mostrar_servicio(<?php echo $cod_servicio ?>);
							<?php 
						}
						?>
						document.getElementById('div_qr_patron').innerHTML = "";
						clearInterval(intervalo);
					}
				}
			});
		}

	</script>
	<?php 
}
else
{
	?>
	<script type="text/javascript">
		window.location.href = "./login.php";
	</script>
	<?php 
}
?>