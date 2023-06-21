<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
$conexion_1=$obj->conexion_m1();
$conexion_2=$obj->conexion_m2();
$conexion=$obj->conexion();

$fecha_inicial = date('Y-m-d 00:00:00',strtotime($_GET['fecha_inicial']));
$fecha_final = date('Y-m-d 23:59:59',strtotime($_GET['fecha_final']));
$local = $_GET['local'];

$num_local = explode('_', $local);

$sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Empresa'";
if($num_local[1] == 1)
  $result=mysqli_query($conexion_1,$sql);
else
  $result=mysqli_query($conexion_2,$sql);
$ver=mysqli_fetch_row($result);

$empresa = preg_replace("/[\r\n|\n|\r]+/", " ", $ver[2]);
$empresa = str_replace('  ', ' ', $empresa);
$empresa = json_decode($empresa,true);

$sql = "SELECT `codigo`, `cliente`, `productos`, `pago`, `fecha`, `cobrador`, `estado` FROM `ventas` WHERE fecha BETWEEN '$fecha_inicial' AND '$fecha_final' order by fecha ASC";
if($num_local[1] == 1)
  $result=mysqli_query($conexion_1,$sql);
else
  $result=mysqli_query($conexion_2,$sql);

$productos = array();
$descuentos = 0;

while ($mostrar=mysqli_fetch_row($result)) 
{
  $estado = $mostrar[6];
  if($estado != 'ANULADA')
  { 
    $pagos = json_decode($mostrar[3],true);

    $productos_venta = json_decode($mostrar[2],true);
    foreach ($productos_venta as $i => $producto)
    {
      $cod_producto = $producto['codigo'];
      $num_inv = $producto['num_inv'];

      $sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo='$cod_producto'";
      $result_producto=mysqli_query($conexion,$sql_producto);
      $mostrar_producto=mysqli_fetch_row($result_producto);

      if($mostrar_producto != null)
      {
        $inventario = array();
        if($num_local[1] == 1)
        {
          if ($mostrar_producto[6] != '')
            $inventario = json_decode($mostrar_producto[6],true);
        }
        else
        {
          if ($mostrar_producto[7] != '')
            $inventario = json_decode($mostrar_producto[7],true);
        }

        $costo = 0;

        if(isset($inventario[$num_inv]))
        {
          $costo = $inventario[$num_inv]['costo'];
        }
        if(isset($productos[$cod_producto]))
        {
          $productos[$cod_producto]['cant'] += $producto['cant'];
          $productos[$cod_producto]['venta_total'] += $producto['valor_unitario']*$producto['cant'];
          $productos[$cod_producto]['costo_total'] += $costo*$producto['cant'];
        }
        else
        {
          $productos[$cod_producto]['descripcion'] = $producto['descripcion'];
          $productos[$cod_producto]['cant'] = $producto['cant'];
          $productos[$cod_producto]['costo_total'] = $costo*$producto['cant'];
          $productos[$cod_producto]['venta_total'] = $producto['valor_unitario']*$producto['cant'];
        }
      }
    }

    foreach ($pagos as $i => $pago)
    {
      if($pago['tipo'] == 'Descuento')
        $descuentos += $pago['valor'];
    }

    $fecha_venta = strftime("%A, %e %b %Y", strtotime($mostrar[4]));
    $fecha_venta = ucfirst(iconv("ISO-8859-1","UTF-8",$fecha_venta));

    $fecha_venta .= date(' | h:i A',strtotime($mostrar[4]));

    $bg_estado = '';

    $bg_estado = 'bg-danger-light';
    $total = 0;
  }
}

$fecha_inicial = date('d/m/y',strtotime($_GET['fecha_inicial']));
$fecha_final = date('d/m/y',strtotime($_GET['fecha_final']));

$periodo = $fecha_inicial.' - '.$fecha_final;

$bodega = 'PDV_'.$num_local[1];

?>
<div class="text-center p-2">
  <h5 class="text-center">Informe de Rentabilidad</h5>
  <hr>
