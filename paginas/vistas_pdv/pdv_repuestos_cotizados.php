<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();
session_set_cookie_params(7*24*60*60);
session_start();

$busqueda = str_replace("***", "%",$_GET['consulta']);
$consulta="WHERE (descripcion LIKE '%$busqueda%' OR barcode LIKE '%$busqueda%') AND estado = 'DISPONIBLE' order by descripcion ASC";

$sql_productos = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` $consulta";
$result_productos=mysqli_query($conexion,$sql_productos);

?>
<div class="row mx-1 contendor_productos mb-2">
	<?php 
	$num_item = 0;
	while ($mostrar_productos=mysqli_fetch_row($result_productos)) 
	{
		$nombre_producto = $mostrar_productos[1];

		$cod_producto = $mostrar_productos[0];

		$inventario_p = array();
		$inventario_1 = array();
		$inventario_2 = array();

		$cant_p = 0;
		$cant_1 = 0;
		$cant_2 = 0;

		$valor_venta_p = 0;
		$valor_venta_mayor_p = 0;
		$valor_venta_1 = 0;
		$valor_venta_mayor_1 = 0;
		$valor_venta_2 = 0;
		$valor_venta_mayor_2 = 0;

		if ($mostrar_productos[3] != '')
			$inventario_p = json_decode($mostrar_productos[3],true);
		if ($mostrar_productos[6] != '')
			$inventario_1 = json_decode($mostrar_productos[6],true);
		if ($mostrar_productos[7] != '')
			$inventario_2 = json_decode($mostrar_productos[7],true);

		foreach ($inventario_p as $i => $producto)
		{
			$cant_p += $producto['stock'];
			$valor_venta_p = $producto['valor_venta'];
			$valor_venta_mayor_p = $producto['valor_venta_mayor'];
		}
		foreach ($inventario_1 as $i => $producto)
		{
			$cant_1 += $producto['stock'];
			$valor_venta_1 = $producto['valor_venta'];
			$valor_venta_mayor_1 = $producto['valor_venta_mayor'];
		}
		foreach ($inventario_2 as $i => $producto)
		{
			$cant_2 += $producto['stock'];
			$valor_venta_2 = $producto['valor_venta'];
			$valor_venta_mayor_2 = $producto['valor_venta_mayor'];
		}

		?>
		<a href="#" onclick="agregar_producto_repuesto_cotizado('<?php echo $cod_producto ?>')" class="text-dark">
			<div class="w-100 mb-2 p-1">
				<div class="card-header p-0 px-1 d-flex align-items-center justify-content-between bg-primary text-dark">
					<div class="d-flex align-items-center"><?php echo $nombre_producto ?></div>
					<div class="d-flex align-items-center">
						<span class="mr-2">Cod: <?php echo $cod_producto ?> </span>
						<ul class="navbar-nav navbar-nav-icons flex-row align-items-center ml-3">
							<li class="nav-item dropdown">
								<a class="nav-link pe-0" id="navbarDropdownUser" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<span class="fa fa-warehouse text-warning f-16"></span>
								</a>
								<div class="dropdown-menu dropdown-menu-end py-0" aria-labelledby="navbarDropdownUser">
									<div class="bg-white dark__bg-1000 rounded-2 py-2">
										<div class="dropdown-item">Principal: 
											<b><?php echo $cant_p ?></b>
											Público: <b>$<?php echo number_format($valor_venta_p,0,'.','.') ?></b>
											|
											Mayor: <b>$<?php echo number_format($valor_venta_mayor_p,0,'.','.') ?></b>
										</div>
										<div class="dropdown-item">Local 1: 
											<b><?php echo $cant_1 ?></b>
											Público: <b>$<?php echo number_format($valor_venta_1,0,'.','.') ?></b>
											|
											Mayor: <b>$<?php echo number_format($valor_venta_mayor_1,0,'.','.') ?></b>
										</div>
										<div class="dropdown-item">Local 2: 
											<b><?php echo $cant_2 ?></b>
											Público: <b>$<?php echo number_format($valor_venta_2,0,'.','.') ?></b>
											|
											Mayor: <b>$<?php echo number_format($valor_venta_mayor_2,0,'.','.') ?></b>
										</div>
									</div>
								</div>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</a>
		<?php 
		$num_item++;
	}

	if ($num_item==0)
	{
		?>
		<div class="ml-3">No se han registrado productos para esta busqueda.</div>
		<?php 
	}
	?>
</div>
<!---->