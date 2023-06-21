<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();

$codigo = $_GET['cod_producto'];
$pos = $_GET['pos'];

$bodega = $_GET['bodega'];

$sql = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo = '$codigo'";
$result=mysqli_query($conexion,$sql);
$mostrar=mysqli_fetch_row($result);

$inventarios = array();
if($bodega == 'Principal')
{
	if ($mostrar[3] != '')
		$inventarios = json_decode($mostrar[3],true);
}
else if($bodega == 'PDV_1')
{
	if ($mostrar[6] != '')
		$inventarios = json_decode($mostrar[6],true);
}
else if($bodega == 'PDV_2')
{
	if ($mostrar[7] != '')
		$inventarios = json_decode($mostrar[7],true);
}

$producto = $inventarios[$pos];

$costo = $producto['costo'];
$valor_venta = $producto['valor_venta'];
$valor_venta_mayor = $producto['valor_venta_mayor'];
$creador = $producto['creador'];
$cant_inicial = $producto['cant_inicial'];
$stock = $producto['stock'];

$marca = '';
$proveedor = '';

if(isset($producto['marca']))
	$marca = $producto['marca'];

if(isset($producto['proveedor']))
	$proveedor = $producto['proveedor'];

?>
<div class="table-responsive text-dark text-center p-0">
	<table class="table text-dark table-sm w-100 " id="tabla_inventario_<?php echo $codigo ?>">
		<thead>
			<tr class="text-center">
				<th colspan="5" class=" bg-warning">EDITAR INVENTARIO DE PRODUCTO</th>
			</tr>
			<tr class="text-center">
				<th class="p-1">Proveedor/Marca</th>
				<th class="p-1">Valor Venta Público</th>
				<th class="p-1">Valor Venta Mayor</th>
				<th class="p-1">Costo</th>
				<th class="p-1"></th>
			</tr>
		</thead>
		<tbody class="overflow-auto text-dark px-1">
			<tr>
				<td class="p-1">
					<input type="text" class="form-control form-control-sm" name="edit_proveedor" id="edit_proveedor" placeholder="Proveedor" autocomplete="off" value="<?php echo $proveedor ?>">
					<input type="text" class="form-control form-control-sm" name="edit_marca" id="edit_marca" placeholder="Marca" autocomplete="off" value="<?php echo $marca ?>">
				</td>
				<td class="p-1">
					<input type="text" class="form-control form-control-sm moneda text-right" name="edit_valor_venta" id="edit_valor_venta" placeholder="Valor venta" autocomplete="off" value="<?php echo $valor_venta ?>">
				</td>
				<td class="p-1">
					<input type="text" class="form-control form-control-sm moneda text-right" name="edit_valor_venta_mayor" id="edit_valor_venta_mayor" placeholder="Valor venta mayor" autocomplete="off" value="<?php echo $valor_venta_mayor ?>">
				</td>
				<td class="p-1">
					<input type="text" class="form-control form-control-sm moneda text-right" name="edit_costo" id="edit_costo" placeholder="Costo" autocomplete="off" value="<?php echo $costo ?>">
				</td>
				<td class="p-1">
					<button class="btn btn-outline-primary btn-round p-1" id="btn_editar_stock">
						<span class="fa fa-save"></span> Guardar
					</button>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<script type="text/javascript">
	
	$('#btn_editar_stock').click(function()
	{
		document.getElementById('div_loader').style.display = 'block';
		document.getElementById("btn_editar_stock").disabled = true;
		edit_valor_venta = document.getElementById("edit_valor_venta").value;
		edit_valor_venta_mayor = document.getElementById("edit_valor_venta_mayor").value;
		edit_costo = document.getElementById("edit_costo").value;
		edit_marca = document.getElementById("edit_marca").value;
		edit_proveedor = document.getElementById("edit_proveedor").value;
		if(edit_valor_venta != '' && edit_valor_venta_mayor != '' && edit_costo != '' && edit_proveedor != '' && edit_marca != '')
		{
			$.ajax({	
				type:"POST",
				data:"cod_producto=<?php echo $codigo ?>&pos=<?php echo $pos ?>&bodega=<?php echo $bodega ?>&edit_valor_venta=" + edit_valor_venta+"&edit_valor_venta_mayor=" + edit_valor_venta_mayor+"&edit_costo=" + edit_costo+"&edit_proveedor=" + edit_proveedor+"&edit_marca=" + edit_marca,
				url:"procesos/editar_stock_producto.php",
				success:function(r)
				{
					datos=jQuery.parseJSON(r);
					if(datos['consulta'] == 1)
					{
						w_alert({ titulo: 'Stock editado correctamente', tipo: 'success' });
						$("#Modal_Edit_Stock").modal('toggle');
						$('#div_modal_producto').load('paginas/detalles/detalles_producto.php/?cod_producto=<?php echo $codigo ?>&bodega=<?php echo $bodega ?>', function(){cerrar_loader();});
						$("#Modal_Ver").modal('show');

					}
					else
						w_alert({ titulo: datos['consulta'], tipo: 'danger' });
					if(datos['consulta'] == 'Reload')
					{
						document.getElementById('div_login').style.display = 'block';
cerrar_loader();
						
					}
				}
			});
		}
		else
		{
			if(edit_valor_venta == '')
			{
				w_alert({ titulo: 'Ingrese el valor de venta público del producto', tipo: 'danger' });
				document.getElementById("edit_valor_venta").focus();
			}
			else if(edit_valor_venta_mayor == '')
			{
				w_alert({ titulo: 'Ingrese el valor de venta mayor del producto', tipo: 'danger' });
				document.getElementById("edit_valor_venta_mayor").focus();
			}
			else if(edit_costo == '')
			{
				w_alert({ titulo: 'Ingrese el costo del producto', tipo: 'danger' });
				document.getElementById("edit_costo").focus();
			}
		}

		cerrar_loader();
		document.getElementById("btn_editar_stock").disabled = false;
	});

</script>