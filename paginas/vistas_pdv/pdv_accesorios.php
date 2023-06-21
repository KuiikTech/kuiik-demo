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

$cod_servicio = $_GET['cod_servicio'];

$consulta = '';

if (isset($_SESSION['usuario_restaurante2']))
	$bodega = 'PDV_2';
else
	$bodega = 'PDV_1';

if (isset($_GET['cod_categoria']))
{
	$cod_categoria = $_GET['cod_categoria'];
	$consulta = "WHERE categoria='$cod_categoria' AND estado = 'DISPONIBLE' order by descripcion ASC";
}
else
{
	if (isset($_GET['consulta']))
	{
		$busqueda = str_replace("***", "%",$_GET['consulta']);
		$consulta="WHERE (descripcion LIKE '%$busqueda%' OR barcode LIKE '%$busqueda%') AND estado = 'DISPONIBLE' order by descripcion ASC";
	}
	else
	{
		if (isset($_GET['consulta0']))
			$consulta="WHERE codigo<0";
	}
}
$sql_accesorios = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` $consulta";
$result_accesorios=mysqli_query($conexion,$sql_accesorios);

?>
<div class="row mx-1 contendor_productos mb-2">
	<?php 
	$num_item = 0;
	while ($mostrar_accesorios=mysqli_fetch_row($result_accesorios)) 
	{
		$nombre_accesorio = $mostrar_accesorios[1];

		$cod_accesorio = $mostrar_accesorios[0];

		$inventario = array();
		if($bodega == 'PDV_1')
		{
			$bodega_inventario = 'inventario_1';
			if ($mostrar_accesorios[6] != '')
				$inventario = json_decode($mostrar_accesorios[6],true);
		}
		if($bodega == 'PDV_2')
		{
			$bodega_inventario = 'inventario_2';
			if ($mostrar_accesorios[7] != '')
				$inventario = json_decode($mostrar_accesorios[7],true);
		}

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

		if ($mostrar_accesorios[3] != '')
			$inventario_p = json_decode($mostrar_accesorios[3],true);
		if ($mostrar_accesorios[6] != '')
			$inventario_1 = json_decode($mostrar_accesorios[6],true);
		if ($mostrar_accesorios[7] != '')
			$inventario_2 = json_decode($mostrar_accesorios[7],true);

		foreach ($inventario_p as $i => $accesorio)
		{
			$cant_p += intval($accesorio['stock']);
			$valor_venta_p = $accesorio['valor_venta'];
			$valor_venta_mayor_p = $accesorio['valor_venta_mayor'];
		}
		foreach ($inventario_1 as $i => $accesorio)
		{
			$cant_1 += intval($accesorio['stock']);
			$valor_venta_1 = $accesorio['valor_venta'];
			$valor_venta_mayor_1 = $accesorio['valor_venta_mayor'];
		}
		foreach ($inventario_2 as $i => $accesorio)
		{
			$cant_2 += intval($accesorio['stock']);
			$valor_venta_2 = $accesorio['valor_venta'];
			$valor_venta_mayor_2 = $accesorio['valor_venta_mayor'];
		}

		?>
		<div class="w-100 p-0">
			<div class="card-header p-0 px-1 d-flex align-items-center justify-content-between bg-primary text-white">
				<div class="d-flex align-items-center "><?php echo $nombre_accesorio ?></div>
				<div class="d-flex align-items-center">
					<span class="mr-2">Cod: <?php echo $cod_accesorio ?> </span>
					<ul class="navbar-nav navbar-nav-icons flex-row align-items-center ml-3">
						<li class="nav-item dropdown">
							<a class="nav-link p-0 " id="navbarDropdownUser" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
			<ul class="pc-mega-list m-0 p-0" style="list-style: none !important; ">
				<?php
				foreach ($inventario as $i => $accesorio)
				{
					$costo = $accesorio['costo'];
					$valor_venta = $accesorio['valor_venta'];
					$valor_venta_mayor = $accesorio['valor_venta_mayor'];
					$creador = $accesorio['creador'];
					$cant_inicial = $accesorio['cant_inicial'];
					$stock = $accesorio['stock'];
					$marca = $accesorio['marca'];

					$descripcion = '- '.ucwords(strtolower($nombre_accesorio)) .' ('.ucwords(strtolower($marca)).')';

					if($stock>0)
					{
						?>
						<li>
							<a onclick="agregar_accesorio('<?php echo $cod_accesorio ?>','<?php echo $i ?>','<?php echo $cod_servicio ?>','<?php echo 1 ?>')" class="dropdown-item p-0">
								<div class="card-body w-100 px-1 py-0 border-bottom d-flex align-items-center justify-content-between">
									<p class="d-inline-block mb-0 text-dark">Stock: <b class="text-dark"><?php echo $stock ?></b></p>
									<p class="d-inline-block mb-0 text-dark">Marca:<b class="text-dark"><?php echo $marca ?></b></p>
									<div title="VM: $<?php echo number_format($accesorio['valor_venta_mayor'],0,'.','.') ?>">
										<p class="d-inline-block mb-0 h4"><span class="fa fa-dollar-sign f-24 text-success"></span><?php echo number_format($accesorio['valor_venta'],0,'.','.')?></p>
									</div>
								</div>
							</a>
						</li>
						<?php 
					}
				}
				?>
			</ul>
		</div>
		<?php 
		$num_item++;
	}

	if ($num_item==0)
	{
		if (isset($_GET['cod_categoria']))
		{
			?>
			<div class="ml-3">No se han registrado accesorios para esta categoría.</div>
			<?php 
		}
		else
		{
			?>
			<div class="ml-3">No se encontraron accesorios para esta busqueda.</div>
			<?php 
		}
	}
	?>
</div>
<!---->