</div>
<div class="p-2">
  <div class="row">
    <p class="row mb-0">
      <span class="text-left"> Periodo: <b> <?php echo $periodo ?> </b></span>
    </p>
    <p class="row mb-0">
      <span class="text-left"> Empresa/Local: <b> <?php echo $empresa['nombre'].' (Local '.$num_local[1].')' ?> </b></span>
    </p>
    <p class="row mb-0">
      <span class="text-left"> NIT: <b> <?php echo $empresa['nit'] ?> </b></span>
    </p>
    <p class="row mb-0">
      <span class="text-left"> Dirección: <b> <?php echo $empresa['direccion'] ?> </b></span>
    </p>
    <p class="row mb-0">
      <span class="text-left"> Ciudad: <b> <?php echo $empresa['ciudad'] ?> </b></span>
    </p>

    <div class="row m-0 mt-5" id="div_tabla_ventas">
      <div class="border-top text-center px-2">
        <table class="table text-dark table-sm Data_Table" id="tabla_ventas">
          <thead>
            <tr class="text-center">
              <th>Cod</th>
              <th>Producto</th>
              <th>Cant</th>
              <th width="120px">Venta total</th>
              <th width="120px">Costo total</th>
              <th>Utilidad</th>
              <th>Margen</th>
              <th></th>
            </tr>
          </thead>
          <tbody class="overflow-auto">
            <?php 
            $venta_total = 0;
            $costo_total = 0;
            $utilidad_total = 0;
            foreach ($productos as $i => $producto)
            {
              $cod_producto = $i;
              $descripcion = substr($producto['descripcion'], 0,80);
              $cantidad = $producto['cant'];

              $utilidad = $producto['venta_total'] - $producto['costo_total'];
              $margen = round(($utilidad / $producto['venta_total'])* 100, 2);

              $venta_total += $producto['venta_total'];
              $costo_total += $producto['costo_total'];
              $utilidad_total += $utilidad;
              ?>
              <tr>
                <td class="text-center"><?php echo str_pad($cod_producto,3,"0",STR_PAD_LEFT) ?></td>
                <td class="text-left"><?php echo $descripcion ?></td>
                <td><?php echo $cantidad ?></td>
                <td class="text-right"><strong>$<?php echo number_format($producto['venta_total'],0,'.','.')?></strong></td>
                <td class="text-right"><strong>$<?php echo number_format($producto['costo_total'],0,'.','.')?></strong></td>
                <td class="text-right"><strong>$<?php echo number_format($utilidad,0,'.','.')?></strong></td>
                <td class="text-right"><?php echo $margen ?> %</td>
                <td class="text-center">
                  <button class="btn btn-outline-primary btn-round p-0 px-1" data-bs-toggle="modal" data-bs-target="#Modal_Ver" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_modal_producto').load('paginas/detalles/detalles_producto.php/?cod_producto=<?php echo $cod_producto ?>&bodega=<?php echo $bodega ?>', function(){cerrar_loader();});">
                    <span class="fa fa-search"></span>
                  </button>
                </td>
              </tr>
              <?php 
            }
            $venta_total += $descuentos;
            $utilidad_total += $descuentos;
            $margen_total = $utilidad_total/$venta_total;
            ?>
          </tbody>
        </table>
        <table class="table text-dark table-sm Data_Table" id="tabla_ventas">
          <thead>
            <tr class="text-center">
              <th class="text-right">Venta total</th>
              <th class="text-right">Costo total</th>
              <th class="text-right">Utilidad Total</th>
              <th class="text-right">Margen de Utilidad(PROM)</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="text-right h4"><strong>$<?php echo number_format($venta_total,0,'.','.')?></strong></td>
              <td class="text-right h4 text-warning"><strong>$<?php echo number_format($costo_total,0,'.','.')?></strong></td>
              <td class="text-right h4 text-success"><strong>$<?php echo number_format($utilidad_total,0,'.','.')?></strong></td>
              <td class="text-right h4 text-success"><strong><?php echo number_format($margen_total,0,'.','.')?></strong></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>