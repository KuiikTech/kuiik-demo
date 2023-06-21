<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();

$cliente = array(
	'codigo' => '', 
	'id' => '', 
	'nombre' => '',
	'telefono' => '', 
	'direccion' => ''
);
session_set_cookie_params(7*24*60*60);
session_start();

$cod_cliente = '';
$cod_espacio = $_GET['cod_espacio'];

if(isset($_GET['cod_cliente']))
{
	$cod_cliente = $_GET['cod_cliente'];
	$sql = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro` FROM `clientes` WHERE codigo = '$cod_cliente'";
	$result=mysqli_query($conexion,$sql);
	$ver=mysqli_fetch_row($result);

	$cliente = array(
		'codigo' => $ver[0], 
		'id' => $ver[1], 
		'nombre' => $ver[2], 
		'telefono' => $ver[3], 
		'direccion' => $ver[4]
	);
}
else
{
	$sql_espacio = "SELECT `codigo`, `nombre`, `items`, `fecha_creacion`, `cod_cliente`, `pagos`, `informacion`, `caja` FROM `espacios` WHERE codigo = '$cod_espacio'";
	$result_espacio=mysqli_query($conexion,$sql_espacio);
	$mostrar_espacio=mysqli_fetch_row($result_espacio);

	$cod_cliente = $mostrar_espacio[4];

	if($cod_cliente != '')
	{
		$sql = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro` FROM `clientes` WHERE codigo = '$cod_cliente'";
		$result=mysqli_query($conexion,$sql);
		$ver=mysqli_fetch_row($result);

		$cliente = array(
			'codigo' => $ver[0], 
			'id' => $ver[1], 
			'nombre' => $ver[2], 
			'telefono' => $ver[3], 
			'direccion' => $ver[4]
		);
	}
}

?>
<div class="text-center row h5">
	<h4 class="col mt-2">Datos de cliente</h4>
	<div class="btn-float-right">
		<button class="btn btn-sm btn-outline-primary btn-round px-2" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_cliente').load('paginas/detalles/agregar_cliente.php/?cod_espacio=<?php echo $cod_espacio ?>', function(){cerrar_loader();});">
			<span class="fa fa-plus"></span>
		</button>
	</div>

</div>
<div class="row m-0 border-top pt-3 border-bottom border-2">
	<input type="text" name="cod_cliente" id="cod_cliente" hidden="" value="<?php echo $cod_cliente ?>">
	<p class="row m-0 pl-0">
		<span class="col-4 col-sm-4 col-md-6 col-lg-5 d-flex justify-content-end pr-0">
			<?php 
			if($cod_cliente != '')
			{ 
				?>
				<a class="p-0 text-primary" href="javascript:seleccionar_cliente('')">
					<span class="fa fa-times text-danger f-16"></span>
				</a> 
				<?php 
			}
			?>
			Cédula/NIT: 
		</span>
		<?php 
		if($cod_cliente != '')
		{ 
			?>
			<span class="col-8 col-sm-8 col-md-6 col-lg-7 text-left px-1"><b class="text-truncate w-100" id="b_id_cliente"><?php echo $cliente['id'] ?></b></span>
			<?php 
		}
		else
		{
			?>
			<span class="col-8 col-sm-8 col-md-6 col-lg-7 text-left px-1"><input type="text" class="form-control form-control-sm" name="input_cc_cliente" id="input_cc_cliente" placeholder="Busqueda por C.C/NIT" onkeydown="if(event.key=== 'Enter'){buscar_x_cc(this.value,'<?php echo $cod_espacio ?>')}"></span>
			<?php 
		}
		?>
	</p>
	<p class="row m-0 pl-0">
		<span class="col-4 col-sm-4 col-md-6 col-lg-5 d-flex justify-content-end pr-0"> Nombre: </span>
		<span class="col-8 col-sm-8 col-md-6 col-lg-7 text-left px-1"><b class="text-truncate w-100" id="b_nombre_cliente"><?php echo $cliente['nombre'] ?></b></span>
	</p>
	<p class="row m-0 pl-0">
		<span class="col-4 col-sm-4 col-md-6 col-lg-5 d-flex justify-content-end pr-0"> 
		Teléfono: </span>
		<span class="col-8 col-sm-8 col-md-6 col-lg-7 text-left px-1"><b class="text-truncate w-100" id="b_telefono_cliente"><?php echo $cliente['telefono'] ?></b></span>
	</p>
</div>
<div class="form-group my-2">
	<div class="input-group">
		<input type="text" class="form-control form-control-sm col" name="input_busqueda" id="input_busqueda" placeholder="Cédula/NIT - Nombre - Teléfono" autocomplete="off">
		<button class="btn btn-sm btn-outline-primary btn-round" id="btn_buscar_cliente"><span class="fas fa-search"></span></button>
	</div>
</div>
<div id="tabla_busqueda_cliente_servicio"></div>

<script type="text/javascript">

	$('#input_busqueda').keypress(function(e){
		if(e.keyCode==13)
			$('#btn_buscar_cliente').click();
	});

	$('#btn_buscar_cliente').click(function()
	{
		document.getElementById('div_loader').style.display = 'block';
		input_busqueda = document.getElementById("input_busqueda").value;
		input_busqueda = input_busqueda.replace(/ /g, "***");
		if(input_busqueda != '' && input_busqueda.length>2)
			$('#tabla_busqueda_cliente_servicio').load('tablas/tabla_busqueda_cliente_servicio.php/?page=1&input_buscar='+input_busqueda, function(){cerrar_loader();});
		else
		{
			w_alert({ titulo: 'Ingrese al menos 3 caracteres', tipo: 'danger' });
			document.getElementById("input_busqueda").focus();
		}
		cerrar_loader();
	});
</script>