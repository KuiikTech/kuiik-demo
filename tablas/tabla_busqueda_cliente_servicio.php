<?php 
require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();

$page = 1;
$start = 0;

$busqueda = str_replace("***", "%",$_GET['input_buscar']);

if(isset($_GET['page']))
{
	$page = $_GET['page'];
	$start = ($page-1)*10;
}

$sql = "SELECT count(*) FROM `clientes` WHERE `nombre` LIKE '%$busqueda%' OR `id` LIKE '%$busqueda%' OR `telefono` LIKE '%$busqueda%' ORDER BY `nombre` ASC";
$result=mysqli_query($conexion,$sql);
$mostrar=mysqli_fetch_row($result);

$page_items['start'] = $start+1;
$page_items['end'] = $start+10;
$page_items['total'] = $mostrar[0];

$total_pages = intval(ceil($mostrar[0]/10));

if($page_items['end']>$page_items['total'])
	$page_items['end'] = $page_items['total'];

$info_page = 'Mostrando <b class="text-primary">'.$page_items['start'].'</b> a <b class="text-primary">'.$page_items['end'].'</b> de <b class="text-primary">'.$page_items['total'].'</b> entradas totales';

$sql = "SELECT `codigo`, `id`, `nombre`, `telefono` FROM `clientes` WHERE `nombre` LIKE '%$busqueda%' OR `id` LIKE '%$busqueda%' OR `telefono` LIKE '%$busqueda%' ORDER BY `nombre` ASC LIMIT $start,10";
$result=mysqli_query($conexion,$sql);

$nombre_tabla = 'Clientes encontrados';

?>
<div class="container px-3 m-0">
	<div class="card-header text-center py-1">
		<h5><?php echo $nombre_tabla ?></h5>
	</div>
	<ul class="list-group list-group-flush">
		<?php 
		$busqueda_v = explode('%', $busqueda);

		while ($mostrar=mysqli_fetch_row($result)) 
		{ 
			$nombre = strtolower($mostrar[2].' '.$mostrar[3]);
			$identificacion = $mostrar[1];
			$telefono = $mostrar[3];
			foreach ($busqueda_v as $i => $palabra)
			{
				$nombre = ucwords(str_ireplace($palabra,'??//'.ucwords($palabra).'))//', $nombre));
				$identificacion = ucwords(str_ireplace($palabra,'??//'.ucwords($palabra).'))//', $identificacion));
				$telefono = ucwords(str_ireplace($palabra,'??//'.ucwords($palabra).'))//', $telefono));
			}

			$nombre = ucwords(str_ireplace('??//','<mark>', $nombre));
			$identificacion = ucwords(str_ireplace('??//','<mark>', $identificacion));
			$telefono = ucwords(str_ireplace('??//','<mark>', $telefono));

			$nombre = ucwords(str_ireplace('))//','</mark>', $nombre));
			$identificacion = ucwords(str_ireplace('))//','</mark>', $identificacion));
			$telefono = ucwords(str_ireplace('))//','</mark>', $telefono));
			?>
			<a href="javascript:selecionar_cliente('<?php echo $mostrar[0] ?>','<?php echo $mostrar[1] ?>','<?php echo $nombre ?>','<?php echo $telefono ?>')">
				<li class="list-group-item-bg list-group-item d-flex justify-content-between align-items-center p-1">
					<div class="row align-items-center text-truncate">
						<span class="h6 mb-0 text-capitalize"><?php echo $nombre ?></span>
						<small class="text-muted d-block">ID: <?php echo $identificacion ?> <b class="text-danger"> | </b>  TEL: <?php echo $telefono ?></small>
					</div>
				</li>	
			</a>
			<?php 
		} 
		?>
	</ul>

	<div class="row pt-2 text-center">
		<div class="col-sm-12 col-md-12 text-center">
			<small><?php echo $info_page ?></small>
		</div>
		<div class="col-sm-12 col-md-12 text-center pt-2">
			<ul class="pagination">
				<?php 
				$disabled = '';
				if($page < 2)
					$disabled = 'disabled'
				?>
				<li class="paginate_button page-item <?php echo $disabled ?>">
					<a href="javascript:$('#tabla_busqueda_cliente_servicio').load('tablas/tabla_busqueda_cliente_servicio.php/?page=<?php echo $page-1 ?>&input_buscar=<?php echo $busqueda ?>', function(){cerrar_loader();});" class="page-link"><</a>
				</li>
				<?php 
				for ($i=1; $i < $total_pages+1; $i++)
				{ 
					$active = '';
					if($page == $i)
						$active = 'active';

					$cant_pages2 = 3;
					if($page<4)
						$cant_pages2 = $page;
					if($page>($total_pages-4))
						$cant_pages2 = $page-($total_pages-6);

					$cant_pages = 7 - $cant_pages2;
					
					if($i>($page-$cant_pages2) && $i<($page+$cant_pages))
					{
						?>
						<li class="paginate_button page-item <?php echo $active ?>">
							<a href="javascript:$('#tabla_busqueda_cliente_servicio').load('tablas/tabla_busqueda_cliente_servicio.php/?page=<?php echo $i ?>&input_buscar=<?php echo $busqueda ?>', function(){cerrar_loader();});" class="page-link"><?php echo $i ?></a>
						</li>
						<?php 
					}
				}
				$disabled = '';
				if(($page == $total_pages) || $total_pages == 0)
					$disabled = 'disabled'
				?>
				<li class="paginate_button page-item <?php echo $disabled ?>">
					<a href="javascript:$('#tabla_busqueda_cliente_servicio').load('tablas/tabla_busqueda_cliente_servicio.php/?page=<?php echo $page+1 ?>&input_buscar=<?php echo $busqueda ?>', function(){cerrar_loader();});" class="page-link">></a>
				</li>
			</ul>
		</div>
	</div>

</div>