<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();
session_set_cookie_params(7*24*60*60);
session_start();

$cod_cliente = '';
$cod_recurso = '';

if (isset($_SESSION['carrito_productos']))
	$carrito_productos = $_SESSION['carrito_productos'];
else
	$carrito_productos = array();

$sql_usuarios = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `foto`, `telefono`, `rol`, `fecha_registro`, `estado`, `color` FROM `usuarios` WHERE codigo != 1 AND estado = 'ACTIVO'";
$result_usuarios=mysqli_query($conexion,$sql_usuarios);

$sql_clientes= "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `telefono` FROM `clientes` ORDER BY `nombre` ASC";
$result_clientes=mysqli_query($conexion,$sql_clientes);

?>
<div class="modal-header p-2">
	<h5 class="mb-0">Nueva venta de productos</h5>
	<button type="button" class="btn-close float-end mr-0" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body p-1 mx-0">
	<div class="form-group form-group-sm">
		<div class="row">
			<label for="cliente" class="col-sm-4 col-md-2 col-lg-2"><span class="requerido">*</span>Cliente: </label>
			<div class="col-sm-8 col-md-10 col-lg-10">
				<select class="form-control form-control-sm select2" id="cliente" name="cliente">
					<option value="">Seleccionar Cliente</option>
					<?php 
					if(isset($_SESSION['cliente_carrito']))
						$cod_cliente = $_SESSION['cliente_carrito']['codigo'];
					while ($mostrar_clientes=mysqli_fetch_row($result_clientes))
					{
						$cedula = $mostrar_clientes[1];
						$nombre_cliente = $mostrar_clientes[2].' '.$mostrar_clientes[3];
						$telefono = $mostrar_clientes[4];
						if($cod_cliente == $mostrar_clientes[0])
							$selecionado = 'selected';
						else
							$selecionado = '';

						$cliente = $cedula.' - '.$nombre_cliente.' ('.$telefono.')';
						?>
						<option value="<?php echo $mostrar_clientes[0] ?>" <?php echo $selecionado ?>><?php echo $cliente ?></option>
						<?php 
					}
					?>
				</select>
			</div>
		</div>
	</div>
	<div class="form-group form-group-sm">
		<div class="row">
			<label for="recurso" class="col-sm-4 col-md-2 col-lg-2"><span class="requerido">*</span>Recurso: </label>
			<div class="col-sm-8 col-md-10 col-lg-10">
				<select class="form-control form-control-sm select2" id="recurso" name="recurso">
					<option value="">Seleccionar Responsable</option>
					<?php 
					if(isset($_SESSION['recurso_carrito']))
						$cod_recurso = $_SESSION['recurso_carrito']['codigo'];
					while ($mostrar_usuarios=mysqli_fetch_row($result_usuarios))
					{
						$nombre_usuario = $mostrar_usuarios[2].' '.$mostrar_usuarios[3];
						if($cod_recurso == $mostrar_usuarios[0])
							$selecionado = 'selected';
						else
							$selecionado = '';
						?>
						<option value="<?php echo $mostrar_usuarios[0] ?>" <?php echo $selecionado ?>><?php echo $nombre_usuario ?></option>
						<?php 
					}
					?>
				</select>
			</div>
		</div>
	</div>
	<div class="text-center">
		<h4>Productos para venta</h4>
	</div>
	<div class="table-responsive text-dark text-center py-0 px-1">
		<table width="100%" class="table text-dark table-sm" id="tabla_insumos">
			<thead>
				<tr class="text-center">
					<th class="p-1 px-4">#</th>
					<th class="p-1">Cod</th>
					<th class="p-1">Descripción</th>
					<th class="p-1">Fecha Vencimiento</th>
					<th class="p-1">Valor Unitario</th>
					<th class="p-1">cant</th>
					<th class="p-1">Total</th>
					<th class="p-1"></th>
				</tr>
			</thead>
			<tbody class="overflow-auto">
				<?php 
				$total_a_pagar = 0;
				foreach ($carrito_productos as $i => $item)
				{
					$cod_producto = $item['codigo'];
					$num_inventario = $item['num_inventario'];
					$descripcion = $item['descripcion'];
					$valor = $item['valor'];
					$cant = $item['cant'];
					$fecha_ven = $item['fecha_ven'];

					$total_producto = $valor * $cant;

					$total_a_pagar += $total_producto;
					?>
					<tr role="row" class="odd">
						<td class="p-1 py-0 text-center text-dark"><?php echo $i ?></td>
						<td class="p-1 py-0 text-center text-dark"><?php echo $cod_producto.'-'.$num_inventario ?></td>
						<td class="p-1 py-0 text-truncate text-dark"><?php echo $descripcion ?></td>
						<td class="p-1 py-0 text-truncate text-dark"><?php echo $fecha_ven ?></td>
						<td class="p-1 py-0 text-right text-dark"><strong>$<?php echo number_format($valor,0,'.','.')?></strong></td>
						<td class="p-1 py-0 text-truncate text-dark h3"><?php echo $cant ?></td>
						<td class="p-1 py-0 text-right text-dark"><strong>$<?php echo number_format($total_producto,0,'.','.')?></strong></td>
						<td class="p-1 py-0 text-center text-dark">
							<button class="btn btn-sm btn-outline-danger btn-round p-1" onclick="rem_producto_carrito('<?php echo $i ?>')">
								<i class="material-icons-two-tone">clear</i>
							</button>
						</td>
					</tr>
					<?php 
				} 
				?>
				<tr role="row" class="odd bg-primary">
					<td class="p-1 text-right text-truncate text-dark h4" colspan="6">Total a pagar:</td>
					<td class="p-1 text-right text-dark h4"><strong>$<?php echo number_format($total_a_pagar,0,'.','.')?></strong></td>
					<td class="p-1"></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="modal-footer p-1">
	<div class="col text-left">
		<button type="button" class="btn btn-sm btn-secondary btn-round" data-bs-dismiss="modal" id="close_Modal_Carrito">Cerrar</button>
	</div>
	<div class="col text-right">
		<button class="btn btn-sm btn-outline-primary btn-round" id="btn_procesar_venta">Procesar Venta</button>
	</div>
</div>

<script type="text/javascript">
	
	//$('.select2').select2();

</script>