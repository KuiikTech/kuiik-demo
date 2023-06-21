<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
session_set_cookie_params(7*24*60*60);
session_start();

if (isset($_SESSION['lista_servicios_bono']))
	$lista_servicios_bono = $_SESSION['lista_servicios_bono'];
else
	$lista_servicios_bono = array();

$total_servicios = 0;

?>
<div class="table-responsive text-dark text-center py-0 px-1">
	<table width="100%" class="table text-dark table-sm mb-1" id="tabla_insumos">
		<thead>
			<tr class="text-center">
				<th class="p-1" width="20px">Cod</th>
				<th class="p-1">Categoría</th>
				<th class="p-1">Descripción</th>
				<th class="p-1" width="100px">Valor</th>
				<th class="p-1"></th>
			</tr>
		</thead>
		<tbody class="overflow-auto  text-dark">
			<?php 
			foreach ($lista_servicios_bono as $i => $servicio)
			{
				$codigo = $servicio['codigo'];
				$categoria = $servicio['categoria'];
				$descripcion = $servicio['descripcion'];
				$valor = $servicio['valor'];

				$sql = "SELECT `cod_categoria`, `nombre` FROM `categorias` WHERE cod_categoria = '$categoria'";
				$result=mysqli_query($conexion,$sql);
				$ver=mysqli_fetch_row($result);

				$categoria = ucwords(mb_strtolower($ver[1]));

				$total_servicios += $valor;

				?>
				<tr role="row" class="odd <?php echo $error ?>" >
					<td class="text-center p-0"><?php echo str_pad($codigo,3,"0",STR_PAD_LEFT) ?></td>
					<td class="text-center p-0"><?php echo $categoria ?></td>
					<td class="text-left p-0"><b><?php echo $descripcion ?></b></td>
					<td class="text-right p-0"><b>$<?php echo number_format($valor,0,'.','.')?></b></td>
					<td class="text-center p-0">
						<button type="button" class="btn btn-sm btn-outline-danger btn-round p-0" onclick="eliminar_servicio_bono('<?php echo $i ?>')">
							<i class="material-icons-two-tone">clear</i>
						</button>
					</td>
				</tr>
				<?php 
			} 
			?>

			<tr role="row" class="odd bg-primary">
				<td class="text-right p-0" colspan="3"><b>TOTAL</b></td>
				<td class="text-right p-0"><b>$<?php echo number_format($total_servicios,0,'.','.')?></b></td>
				<td class="text-center p-0"></td>
			</tr>

		</tbody>
	</table>
</div>

<script type="text/javascript">

	$('input.moneda').keyup(function(event)
	{
		if(event.which >= 37 && event.which <= 40)
		{
			event.preventDefault();
		}
		$(this).val(function(index, value)
		{
			return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
		});
	});
	
</script>
