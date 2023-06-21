<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
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

	$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
	$result_e = mysqli_query($conexion, $sql_e);
	$ver_e = mysqli_fetch_row($result_e);

	$rol = $ver_e[5];

	$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado` FROM `caja` WHERE estado = 'ABIERTA'";
	$result = mysqli_query($conexion, $sql);
	$mostrar = mysqli_fetch_row($result);


	if ($rol == 'Mesero' || $rol == 'Administrador') {
		if ($mostrar != NULL) {
			$cod_caja = $mostrar[0];

			$fecha_cierre = date('Y-m-d G:i:s');
			$fecha_apertura = $mostrar[2];

			$sql_ventas = "SELECT `codigo`, `cliente`, `productos`, `pago`, `fecha`, `cobrador`, `estado` FROM `ventas` WHERE fecha BETWEEN '$fecha_apertura' AND '$fecha_cierre'";

?>
			<div class="card px-1">
				<div class="card-header text-center p-2">
					<h4>Ventas del dia (Caja # <?php echo $cod_caja ?>)</h4>
				</div>
				<div class="card-body p-2 py-0">
					<table class="table text-dark table-sm" id="tabla_ventas">
						<thead>
							<tr class="text-center">
								<th class="p-1">Cod</th>
								<th class="p-1">Cliente</th>
								<th class="p-1">Productos</th>
								<th class="p-1">Total</th>
								<th class="p-1">Fecha</th>
								<th class="p-1">Método Pago</th>
								<th class="p-1">Creador</th>
							</tr>
						</thead>
						<tbody class="overflow-auto">
							<?php
							$result_ventas = mysqli_query($conexion, $sql_ventas);
							$total_ventas = 0;

							while ($mostrar_ventas = mysqli_fetch_row($result_ventas)) {
								$cod_venta = $mostrar_ventas[0];

								$total = 0;
								$cliente = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar_ventas[1]);
								$cliente = str_replace("	", " ", $cliente);
								$cliente = json_decode($cliente, true);

								$estado_venta = $mostrar_ventas[6];

								$productos_venta = json_decode($mostrar_ventas[2], true);
								foreach ($productos_venta as $i => $producto)
									$total += $producto['valor_unitario'] * $producto['cant'];

								$cobrador = $mostrar_ventas[5];

								if ($cobrador != 0) {
									$sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$cobrador'";
									$result_e = mysqli_query($conexion, $sql_e);
									$ver_e = mysqli_fetch_row($result_e);

									if ($ver_e != null)
										$cobrador = $ver_e[0];
								} else
									$cobrador = 'Sistema';

								$fecha_venta = strftime("%e %b %Y", strtotime($mostrar_ventas[4]));
								$fecha_venta = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_venta));

								$fecha_venta .= date(' <br> h:i A', strtotime($mostrar_ventas[4]));
								$bg_estado = '';


								$pagos = json_decode($mostrar_ventas[3], true);
								foreach ($pagos as $i => $pago) {
									if ($pago['tipo'] == 'Descuento')
										$total += $pago['valor'];
								}

								if ($estado_venta == 'ANULADA') {
									$bg_estado = 'bg-danger-light';
									$total = 0;
								}
							?>
								<tr>
									<td class="p-1 text-center <?php echo $bg_estado ?>"><?php echo str_pad($mostrar_ventas[0], 3, "0", STR_PAD_LEFT) ?></td>
									<td class="p-1"><?php echo $cliente['nombre'] ?></td>
									<td class="p-1 text-left text-sm">
										<?php
										foreach ($productos_venta as $j => $producto) {
											echo '<b>' . $producto['cant'] . '</b> - ';
											echo $producto['descripcion'] . ' - ';
											echo '<b>$' . number_format($producto['valor_unitario'] * $producto['cant'], 0, '.', '.') . '</b> <br>';
										}
										?>
									</td>
									<td class="p-1 text-right"><b>$<?php echo number_format($total, 0, '.', '.') ?></b></td>
									<td class="p-1 text-center"><small><?php echo $fecha_venta ?></small></td>
									<td class="p-1 text-left">
										<?php
										foreach ($pagos as $i => $pago)
											echo '-> ' . $pago['tipo'] . ' <b>($' . number_format($pago['valor'], 0, '.', '.') . ')</b><br>';
										?>
									</td>
									<td class="p-1 text-center"><?php echo $cobrador ?></td>
								</tr>
							<?php
								$total_ventas += $total;
							}
							?>
						</tbody>
					</table>
					<div class="row float-right mt-3">
						<h3>Total ventas: $<?php echo number_format($total_ventas, 0, '.', '.') ?></h3>
					</div>
				</div>
			</div>
		<?php
		} else {
		?>
			<div class="row m-0 p-2">
				<div class="bg-danger p-2 rounded-pill text-center">
					<h4 class="text-white mb-0">La caja no está ABIERTA</h4>
				</div>
			</div>
		<?php
		}
	} else {
		?>
		<div class="row m-0 p-2">
			<div class="bg-warning p-2 rounded-pill text-center">
				<h4 class="text-white mb-0">Solo los cajeros tiene acceso al punto de venta</h4>
			</div>
		</div>
	<?php
	}
	?>

<?php
} else {
?>
	<script type="text/javascript">
		document.getElementById('div_login').style.display = 'block';
		cerrar_loader();
		
	</script>
<?php
}
?>