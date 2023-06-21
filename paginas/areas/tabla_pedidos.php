<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();

$sql_caja = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `egresos`, `creador`, `cajero`, `finalizador`, `estado`, `info`, `kilos_inicio`, `kilos_fin` FROM `caja` WHERE estado = 'ABIERTA'";
$result_caja=mysqli_query($conexion,$sql_caja);
$mostrar_caja=mysqli_fetch_row($result_caja);

if($mostrar_caja != null){
$fecha_inicio = $mostrar_caja[2];

$area = $_GET['area'];

$sql = "SELECT `codigo`, `producto`, `cantidad`, `valor`, `mesa`, `solicitante`, `fecha_registro`, `fecha_entrega`, `estado`, `area` FROM `pedidos` WHERE fecha_registro> '$fecha_inicio' AND area = '$area' AND estado != 'DESPACHADO' AND estado != 'CANCELADO' AND estado != 'EN ESPERA' ORDER BY FIELD(estado,'PREPARANDO','PENDIENTE'), fecha_registro ASC";
$result=mysqli_query($conexion,$sql);

?>
<!-- Tabla Pedidos -->
<div class="card">
	<div class="card-body">
		<div class="d-sm-flex align-items-center mb-4">
			<h4 class="card-title text-center">Pedidos <?php echo $area; ?></h4>
		</div>
		<table class="table table-sm table-striped Data_Table" id="tabla_productos">
			<thead>
				<tr>
					<!--<th class="text-center"><strong>Codigo</strong></th>-->
					<th><strong>Mesa</strong></th>
					<th><strong>Producto</strong></th>
					<th class="text-center"><strong>Cant</strong></th>
					<th class="text-center"><strong>Valor Unitario</strong></th>
					<th class="text-center"><strong>Solicitante</strong></th>
					<th class="text-center"><strong>Estado</strong></th>
					<th class="text-center"></th>
				</tr>
			</thead>
			<tbody class="overflow-auto">
				<?php 
				$pos = 1;
				$reservas = array();
				while ($mostrar=mysqli_fetch_row($result)) 
				{ 
					$cod_pedido = $mostrar[0];
					$cod_mesa = $mostrar[4];
					$sql_mesa="SELECT `cod_mesa`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_apertura` FROM `mesas` WHERE cod_mesa='$cod_mesa'";
					$result_mesa=mysqli_query($conexion,$sql_mesa);
					$ver_mesa=mysqli_fetch_row($result_mesa);

					$nombre_mesa = $ver_mesa[1];

					$cod_producto = $mostrar[1];
					$cant = $mostrar[2];

					$sql_producto = "SELECT `cod_producto`, `descripción`, `unidad`, `valor`, `inventario`, `cod_categoria`, `imagen`, `fecha_modificacion`, `tipo`, `area` FROM `productos` WHERE cod_producto='$cod_producto'";
					$result_producto=mysqli_query($conexion,$sql_producto);
					$mostrar_producto=mysqli_fetch_row($result_producto);

					$descripcion = $mostrar_producto[1];
					$valor_unitario = $mostrar[3];

					$solicitante = $mostrar[5];

					$sql_e = "SELECT nombre, rol, foto FROM usuarios WHERE codigo = '$solicitante'";
					$result_e=mysqli_query($conexion,$sql_e);
					$ver_e=mysqli_fetch_row($result_e);

					$solicitante = $ver_e[0];

					$estado = $mostrar[8];

					$color_text = '';
					$reserva = 'NO';

					if($estado == 'PENDIENTE')
					{
						$bg_tr = 'bg_pendiente';
						$color_text = 'text-danger';

						$nombre_mesa_low = strtolower($nombre_mesa);
						if (strpos($nombre_mesa_low, "reserva") !== false)
						{
							$reserva = 'SI';

							$reservas[$pos]['cod_pedido'] = $cod_pedido;
							$reservas[$pos]['nombre_mesa'] = $nombre_mesa;
							$reservas[$pos]['descripcion'] = $descripcion;
							$reservas[$pos]['cant'] = $cant;
							$reservas[$pos]['valor_unitario'] = $valor_unitario;
							$reservas[$pos]['solicitante'] = $solicitante;
							$reservas[$pos]['estado'] = $estado;
							$pos++;
						}
					}
					
					if($estado == 'PREPARANDO')
						$bg_tr = '';
					if($estado == 'DESPACHADO')
						$bg_tr = 'bg_despachado';
					if($estado == 'CANCELADO')
						$bg_tr = 'bg_cancelado';

					if($reserva == 'NO')
					{
						?>
						<tr class="<?php echo $bg_tr ?>">
							<!--<td class="text-center"><?php echo str_pad($mostrar[0],3,"0",STR_PAD_LEFT) ?></td>-->
							<td><?php echo $nombre_mesa ?></td>
							<td><?php echo $descripcion ?></td>
							<td class="text-center"><b><?php echo $cant ?></b></td>
							<td class="text-right"><b><?php echo '$ '.number_format($valor_unitario,0,'.','.'); ?></b></td>
							<td><?php echo $solicitante ?></td>
							<td class="text-center <?php echo $color_text ?>"><b><?php echo $estado ?></b></td>
							<td class="text-center p-1">
								<?php 
								if($estado == 'PENDIENTE' || $estado == 'PREPARANDO')
								{
									if($estado == 'PENDIENTE')
									{
										?>
										<button class="btn btn-info btn-rounded btn-icon ml-1" onclick="preparando_pedido('<?php echo $cod_mesa ?>','<?php echo $cod_pedido ?>','<?php echo $area ?>')">
											P
										</button>
										<?php 
									}
									if($estado == 'PREPARANDO')
									{
										?>
										<button class="btn btn-success btn-rounded btn-icon ml-1" onclick="pedido_despachado('<?php echo $cod_mesa ?>','<?php echo $cod_pedido ?>','<?php echo $area ?>')">
											D
										</button>
										<?php 
									}
								}
								?>
							</td>
						</tr>
						<?php 
					}
				} 

				foreach ($reservas as $i => $reserva)
				{
					$cod_pedido = $reserva['cod_pedido'];
					$nombre_mesa = $reserva['nombre_mesa'];
					$descripcion = $reserva['descripcion'];
					$cant = $reserva['cant'];
					$valor_unitario = $reserva['valor_unitario'];
					$solicitante = $reserva['solicitante'];
					$estado = $reserva['estado'];
					?>
					<tr class="bg-warning">
						<!--<td class="text-center"><?php echo str_pad($cod_pedido,3,"0",STR_PAD_LEFT) ?></td>-->
						<td class="text-danger"><b><?php echo $nombre_mesa ?></b></td>
						<td><?php echo $descripcion ?></td>
						<td class="text-center"><b><?php echo $cant ?></b></td>
						<td class="text-right"><b><?php echo '$ '.number_format($valor_unitario,0,'.','.'); ?></b></td>
						<td><?php echo $solicitante ?></td>
						<td class="text-center <?php echo $color_text ?>"><b><?php echo $estado ?></b></td>
						<td class="text-center p-1">
							<?php 
							if($estado == 'PENDIENTE' || $estado == 'PREPARANDO')
							{
								if($estado == 'PENDIENTE')
								{
									?>
									<button class="btn btn-info btn-rounded btn-icon ml-1" onclick="preparando_pedido('<?php echo $cod_mesa ?>','<?php echo $cod_pedido ?>','<?php echo $area ?>')">
										P
									</button>
									<?php 
								}
								if($estado == 'PREPARANDO')
								{
									?>
									<button class="btn btn-success btn-rounded btn-icon ml-1" onclick="pedido_despachado('<?php echo $cod_mesa ?>','<?php echo $cod_pedido ?>','<?php echo $area ?>')">
										D
									</button>
									<?php 
								}
							}
							?>
						</td>
					</tr>
					<?php 
				}
				
				$sql = "SELECT `codigo`, `producto`, `cantidad`, `valor`, `mesa`, `solicitante`, `fecha_registro`, `fecha_entrega`, `estado`, `area` FROM `pedidos` WHERE fecha_registro> '$fecha_inicio' AND area = '$area' AND (estado = 'DESPACHADO' OR estado = 'CANCELADO') ORDER BY FIELD(estado,'PREPARANDO','PENDIENTE','DESPACHADO','CANCELADO'), fecha_entrega DESC";
				$result=mysqli_query($conexion,$sql);
				while ($mostrar=mysqli_fetch_row($result)) 
				{ 
					$cod_pedido = $mostrar[0];
					$cod_mesa = $mostrar[4];
					$sql_mesa="SELECT `cod_mesa`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_apertura` FROM `mesas` WHERE cod_mesa='$cod_mesa'";
					$result_mesa=mysqli_query($conexion,$sql_mesa);
					$ver_mesa=mysqli_fetch_row($result_mesa);

					$nombre_mesa = $ver_mesa[1];

					$cod_producto = $mostrar[1];
					$cant = $mostrar[2];

					$sql_producto = "SELECT `cod_producto`, `descripción`, `unidad`, `valor`, `inventario`, `cod_categoria`, `imagen`, `fecha_modificacion`, `tipo`, `area` FROM `productos` WHERE cod_producto='$cod_producto'";
					$result_producto=mysqli_query($conexion,$sql_producto);
					$mostrar_producto=mysqli_fetch_row($result_producto);

					$descripcion = $mostrar_producto[1];
					$valor_unitario = $mostrar[3];

					$solicitante = $mostrar[5];

					$sql_e = "SELECT nombre, rol, foto FROM usuarios WHERE codigo = '$solicitante'";
					$result_e=mysqli_query($conexion,$sql_e);
					$ver_e=mysqli_fetch_row($result_e);

					$solicitante = $ver_e[0];

					$estado = $mostrar[8];

					$color_text = '';
					$reserva = 'NO';

					if($estado == 'PENDIENTE')
					{
						$bg_tr = 'bg_pendiente';
						$color_text = 'text-danger';
					}
					
					if($estado == 'DESPACHADO')
						$bg_tr = 'bg_despachado';
					if($estado == 'CANCELADO')
						$bg_tr = 'bg_cancelado';

					?>
					<tr class="<?php echo $bg_tr ?>">
						<!--<td class="text-center"><?php echo str_pad($mostrar[0],3,"0",STR_PAD_LEFT) ?></td>-->
						<td><?php echo $nombre_mesa ?></td>
						<td><?php echo $descripcion ?></td>
						<td class="text-center"><b><?php echo $cant ?></b></td>
						<td class="text-right"><b><?php echo '$ '.number_format($valor_unitario,0,'.','.'); ?></b></td>
						<td><?php echo $solicitante ?></td>
						<td class="text-center <?php echo $color_text ?>"><b><?php echo $estado ?></b></td>
						<td class="text-center p-1">
							<?php 
							if($estado == 'PENDIENTE' || $estado == 'PREPARANDO')
							{
								if($estado == 'PENDIENTE')
								{
									?>
									<button class="btn btn-info btn-rounded btn-icon ml-1" onclick="preparando_pedido('<?php echo $cod_mesa ?>','<?php echo $cod_pedido ?>','<?php echo $area ?>')">
										P
									</button>
									<?php 
								}
								if($estado == 'PREPARANDO')
								{
									?>
									<button class="btn btn-success btn-rounded btn-icon ml-1" onclick="pedido_despachado('<?php echo $cod_mesa ?>','<?php echo $cod_pedido ?>','<?php echo $area ?>')">
										D
									</button>
									<?php 
								}
							}
							?>
						</td>
					</tr>
					<?php 
				} 
				?>
			</tbody>
		</table>
	</div>
</div>
<!-- #END# Tabla Productos -->
<script type="text/javascript">

	function pedido_termiado(cod_mesa,cod_pedido,area)
	{
		$.ajax({
			type:"POST",
			data:"cod_pedido="+cod_pedido+"&cod_mesa="+cod_mesa,
			url:"../../procesos/pedido_terminado.php",
			success:function(r)
			{
				datos=jQuery.parseJSON(r);
				if (datos['consulta'] == 1)
				{
					$.notify({ title: '<strong>EXITO: </strong>',message: 'Pedido teminado'},{ type: 'success', placement: {from: "top",align: "center"}});
					$('#div_contenido').load('tabla_pedidos.php/?area='+area);
				}
				else
					$.notify({ title: '<strong>ERROR: </strong>',message: datos['consulta']},{ type: 'danger', placement: {from: "top",align: "center"}});
			}
		});
	}

	function pedido_despachado(cod_mesa,cod_pedido,area)
	{
		$.ajax({
			type:"POST",
			data:"cod_pedido="+cod_pedido+"&cod_mesa="+cod_mesa,
			url:"../../procesos/despachar_pedido.php",
			success:function(r)
			{
				datos=jQuery.parseJSON(r);
				if (datos['consulta'] == 1)
				{
					$.notify({ title: '<strong>EXITO: </strong>',message: 'Pedido despachado'},{ type: 'success', placement: {from: "top",align: "center"}});
					$('#div_contenido').load('tabla_pedidos.php/?area='+area);
				}
				else
					$.notify({ title: '<strong>ERROR: </strong>',message: datos['consulta']},{ type: 'danger', placement: {from: "top",align: "center"}});
			}
		});
	}

	function preparando_pedido(cod_mesa,cod_pedido,area)
	{
		$.ajax({
			type:"POST",
			data:"cod_pedido="+cod_pedido+"&cod_mesa="+cod_mesa,
			url:"../../procesos/preparar_pedido.php",
			success:function(r)
			{
				datos=jQuery.parseJSON(r);
				if (datos['consulta'] == 1)
				{
					$.notify({ title: '<strong>EXITO: </strong>',message: 'Preparando pedido'},{ type: 'success', placement: {from: "top",align: "center"}});
					$('#div_contenido').load('tabla_pedidos.php/?area='+area);
				}
				else
					$.notify({ title: '<strong>ERROR: </strong>',message: datos['consulta']},{ type: 'danger', placement: {from: "top",align: "center"}});
			}
		});
	}

	function pedido_cancelado(cod_mesa,cod_pedido,area)
	{
		$.ajax({
			type:"POST",
			data:"cod_pedido="+cod_pedido+"&cod_mesa="+cod_mesa,
			url:"../../procesos/cancelar_pedido.php",
			success:function(r)
			{
				datos=jQuery.parseJSON(r);
				if (datos['consulta'] == 1)
				{
					$.notify({ title: '<strong>EXITO: </strong>',message: 'Pedido cancelado'},{ type: 'success', placement: {from: "top",align: "center"}});
					$('#div_contenido').load('tabla_pedidos.php/?area='+area);
				}
				else
					$.notify({ title: '<strong>ERROR: </strong>',message: datos['consulta']},{ type: 'danger', placement: {from: "top",align: "center"}});
			}
		});
	}


</script>

<?php 
}
else
{
?>
<div class="row m-0 p-2">
    <div class="bg-warning p-2 rounded-pill text-center">
      <h4 class="text-white mb-0">La caja no se encuenta ABIERTA</h4>
    </div>
  </div>
<?php 
}
?>