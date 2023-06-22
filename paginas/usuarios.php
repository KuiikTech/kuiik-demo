<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');

require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();

$num_tabla = 1;
session_set_cookie_params(7*24*60*60);
session_start();

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	require_once "../clases/permisos.php";
	$obj_permisos = new permisos();
	$acceso = $obj_permisos->buscar_permiso($usuario,'Usuarios','VER');

	if($acceso == 'SI')
	{
		?>
		<div id="div_contenido"></div>
		
		<script type="text/javascript">
			$(document).ready(function()
			{
				document.title = 'Usuarios | Restaurante | W-POS | Kuiik';
				$('.active').removeClass("active")
				document.getElementById('a_usuarios').classList.add("active");

				document.getElementById('div_loader').style.display = 'block';
				$('#div_contenido').load('tablas/usuarios.php', function(){cerrar_loader();});
			});


		</script>


		<?php 
	}
	else
		require_once 'error_403.php';
}
else
	header("Location:login.php");
?>