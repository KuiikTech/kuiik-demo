<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();


$cod_caja=$_GET['cod_caja'];

$inventario= '';
$ventas = '';

$base = 0;
$total_preparaciones = 0;
$total_productos = 0;
$total_ventas = 0;
$total_efectivo = 0;
$total_transferencias = 0;
$total_creditos = 0;
$total_gastos = 0;
$resultado = 0;
$total_descuentos = 0;
$total_otros = 0;

$fecha_apertura = '---';
$fecha_cierre = '---';

$cajero = '---';
$finalizador = '---';

$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `finalizador` FROM `caja` WHERE codigo = '$cod_caja'";
$result=mysqli_query($conexion,$sql);
$mostrar=mysqli_fetch_row($result);

$estado = $mostrar[12];

$fecha_registro = strftime("%A, %e %b %Y", strtotime($mostrar[1]));
$fecha_registro = ucfirst(iconv("ISO-8859-1","UTF-8",$fecha_registro));
$fecha_registro .= date(' | h:i A',strtotime($mostrar[1]));

$base = $mostrar[8];
$inventario = json_decode($mostrar[4],true);

$creador = $mostrar[10];

$sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
$result_e=mysqli_query($conexion,$sql_e);
$ver_e=mysqli_fetch_row($result_e);

$creador = $ver_e[0];

if($estado == 'CREADA')
{
	$sql_ventas = "SELECT `codigo`, `cliente`, `productos`, `pago`, `fecha`, `cobrador` FROM `ventas` WHERE fecha > '$mostrar[1]' order by fecha ASC";
	$sql_gastos = "SELECT `codigo`, `descripcion`, `valor`, `fecha_registro` FROM `gastos` WHERE fecha_registro > '$mostrar[1]' order by fecha_registro ASC";
}

if($estado == 'ABIERTA' || $estado == 'CERRADA')
{
	$cajero = $mostrar[11];
	if($estado == 'CERRADA')
		$fecha_cierre = $mostrar[3];
	else
		$fecha_cierre = date('Y-m-d G:i:s');

	$sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$cajero'";
	$result_e=mysqli_query($conexion,$sql_e);
	$ver_e=mysqli_fetch_row($result_e);

	$cajero = $ver_e[0];

	$fecha_apertura = $mostrar[2];

	$sql_gastos = "SELECT SUM(`valor`) FROM `gastos` WHERE fecha_registro BETWEEN '$fecha_apertura' AND '$fecha_cierre'";
	$result_gastos=mysqli_query($conexion,$sql_gastos);
	$mostrar_gastos=mysqli_fetch_row($result_gastos);

	$total_gastos = $mostrar_gastos[0];

	$sql_gastos = "SELECT `codigo`, `descripcion`, `valor`, `fecha_registro` FROM `gastos` WHERE fecha_registro BETWEEN '$fecha_apertura' AND '$fecha_cierre' order by fecha_registro ASC";

	$sql_cuentas = "SELECT SUM(`valor`) FROM `cuentas_por_cobrar` WHERE fecha_ingreso BETWEEN '$fecha_apertura' AND '$fecha_cierre'";
	$result_cuentas=mysqli_query($conexion,$sql_cuentas);
	$mostrar_cuentas=mysqli_fetch_row($result_cuentas);

	$total_otros += $mostrar_cuentas[0];

	$sql_ventas = "SELECT `codigo`, `cliente`, `productos`, `pago`, `fecha`, `cobrador` FROM `ventas` WHERE fecha BETWEEN '$fecha_apertura' AND '$fecha_cierre'";
	$result_ventas=mysqli_query($conexion,$sql_ventas);

	while ($mostrar_ventas=mysqli_fetch_row($result_ventas)) 
	{
		$estado_venta = $mostrar_ventas[6];
		if($estado_venta != 'ANULADA')
		{
			$productos_venta = json_decode($mostrar_ventas[2],true);

			foreach ($productos_venta as $i => $producto)
				$total_ventas += $producto['valor_unitario']*$producto['cant'];

			$pagos_venta = json_decode($mostrar_ventas[3],true);
			foreach ($pagos_venta as $i => $pago)
			{
				if($pago['tipo'] == 'Efectivo')
					$total_efectivo += $pago['valor'];

				if($pago['tipo'] == 'Transferencia')
					$total_transferencias += $pago['valor'];

				if($pago['tipo'] == 'Crédito')
					$total_creditos += $pago['valor'];

				if($pago['tipo'] == 'Descuentos')
					$total_descuentos += $pago['valor'];
			}
		}
	}

	foreach ($inventario as $i => $producto)
	{
		if($producto['inventario_final'] != null)
			$total_productos += $producto['valor'] * ($producto['inventario_recibido'] - $producto['inventario_final']);
	}

	$cajero = $mostrar[11];
	$sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$cajero'";
	$result_e=mysqli_query($conexion,$sql_e);
	$ver_e=mysqli_fetch_row($result_e);

	$cajero = $ver_e[0];

	$fecha_apertura = strftime("%A, %e %b %Y", strtotime($mostrar[2]));
	$fecha_apertura = ucfirst(iconv("ISO-8859-1","UTF-8",$fecha_apertura));
	$fecha_apertura .= date(' | h:i A',strtotime($mostrar[2]));

}

