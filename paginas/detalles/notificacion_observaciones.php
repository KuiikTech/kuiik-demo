<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
session_set_cookie_params(7*24*60*60);
session_start();
$usuario = $_SESSION['usuario_restaurante'];

require_once "../../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();

$sql_notificaciones = "SELECT count(*) FROM `notificaciones` WHERE estado = 'PENDIENTE' AND usuario = '$usuario'";
$result_notificaciones=mysqli_query($conexion,$sql_notificaciones);
$mostrar_notificaciones=mysqli_fetch_row($result_notificaciones);

$num_notificaciones = $mostrar_notificaciones[0];

$sql_notificaciones = "SELECT `codigo`, `descripcion`, `tipo`, `estado`, `fecha_registro` FROM `notificaciones` WHERE estado = 'PENDIENTE' AND usuario = '$usuario'";
$result_notificaciones=mysqli_query($conexion,$sql_notificaciones);
?>

<a class="nav-link" data-bs-toggle="dropdown" href="#">
	<i class="fas fa-bell"></i>
	<?php 
	if ($num_notificaciones > 0)
		{ ?>
			<span class="badge badge-danger navbar-badge"><?php echo $num_notificaciones ?></span>
			<?php 
		}
		?>
	</a>
	<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
		<span class="dropdown-item dropdown-header"><?php echo $num_notificaciones ?> Notificaciones pendientes</span>
		<div class="dropdown-divider"></div>
		<?php 
		while ($mostrar_notificaciones=mysqli_fetch_row($result_notificaciones)) 
		{ 
			$cod_notificaciones = str_pad($mostrar_notificaciones[0],4,"0",STR_PAD_LEFT);
			$fecha_ven = date('d-m-Y h:i a',strtotime($mostrar_notificaciones[4]));

			$date1 = new DateTime($fecha_h);
			$date2 = new DateTime($fecha_ven);
			$diff = $date2->diff($date1);

			$tiempo = 'Hace ';
			if ($diff->y != 0)
				$tiempo .= $diff->y . ' años ';
			else
			{
				if ($diff->m != 0)
					$tiempo .= $diff->m . ' meses ';
				else
				{
					if ($diff->d != 0)
						$tiempo .= $diff->d . ' días ';
					else
					{
						if ($diff->h != 0)
							$tiempo .= $diff->h . ' horas ';
						else
						{
							if ($diff->i != 0)
								$tiempo .= $diff->i . ' minutos ';
							else
								$tiempo .= 'menos de 1 minuto';
						}
					}
				}
			}

			$descripcion = substr(str_replace(': ', ':<br>', $mostrar_notificaciones[1]), 0,62);
			?>  
			<a href="#" class="dropdown-item">
				<i class="fas fa-clock mr-2"></i><?php echo $cod_notificaciones .' | '.$descripcion ?>
				<span class="float-right text-muted text-sm"><?php echo $tiempo ?></span>
			</a>
			<div class="dropdown-divider"></div>
			<?php 
		}
		?>
		<a href="javascript:click_item('notificaciones','no')" class="dropdown-item dropdown-footer">Ver todas las notificaciones</a>
	</div>
	