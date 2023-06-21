<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();
$conexion = $obj->conexion();
session_set_cookie_params(7 * 24 * 60 * 60);
session_start();

if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];

	$sql_acceso = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Acceso a mesas'";
	$result_acceso = mysqli_query($conexion, $sql_acceso);
	$mostrar_acceso = mysqli_fetch_row($result_acceso);

	$acceso_mesas = $mostrar_acceso[2];

	$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
	$result_e = mysqli_query($conexion, $sql_e);
	$ver_e = mysqli_fetch_row($result_e);

	$rol = $ver_e[5];

	$sql_salones = "SELECT `codigo`, `nombre`, `estado`, `color` FROM `salones` WHERE estado = 'ACTIVO' order by orden ASC";
	$result_salones = mysqli_query($conexion, $sql_salones);

	if ($rol == 'Mesero' || $rol == 'Administrador') {
		$sql_mesas = "SELECT `cod_mesa`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_apertura`, `mesero` FROM `mesas` WHERE `estado` != 'ELIMINADO' AND `salon` = 0 order by cod_mesa ASC";
		$result_mesas = mysqli_query($conexion, $sql_mesas);

?>
		<div class="">
			<?php
			while ($mostrar_mesas = mysqli_fetch_row($result_mesas)) {
				$total_mesa = 0;
				$nombre = $mostrar_mesas[1];
				$descripcion = substr($mostrar_mesas[2], 0, 12);
				$cod_mesa = $mostrar_mesas[0];
				$btn_estado = 'btn-outline-primary';
				$mesero = $mostrar_mesas[6];

				if ($mostrar_mesas[4] == 'LIBRE') {
					$btn_estado = 'btn-outline-success';
					$mesero = '---';
				} else
					$btn_estado = 'btn-outline-danger';

				if ($mostrar_mesas[3] != '') {
					$productos_mesa = json_decode($mostrar_mesas[3], true);
					foreach ($productos_mesa as $i => $producto) {
						$cod_producto = $producto['codigo'];
						$cant = $producto['cant'];
						$descripcion_str = $producto['descripcion'];
						//$descripcion = str_split($descripcion, 20);
						$valor_unitario = $producto['valor_unitario'];

						$valor_total = $cant * $valor_unitario;
						$total_mesa += $valor_total;

						$valor_unitario = number_format($valor_unitario, 0, '.', '.');
						$valor_total = '$' . number_format($valor_total, 0, '.', '.');
					}
				}
				$total_mesa = '$' . number_format($total_mesa, 0, '.', '.');

				if ($rol != 'Administrador' && $acceso_mesas != 'Todos' && $acceso_mesas != 'CreadorVer') {
					if ($mesero != $usuario)
						$total_mesa = '---';
				}

				$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$mesero'";
				$result_e = mysqli_query($conexion, $sql_e);
				$ver_e = mysqli_fetch_row($result_e);
				if ($ver_e != null)
					$mesero = $ver_e[0]; //. ' ' . $ver_e[1];
			?>
				<button type="button" class="py-1 px-3 my-1 btn <?php echo $btn_estado ?> btn-round" onclick="abrir_mesa('<?php echo $cod_mesa ?>')" id="btn_mesa_<?php echo $cod_mesa ?>">
					<div class="nav-link-icon__wrapper lh-1">
						<b><?php echo $nombre ?></b>
						<br>
						<span class="descripcion_mesa"><?php echo $total_mesa ?></span>
						<br>
						<small><?php echo $mesero ?></small>
					</div>
				</button>
			<?php
			}
			?>
			<button type="button" hidden class="p-2 my-1 px-4 btn btn-sm btn-outline-primary btn-round" onclick="crear_mesa()">
				<div class="nav-link-icon__wrapper">
					<h1>+</h1>
				</div>
			</button>
		</div>

		<?php
		while ($mostrar_salones = mysqli_fetch_row($result_salones)) {
			$cod_salon = $mostrar_salones[0];
			$nombre_salon = $mostrar_salones[1];
			$color = $mostrar_salones[3];

			if ($color == 'success') {
				$color_titulo = 'bg-success text-white';
				$border_card = 'border border-success border-2';
			}
			if ($color == 'danger') {
				$color_titulo = 'bg-danger text-white';
				$border_card = 'bg-soft-danger';
			}
			if ($color == 'warning') {
				$color_titulo = 'bg-warning text-white';
				$border_card = 'bg-soft-warning';
			}
			if ($color == 'info') {
				$color_titulo = 'bg-info text-white';
				$border_card = 'bg-soft-info';
			}
			if ($color == 'secondary') {
				$color_titulo = 'bg-secondary text-white';
				$border_card = 'bg-soft-secondary';
			}
			if ($color == '') {
				$color_titulo = 'text-dark';
				$border_card = '';
			}
		?>
			<div class="card m-1 p-0 <?php echo $border_card ?>">
				<div class="card-header border-bottom p-1 d-flex <?php echo $color_titulo ?>">
					<h6 class="m-0"><b><?php echo $nombre_salon ?></b></h6>
				</div>
				<div class="card-body p-0">
					<div class="px-2">
						<?php
						$sql_mesas = "SELECT `cod_mesa`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_apertura`, `mesero` FROM `mesas` WHERE `estado` != 'ELIMINADO' AND `salon` = '$cod_salon' order by cod_mesa ASC";
						$result_mesas = mysqli_query($conexion, $sql_mesas);
						while ($mostrar_mesas = mysqli_fetch_row($result_mesas)) {
							$total_mesa = 0;
							$nombre = $mostrar_mesas[1];
							$descripcion = substr($mostrar_mesas[2], 0, 12);
							$cod_mesa = $mostrar_mesas[0];
							$btn_estado = 'btn-outline-primary';
							$mesero = $mostrar_mesas[6];

							if ($mostrar_mesas[4] == 'LIBRE') {
								$btn_estado = 'btn-outline-success';
								$mesero = '---';
							} else
								$btn_estado = 'btn-outline-danger';

							if ($mostrar_mesas[3] != '') {
								$productos_mesa = json_decode($mostrar_mesas[3], true);
								foreach ($productos_mesa as $i => $producto) {
									$cod_producto = $producto['codigo'];
									$cant = $producto['cant'];
									$descripcion_str = $producto['descripcion'];
									//$descripcion = str_split($descripcion, 20);
									$valor_unitario = $producto['valor_unitario'];

									$valor_total = $cant * $valor_unitario;
									$total_mesa += $valor_total;

									$valor_unitario = number_format($valor_unitario, 0, '.', '.');
									$valor_total = '$' . number_format($valor_total, 0, '.', '.');
								}
							}
							$total_mesa = '$' . number_format($total_mesa, 0, '.', '.');

							if ($rol != 'Administrador' && $acceso_mesas != 'Todos' && $acceso_mesas != 'CreadorVer') {
								if ($mesero != $usuario)
									$total_mesa = '---';
							}

							$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$mesero'";
							$result_e = mysqli_query($conexion, $sql_e);
							$ver_e = mysqli_fetch_row($result_e);
							if ($ver_e != null)
								$mesero = $ver_e[0]; //. ' ' . $ver_e[1];
						?>
							<button type="button" class="py-1 px-3 my-1 btn <?php echo $btn_estado ?> btn-round" onclick="abrir_mesa('<?php echo $cod_mesa ?>')" id="btn_mesa_<?php echo $cod_mesa ?>">
								<div class="nav-link-icon__wrapper lh-1">
									<b><?php echo $nombre ?></b>
									<br>
									<span class="descripcion_mesa"><?php echo $total_mesa ?></span>
									<br>
									<small><?php echo $mesero ?></small>
								</div>
							</button>
						<?php
						}
						?>
						<button type="button" hidden class="p-2 my-1 px-4 btn btn-sm btn-outline-primary btn-round" onclick="crear_mesa()">
							<div class="nav-link-icon__wrapper">
								<h1>+</h1>
							</div>
						</button>
					</div>

				</div>
			</div>

		<?php
		}
		?>


		<script type="text/javascript">
			function crear_mesa() {
				$.ajax({
					url: "procesos/crear_mesa.php",
					success: function(r) {
						datos = jQuery.parseJSON(r);
						if (datos['consulta'] == 1) {
							document.getElementById('div_loader').style.display = 'block';
							$('#div_row_mesas').load('paginas/vistas_pdv/pdv_mesas.php', function() {
								cerrar_loader();
							});
							cod_mesa = datos['cod_mesa'];
							abrir_mesa(cod_mesa);
						} else {
							w_alert({
								titulo: datos['consulta'],
								tipo: 'danger'
							});
							if (datos['consulta'] == 'Reload') {
								document.getElementById('div_login').style.display = 'block';
								cerrar_loader();

							}
							cerrar_loader();
						}
					}
				});
			}

			function abrir_mesa(cod_mesa) {
				document.getElementById('div_loader').style.display = 'block';
				$.ajax({
					type: "POST",
					data: "cod_mesa=" + cod_mesa,
					url: "procesos/abrir_mesa.php",
					success: function(r) {
						datos = jQuery.parseJSON(r);
						if (datos['consulta'] == 1) {
							$('#div_row_mesas').load('paginas/vistas_pdv/pdv_mesas.php', function() {
								cerrar_loader();
							});
							$('#div_productos').load('paginas/vistas_pdv/pdv_categorias.php/?cod_mesa=' + cod_mesa, function() {
								cerrar_loader();
							});
							$('#div_cuenta').load('paginas/vistas_pdv/pdv_cuenta.php/?cod_mesa=' + cod_mesa, function() {
								cerrar_loader();
							});
							document.getElementById('div_row_mesas').hidden = true;
							document.getElementById('div_cont_prod_cuenta').hidden = false;
							document.getElementById('div_row_ventas_dia').hidden = true;
						} else {
							w_alert({
								titulo: datos['consulta'],
								tipo: 'danger'
							});
							if (datos['consulta'] == 'Reload') {
								document.getElementById('div_login').style.display = 'block';
								cerrar_loader();

							}
							cerrar_loader();
						}
					}
				});
			}
		</script>

	<?php
	} else {
	?>
		<div class="row m-0 p-2">
			<div class="bg-warning p-2 rounded-pill text-center">
				<h4 class="text-white mb-0">Solo los meseros tiene acceso al punto de venta</h4>
			</div>
		</div>
<?php
	}
} else
	header("Location:login.php");
?>