if($estado == 'CERRADA')
{
	$fecha_cierre = $mostrar[3];

	$fecha_cierre = strftime("%A, %e %b %Y", strtotime($mostrar[3]));
	$fecha_cierre = ucfirst(iconv("ISO-8859-1","UTF-8",$fecha_cierre));
	$fecha_cierre .= date(' | h:i A',strtotime($mostrar[3]));

	$finalizador = $mostrar[13];
	$sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$finalizador'";
	$result_e=mysqli_query($conexion,$sql_e);
	$ver_e=mysqli_fetch_row($result_e);

	$finalizador = $ver_e[0];
}
else
	$fecha_cierre = '---';

$efectivo_caja = $base+$total_ventas-$total_transferencias-$total_creditos-$total_gastos-$total_descuentos+$total_otros;

$base = '$'.number_format($base,0,'.','.');
$total_preparaciones = '$'.number_format($total_preparaciones,0,'.','.');
$total_productos = '$'.number_format($total_productos,0,'.','.');
$total_ventas = '$'.number_format($total_ventas,0,'.','.');
$total_efectivo = '$'.number_format($total_efectivo,0,'.','.');
$total_transferencias = '$-'.number_format($total_transferencias,0,'.','.');
$total_creditos = '$-'.number_format($total_creditos,0,'.','.');
$total_gastos = '$-'.number_format($total_gastos,0,'.','.');
$total_descuentos = '$'.number_format($total_descuentos,0,'.','.');
$efectivo_caja = '$'.number_format($efectivo_caja,0,'.','.');
$total_otros = '$'.number_format($total_otros,0,'.','.');

?>
<p class="row mb-0">
	<span class="col-lg-3 text-right"> Codigo: </span>
	<span class="col-lg-1 text-left"><b> <?php echo str_pad($cod_caja,3,"0",STR_PAD_LEFT) ?> </b></span>
	<span class="col-lg-1 text-right"> Estado: </span>
	<span class="col-lg-2 text-left"><b> <?php echo $estado ?> </b></span>
	<span class="col-lg-2 text-right"> Base: </span>
	<span class="col-lg-3 text-left"><b> <?php echo $base ?> </b></span>
</p>
<p class="row mb-0">
	<span class="col-lg-3 text-right"> Fecha Creación: </span>
	<span class="col-lg-3 text-left"><b> <?php echo $fecha_registro ?> </b></span>
	<span class="col-lg-3 text-right">Total Ventas: </span>
	<span class="col-lg-3 text-left"><b> <?php echo $total_ventas ?> </b></span>
</p>
<p class="row mb-0">
	<span class="col-lg-3 text-right"> Fecha Apertura: </span>
	<span class="col-lg-3 text-left"><b> <?php echo $fecha_apertura ?> </b></span>
	<span class="col-lg-3 text-right">Total Efectivo: </span>
	<span class="col-lg-3 text-left"><b> <?php echo $total_efectivo ?> </b></span>
</p>
<p class="row mb-0">
	<span class="col-lg-3 text-right"> Creador: </span>
	<span class="col-lg-3 text-left"><b> <?php echo $creador ?> </b></span>
	<span class="col-lg-3 text-right"> Transferencias: </span>
	<span class="col-lg-3 text-left text-muted"><b> <?php echo $total_transferencias ?> </b></span>
</p>
<p class="row mb-0">
	<span class="col-lg-3 text-right"> Cajero: </span>
	<span class="col-lg-3 text-left"><b> <?php echo $cajero ?> </b></span>
	<span class="col-lg-3 text-right"> Créditos: </span>
	<span class="col-lg-3 text-left text-muted"><b> <?php echo $total_creditos ?> </b></span>
</p>
<p class="row mb-0">
	<span class="col-lg-3 text-right"> Fecha Cierre: </span>
	<span class="col-lg-3 text-left"><b> <?php echo $fecha_cierre ?> </b></span>
	<span class="col-lg-3 text-right"> Gastos: </span>
	<span class="col-lg-3 text-left text-danger"><b> <?php echo $total_gastos ?> </b></span>
</p>
<p class="row mb-0">
	<span class="col-lg-3 text-right"></span>
	<span class="col-lg-3 text-left"></span>
	<span class="col-lg-3 text-right"> Descuentos: </span>
	<span class="col-lg-3 text-left text-danger"><b> <?php echo $total_descuentos ?> </b></span>
</p>
<p class="row mb-0">
	<span class="col-lg-3 text-right"></span>
	<span class="col-lg-3 text-left"></span>
	<span class="col-lg-3 text-right"> Otros: </span>
	<span class="col-lg-3 text-left"><b id="efectivo_caja"> <?php echo $total_otros ?> </b></span>
</p>
<p class="row mb-0">
	<span class="col-lg-3 text-right"></span>
	<span class="col-lg-3 text-left"></span>
	<span class="col-lg-3 text-right"> Efectivo en Caja: </span>
	<span class="col-lg-3 text-left"><b id="efectivo_caja"> <?php echo $efectivo_caja ?> </b></span>
</p>