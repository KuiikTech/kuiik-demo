<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();
$conexion = $obj->conexion();

$cod_mesa = $_GET['cod_mesa'];

$sql_mesa = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura` FROM `mesas` WHERE cod_mesa='$cod_mesa'";
$result_mesa = mysqli_query($conexion, $sql_mesa);
$mostrar_mesa = mysqli_fetch_row($result_mesa);

$nombre_mesa = $mostrar_mesa[1];

?>
<div class="card_body">
	<div class="row text-center p-1 m-0">
		<span onclick="atras()" class="btn btn-sm btn-outline-primary btn-round p-1 m-0" style="width: 32px; height: 32px;">
			<span class="fa fa-chevron-left"></span>
		</span>
		<span class="col h4 mb-0"> AGREGAR ITEMS A <strong class="text-danger">CUENTA <?php echo $nombre_mesa ?></strong></span>
	</div>
	<hr class="my-1">
	<div class="row m-0 py-0 px-1">
		<?php
		$num_item = 0;
		$sql_categorias = "SELECT `cod_categoria`, `nombre` FROM `categorias_productos` order by nombre ASC";
		$result_categorias = mysqli_query($conexion, $sql_categorias);
		while ($mostrar_categorias = mysqli_fetch_row($result_categorias)) {
			$nombre_cat = ucwords(mb_strtolower($mostrar_categorias[1]));
			$cod_categoria = $mostrar_categorias[0];
		?>
			<button type="button" class="btn btn-sm btn-outline-secondary btn-round mt-1 w-auto" onclick="mostrar_productos('<?php echo $cod_categoria ?>','<?php echo $cod_mesa ?>')"><?php echo $nombre_cat ?></button>
		<?php
			$num_item++;
		}
		?>
	</div>
	<hr class="my-1">
	<div class="row clearfix mx-4">
		<input type="text" class="form-control form-control-sm" id="busqueda" name="busqueda" autocomplete="off" placeholder="Busqueda de productos" onKeyUp="mostrar_busqueda('<?php echo $cod_mesa ?>');">
	</div>
	<hr class="my-1">
	<div class="conatiner px-0" id="div_tabla_productos"></div>
</div>

<script type="text/javascript">
	$('#cantidad_pedido').keypress(function(e) {
		if (e.keyCode == 13)
			$('#btn_agregar_producto_m').click();
	});

	function mostrar_productos(cod_categoria, cod_mesa) {
		document.getElementById('div_loader').style.display = 'block';
		$('#div_tabla_productos').load('paginas/vistas_pdv/pdv_productos.php/?cod_categoria=' + cod_categoria + '&cod_mesa=' + cod_mesa, function() {
			cerrar_loader();
		});
	}

	function mostrar_busqueda(cod_mesa) {
		var busqueda = document.getElementById("busqueda").value;
		busqueda = busqueda.replace(/ /g, "***");
		if (busqueda != '') {
			if (busqueda.length > 2) {
				document.getElementById('div_loader').style.display = 'block';
				$('#div_tabla_productos').load('paginas/vistas_pdv/pdv_productos.php/?consulta=' + busqueda + '&cod_mesa=' + cod_mesa, function() {
					cerrar_loader();
				});
			} else {
				document.getElementById('div_loader').style.display = 'block';
				$('#div_tabla_productos').load('paginas/vistas_pdv/pdv_productos.php/?consulta0=' + busqueda + '&cod_mesa=' + cod_mesa, function() {
					cerrar_loader();
				});
			}
		}
	}
</script>