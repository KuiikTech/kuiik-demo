<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');

require_once "../clases/conexion.php";
require_once "../clases/permisos.php";
$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();
$obj_permisos = new permisos();
session_set_cookie_params(7*24*60*60);
session_start();
$usuario = $_SESSION['usuario_restaurante'];

$acceso = $obj_permisos->buscar_permiso($usuario,'Usuarios','PERMISOS');

if($acceso == 'SI')
{
	$cod_usuario = $_GET['cod_usuario'];

	$sql = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `foto`, `telefono`, `rol`, `fecha_registro`, `estado`, `permisos` FROM `usuarios` WHERE codigo = '$cod_usuario'";
	$result=mysqli_query($conexion,$sql);
	$mostrar=mysqli_fetch_row($result);

	$nombre_usuario = $mostrar[2].' '.$mostrar[3];
	$count_filas = json_decode($mostrar[10],true);

	foreach ($count_filas as $i => $count_filas_2)
	{
		$contador[$i] = 0;
		$count_paginas = count($count_filas_2);
		$paginas_permisos = json_decode($mostrar[10],true);
	}

	?>
	<div class="card">
		<div class="card-header text-center row p-2">
			<h3>Permisos para <?php echo $nombre_usuario ?></h3>
		</div>
		<div class="card-body">

			<table class="table text-dark table-sm table-bordered" id="tabla_permisos" style="width: 100%">
				<thead>
					<tr class="text-center">
						<th class="p-1">Pagina</th>
						<th class="p-1" width="150px">Tipo de Permiso</th>
						<th class="p-1" width="100px">Autorizado</th>
					</tr>
				</thead>
				<div class="overflow-auto">
					<tbody class="overflow-auto">
						<?php 
						if(isset($paginas_permisos))
						{
							$num_item = 1;
							$vista_actual = '';
							$pagina_actual = '';
							$bg_btn = '';
							$count_paginas = count($paginas_permisos);
							foreach ($paginas_permisos as $j => $tipo_permisos)
							{
								$count_tipo = count($tipo_permisos);
								foreach ($tipo_permisos as $k => $valor)
								{
									if($valor == 'NO')
										$bg_btn = 'bg-danger';
									if($valor == 'SI')
										$bg_btn = 'bg-success';
									?>
									<tr class="alineacion_vertical">
										<?php 
										if($pagina_actual != $j) 
										{
											$pagina_actual = $j;
											?>
											<td class="p-1 text-center bg-white alineacion_vertical" rowspan="<?php echo $count_tipo ?>"><strong class="h3"><?php echo $j ?></strong></td>
											<?php 
										}
										else
										{
											?>
											<td class="p-1 text-center" hidden=""><?php echo $j ?></td>
											<?php 
										}
										?>
										<td class="p-1 text-center"><b><?php echo $k ?></b></td>
										<td class="p-1 text-center">
											<a class="btn btn-sm <?php echo $bg_btn ?> btn-round text-white" onclick="cambiar_permiso('<?php echo $cod_usuario ?>','<?php echo $i ?>','<?php echo $j ?>','<?php echo $k ?>')">
												<?php echo $valor ?>
											</a>
										</td>
									</tr>
									<?php 
								}

								$num_item ++;	
							}
						}
						?>
					</tbody>
				</div>
			</table>

		</div>
	</div>

	<script type="text/javascript">

		function cambiar_permiso(cod_usuario,vista,pagina,tipo)
		{
			$.ajax({
				type:"POST",
				data:"cod_usuario="+cod_usuario+"&vista="+vista+"&pagina="+pagina+"&tipo="+tipo,
				url:"procesos/cambiar_permiso.php",
				success:function(r)
				{
					datos=jQuery.parseJSON(r);
					if (datos['consulta']==1)
					{
						if(datos['estado'] == 'SI')
							w_alert({ titulo: 'Permiso concedido', tipo: 'success' });
						else
							w_alert({ titulo: 'Permiso negado', tipo: 'danger' });
						$('#div_contenido').load('paginas/permisos_usuarios.php/?cod_usuario='+cod_usuario);
					}
					else
						w_alert({ titulo: datos['consulta'], tipo: 'danger' });
					if(datos['consulta'] == 'Reload')
					{
						document.getElementById('div_login').style.display = 'block';
cerrar_loader();
						
					}
				}
			});
		}

	</script>
	<?php 
}
else
	require_once 'error_403.php';
?>