<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$cod_caja = $_GET['cod_caja'];
$caja = $_GET['caja'];

$num_caja = 0;

session_set_cookie_params(7 * 24 * 60 * 60);
session_start();

if (isset($_SESSION['caja_restaurante']))
  $num_caja = $_SESSION['caja_restaurante'];

$inventario = '';
$ventas = '';
$anular = 0;

$total_categorias = array();

$base = 0;
$total_preparaciones = 0;
$total_productos = 0;
$total_ventas = 0;
$total_efectivo = 0;
$total_transferencias = 0;
$total_creditos = 0;
$total_egresos = 0;
$resultado = 0;
$total_descuentos = 0;
$total_otros = 0;

$total_recargas = 0;
$total_recargas_e = 0;
$total_servicios = 0;

$fecha_apertura = '---';
$fecha_apertura_v = '---';
$fecha_cierre = '---';

$cajero = '---';
$finalizador = '---';

if ($caja == 1)
  $sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `finalizador`, `egresos`, `info`, `kilos_fin`, `kilos_inicio` FROM `caja` WHERE codigo = '$cod_caja'";
else if ($caja == 2)
  $sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `finalizador`, `egresos`, `info`, `kilos_fin`, `kilos_inicio` FROM `caja2` WHERE codigo = '$cod_caja'";
else
  $sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `finalizador`, `egresos`, `info`, `kilos_fin`, `kilos_inicio` FROM `caja3` WHERE codigo = '$cod_caja'";
$result = mysqli_query($conexion, $sql);
$mostrar = mysqli_fetch_row($result);

$estado = $mostrar[12];

$info = array();
if ($mostrar[15] != NULL) {
  $info = preg_replace("/[\r\n|\n|\r]+/", "<br>", $mostrar[15]);
  $info = str_replace('  ', ' ', $info);
  $info = json_decode($info, true);
}

$observaciones = '';
if (isset($info['observaciones']))
  $observaciones = $info['observaciones'];

$fecha_registro = strftime("%A, %e %b %Y", strtotime($mostrar[1]));
$fecha_registro = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_registro));
$fecha_registro .= date(' | h:i A', strtotime($mostrar[1]));

$base = $mostrar[8];
$inventario = json_decode($mostrar[4], true);

$cantidad_inicial = $mostrar[17];
$cantidad_final = $mostrar[16];

$ingresos = array();
if ($mostrar[9] != NULL)
  $ingresos = json_decode($mostrar[9], true);

$egresos = array();
if ($mostrar[14] != '')
  $egresos = json_decode($mostrar[14], true);

$creador = $mostrar[10];

$sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
$result_e = mysqli_query($conexion, $sql_e);
$ver_e = mysqli_fetch_row($result_e);

if ($ver_e != null)
  $creador = $ver_e[0];

if ($estado == 'CREADA') {
  $sql_ventas = "SELECT `codigo`, `cliente`, `productos`, `pago`, `fecha`, `cobrador`, `estado`  FROM `ventas` WHERE fecha > '$mostrar[1]' AND caja = '$caja' order by fecha ASC";
  $sql_gastos = "SELECT `codigo`, `descripcion`, `valor`, `fecha_registro` FROM `gastos` WHERE fecha_registro > '$mostrar[1]' order by fecha_registro ASC";
}

if ($estado == 'ABIERTA' || $estado == 'CERRADA') {
  $anular = 1;
  $cajero = $mostrar[11];
  if ($estado == 'CERRADA')
    $fecha_cierre = $mostrar[3];
  else
    $fecha_cierre = date('Y-m-d G:i:s');

  $sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$cajero'";
  $result_e = mysqli_query($conexion, $sql_e);
  $ver_e = mysqli_fetch_row($result_e);

  if ($ver_e != null)
    $cajero = $ver_e[0];

  $fecha_apertura = $mostrar[2];

  foreach ($ingresos as $i => $ingreso) {
    if (isset($ingreso['metodo'])) {
      if ($ingreso['metodo'] == 'Efectivo')
        $total_efectivo += $ingreso['valor'];

      if ($ingreso['metodo'] == 'Devolución')
        $total_efectivo += $ingreso['valor'];

      if ($ingreso['metodo'] == 'Bancolombia' || $ingreso['metodo'] == 'Nequi' || $ingreso['metodo'] == 'Tarjeta' || $ingreso['metodo'] == 'Daviplata')
        $total_transferencias += $ingreso['valor'];

      if ($ingreso['metodo'] == 'Descuento') {
        $total_descuentos += $ingreso['valor'];
        $total_ventas += $ingreso['valor'];
      }
    } else
      $total_efectivo += $ingreso['valor'];

    $total_otros += $ingreso['valor'];
  }


  foreach ($egresos as $i => $egreso)
    $total_egresos += $egreso['valor'];

  $sql_ventas = "SELECT `codigo`, `cliente`, `productos`, `pago`, `fecha`, `cobrador`, `estado`, `caja` FROM `ventas` WHERE caja = '$caja' AND fecha BETWEEN '$fecha_apertura' AND '$fecha_cierre'";
  $result_ventas = mysqli_query($conexion, $sql_ventas);

  while ($mostrar_ventas = mysqli_fetch_row($result_ventas)) {
    $estado_venta = $mostrar_ventas[6];
    if ($estado_venta != 'ANULADA') {
      $productos_venta = json_decode($mostrar_ventas[2], true);

      foreach ($productos_venta as $i => $producto) {
        $total_ventas += $producto['valor_unitario'] * $producto['cant'];

        if (isset($total_categorias[$producto['area']]))
          $total_categorias[$producto['area']] += $producto['valor_unitario'] * $producto['cant'];
        else
          $total_categorias[$producto['area']] = $producto['valor_unitario'] * $producto['cant'];
      }

      $pagos_venta = json_decode($mostrar_ventas[3], true);
      foreach ($pagos_venta as $i => $pago) {
        if ($pago['tipo'] == 'Efectivo')
          $total_efectivo += $pago['valor'];

        if ($pago['tipo'] == 'Bancolombia' || $pago['tipo'] == 'Nequi' || $pago['tipo'] == 'Tarjeta' || $pago['tipo'] == 'Daviplata')
          $total_transferencias += $pago['valor'];

        if ($pago['tipo'] == 'Crédito')
          $total_creditos += $pago['valor'];

        if ($pago['tipo'] == 'Descuento') {
          $total_descuentos += $pago['valor'];
          $total_ventas += $pago['valor'];
        }
      }
    }
  }

  $cajero = $mostrar[11];
  $sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$cajero'";
  $result_e = mysqli_query($conexion, $sql_e);
  $ver_e = mysqli_fetch_row($result_e);

  if ($ver_e != null)
    $cajero = $ver_e[0];

  $fecha_apertura_v = strftime("%A, %e %b %Y", strtotime($mostrar[2]));
  $fecha_apertura_v = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_apertura_v));
  $fecha_apertura_v .= date(' | h:i A', strtotime($mostrar[2]));
}

if ($estado == 'CERRADA') {
  $fecha_cierre = $mostrar[3];

  $fecha_cierre_v = strftime("%A, %e %b %Y", strtotime($mostrar[3]));
  $fecha_cierre_v = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_cierre_v));
  $fecha_cierre_v .= date(' | h:i A', strtotime($mostrar[3]));

  $finalizador = $mostrar[13];
  $sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$finalizador'";
  $result_e = mysqli_query($conexion, $sql_e);
  $ver_e = mysqli_fetch_row($result_e);

  if ($ver_e != null)
    $finalizador = $ver_e[0];
} else
  $fecha_cierre_v = '---';

$efectivo_caja = $base + $total_efectivo - $total_egresos + $total_recargas_e;

$base = '$' . number_format($base, 0, '.', '.');
$total_ventas = '$' . number_format($total_ventas, 0, '.', '.');
$total_efectivo = '$' . number_format($total_efectivo, 0, '.', '.');
$total_transferencias = '$-' . number_format($total_transferencias, 0, '.', '.');
$total_creditos = '$-' . number_format($total_creditos, 0, '.', '.');
$total_egresos = '$-' . number_format($total_egresos, 0, '.', '.');
$total_descuentos = '$' . number_format($total_descuentos, 0, '.', '.');
$efectivo_caja = '$' . number_format($efectivo_caja, 0, '.', '.');
$total_otros = '$' . number_format($total_otros, 0, '.', '.');

$total_recargas = '$' . number_format($total_recargas, 0, '.', '.');
$total_servicios = '$' . number_format($total_servicios, 0, '.', '.');

$ruta_pdf = 'cajas_pdf/Reporte*Caja*No*' . $cod_caja . '.pdf';

?>
<div class="modal-header row p-2 m-0">
  <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 col-8">
    <h5 class="modal-title">Detalles de caja</h5>
  </div>
  <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 col-4 text-right">
    <?php
    if ($estado == 'ABIERTA') {
    ?>
      <button hidden class="btn btn-sm btn-outline-primary btn-round p-1" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_cierre_caja').load('paginas/detalles/caja.php/?cod_caja=<?php echo $cod_caja ?>&caja=<?php echo $caja ?>', function(){cerrar_loader();});">
        <span class="fas fa-sync-alt"></span>
      </button>
    <?php
    } else if ($estado == 'CERRADA') {
    ?>
      <button class="btn btn-info btn-round p-1" id="btn_imprimir_cierre">
        <span class="fas fa-file-pdf"></span>
      </button>
    <?php
    }
    ?>
  </div>
</div>
<div class="modal-body p-2 py-0">
  <div id="div_labels" class="row m-0">
    <div class="col-lg-6 col-md-6 col-sm-12">
      <hr>
      <p class="row mb-0">
        <span class="col-lg-6 col-6 col-sm-4 text-md-right text-sm-left"> Codigo: </span>
        <span class="col-lg-6 col-6 col-sm-8 text-left"><b> <?php echo str_pad($cod_caja, 3, "0", STR_PAD_LEFT) ?> </b></span>
      </p>
      <p class="row mb-0">
        <span class="col-lg-6 col-6 col-sm-4 text-md-right text-sm-left"> Estado: </span>
        <span class="col-lg-6 col-6 col-sm-8 text-left"><b> <?php echo $estado ?> </b></span>
      </p>
      <p class="row mb-0">
        <span class="col-lg-6 col-6 col-sm-4 text-md-right text-sm-left"> Fecha Creación: </span>
        <span class="col-lg-6 col-6 col-sm-8 text-left"><b> <?php echo $fecha_registro ?> </b></span>
      </p>
      <p class="row mb-0">
        <span class="col-lg-6 col-6 col-sm-4 text-md-right text-sm-left"> Fecha Apertura: </span>
        <span class="col-lg-6 col-6 col-sm-8 text-left"><b> <?php echo $fecha_apertura_v ?> </b></span>
      </p>
      <p class="row mb-0">
        <span class="col-lg-6 col-6 col-sm-4 text-md-right text-sm-left"> Creador: </span>
        <span class="col-lg-6 col-6 col-sm-8 text-left"><b> <?php echo $creador ?> </b></span>
      </p>
      <p class="row mb-0">
        <span class="col-lg-6 col-6 col-sm-4 text-md-right text-sm-left"> Cajero: </span>
        <span class="col-lg-6 col-6 col-sm-8 text-left"><b> <?php echo $cajero ?> </b></span>
      </p>
      <p class="row mb-0">
        <span class="col-lg-6 col-6 col-sm-4 text-md-right text-sm-left"> Fecha Cierre: </span>
        <span class="col-lg-6 col-6 col-sm-8 text-left"><b> <?php echo $fecha_cierre_v ?> </b></span>
      </p>
      <hr>
      <p class="row mb-0">
        <span class="col-lg-6 col-6 col-sm-4 text-md-right text-sm-left"> Cantidad Inicial: </span>
        <span class="col-lg-6 col-6 col-sm-8 text-left"><b> <?php echo $cantidad_inicial ?> Kg </b></span>
      </p>
      <p class="row mb-0">
        <span class="col-lg-6 col-6 col-sm-4 text-md-right text-sm-left"> Cantidad Final: </span>
        <span class="col-lg-6 col-6 col-sm-8 text-left"><b> <?php echo $cantidad_final ?> Kg </b></span>
      </p>

      <hr>
      <?php
      foreach ($total_categorias as $cat => $value) {
        $nombre_categoria = $cat;
        $total_cat = '$' . number_format($value, 0, '.', '.');
      ?>
        <p class="row mb-0">
          <span class="col-lg-6 col col-sm-4 text-md-right text-sm-left">Total <?php echo $nombre_categoria ?>: </span>
          <span class="col-lg-6 col col-sm-8 text-left"><b> <?php echo $total_cat ?> </b></span>
        </p>
      <?php
      }
      ?>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-12">
      <hr>
      <p class="row mb-0">
        <span class="col-lg-6 col col-sm-4 text-md-right text-sm-left"> Base: </span>
        <span class="col-lg-6 col col-sm-8 text-left"><b> <?php echo $base ?> </b></span>
      </p>
      <p class="row mb-0">
        <span class="col-lg-6 col col-sm-4 text-md-right text-sm-left">Total Ventas: </span>
        <span class="col-lg-6 col col-sm-8 text-left text-success"><b> <?php echo $total_ventas ?> </b></span>
      </p>
      <p class="row mb-0">
        <span class="col-lg-6 col col-sm-4 text-md-right text-sm-left"> Total servicios: </span>
        <span class="col-lg-6 col col-sm-8 text-left text-info"><b id="efectivo_caja"> <?php echo $total_servicios ?> </b></span>
      </p>
      <p class="row mb-0 border-top border-2">
        <span class="col-lg-6 col col-sm-4 text-md-right text-sm-left">Total Efectivo: </span>
        <span class="col-lg-6 col col-sm-8 text-left"><b> <?php echo $total_efectivo ?> </b></span>
      </p>
      <p class="row mb-0">
        <span class="col-lg-6 col col-sm-4 text-md-right text-sm-left"> Transferencias: </span>
        <span class="col-lg-6 col col-sm-8 text-left text-muted"><b> <?php echo $total_transferencias ?> </b></span>
      </p>
      <p class="row mb-0">
        <span class="col-lg-6 col col-sm-4 text-md-right text-sm-left"> Créditos: </span>
        <span class="col-lg-6 col col-sm-8 text-left text-muted"><b> <?php echo $total_creditos ?> </b></span>
      </p>
      <p class="row mb-0">
        <span class="col-lg-6 col col-sm-4 text-md-right text-sm-left"> Descuentos: </span>
        <span class="col-lg-6 col col-sm-8 text-left text-muted"><b> <?php echo $total_descuentos ?> </b></span>
      </p>
      <p class="row mb-0">
        <span class="col-lg-6 col col-sm-4 text-md-right text-sm-left"> Gastos: </span>
        <span class="col-lg-6 col col-sm-8 text-left text-danger"><b> <?php echo $total_egresos ?> </b></span>
      </p>
      <p class="row mb-0">
        <span class="col-lg-6 col col-sm-4 text-md-right text-sm-left"> Ingresos: </span>
        <span class="col-lg-6 col col-sm-8 text-left"><b id="ingresos_caja"> <?php echo $total_otros ?> </b></span>
      </p>
      <p class="row mb-0 border-top border-2">
        <span class="col-lg-6 col col-sm-4 text-md-right text-sm-left"> Efectivo en Caja: </span>
        <span class="col-lg-6 col col-sm-8 text-left"><b id="efectivo_caja"> <?php echo $efectivo_caja ?> </b></span>
      </p>
      <hr>
      <?php
      if ($estado == 'ABIERTA') {
      ?>
        <div class="row m-0">
          <label for="observaciones_cierre">Observaciones</label>
          <textarea class="form-control" placeholder="Leave a comment here" id="observaciones_cierre" name="observaciones_cierre" style="height: 100px" onchange="cambiar_observaciones(this.value)"><?php echo str_replace('<br>', "\n", $observaciones) ?></textarea>
        </div>
      <?php
      } else if ($estado == 'CERRADA') {
      ?>
        <p class="row mb-0"> Observaciones: </p>
        <p class="row mb-0 px-2"> <b> <?php echo $observaciones ?> </b>
        </p>
      <?php
      }
      ?>
    </div>

  </div>
  <hr>

  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
      <a class="nav-link text-gray active" id="ventas-tab" data-bs-toggle="tab" href="#ventas-1" role="tab" aria-controls="ventas" aria-selected="true">Ventas</a>
    </li>
    <li class="nav-item">
      <a class="nav-link text-gray" id="gastos-tab" data-bs-toggle="tab" href="#gastos-1" role="tab" aria-controls="gastos" aria-selected="false">Gastos</a>
    </li>
    <li class="nav-item">
      <a class="nav-link text-gray" id="ingresos-tab" data-bs-toggle="tab" href="#ingresos-1" role="tab" aria-controls="ingresos" aria-selected="false">Ingresos</a>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane show py-2 active" id="ventas-1" role="tabpanel" aria-labelledby="ventas-tab">
      <div class="table-responsive text-dark text-center py-0 px-1">
        <table class="table text-dark table-sm" id="tabla_ventas">
          <thead>
            <tr class="text-center">
              <th class="p-1">Cod</th>
              <th class="p-1">Cliente</th>
              <th class="p-1">Fecha</th>
              <th class="p-1">Total</th>
              <th class="p-1">Creador</th>
              <th class="p-1">Pagos</th>
              <th class="p-1"></th>
            </tr>
          </thead>
          <tbody class="overflow-auto">
            <?php
            $result_ventas = mysqli_query($conexion, $sql_ventas);
            $total_ventas = 0;

            while ($mostrar_ventas = mysqli_fetch_row($result_ventas)) {
              $cod_venta = $mostrar_ventas[0];

              $total = 0;
              $cliente = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar_ventas[1]);
              $cliente = str_replace("	", " ", $cliente);
              $cliente = json_decode($cliente, true);

              $estado_venta = $mostrar_ventas[6];

              $productos_venta = json_decode($mostrar_ventas[2], true);
              foreach ($productos_venta as $i => $producto)
                $total += $producto['valor_unitario'] * $producto['cant'];

              $cobrador = $mostrar_ventas[5];

              if ($cobrador != 0) {
                $sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$cobrador'";
                $result_e = mysqli_query($conexion, $sql_e);
                $ver_e = mysqli_fetch_row($result_e);

                if ($ver_e != null)
                  $cobrador = $ver_e[0];
              } else
                $cobrador = 'Sistema';

              $fecha_venta = strftime("%A, %e %b %Y", strtotime($mostrar_ventas[4]));
              $fecha_venta = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_venta));

              $fecha_venta .= date(' | h:i A', strtotime($mostrar_ventas[4]));
              $bg_estado = '';
              if ($estado_venta == 'ANULADA') {
                $bg_estado = 'bg-danger-light';
                $total = 0;
              }

              $pagos = json_decode($mostrar_ventas[3], true);
              foreach ($pagos as $i => $pago) {
                if ($pago['tipo'] == 'Descuento')
                  $total += $pago['valor'];
              }
            ?>
              <tr>
                <td class="p-1 text-center <?php echo $bg_estado ?>"><?php echo str_pad($mostrar_ventas[0], 3, "0", STR_PAD_LEFT) ?></td>
                <td class="p-1"><?php echo $cliente['nombre'] ?></td>
                <td class="p-1"><?php echo $fecha_venta ?></td>
                <td class="p-1 text-right"><strong>$<?php echo number_format($total, 0, '.', '.') ?></strong></td>
                <td class="p-1 text-center"><?php echo $cobrador ?></td>
                <td class="p-1 text-left lh-1">
                  <small>
                    <?php
                    foreach ($pagos as $i => $pago) {
                      $text_color = '';
                      if ($pago['tipo'] != 'Efectivo' && $pago['tipo'] != 'Descuento' && $pago['tipo'] != 'Crédito')
                        $text_color = 'text-danger';

                      echo '<b><span class="' . $text_color . '">-> ' . $pago['tipo'] . ' </span>($' . number_format($pago['valor'], 0, '.', '.') . ')</b><br>';
                    }
                    ?>

                  </small>
                </td>
                <td class="text-center p-0">
                  <button class="btn btn-sm btn-outline-primary btn-round" data-bs-toggle="modal" data-bs-target="#Modal_venta" onclick="$('#cod_caja_atras').val(<?php echo $cod_caja ?>);$('#caja_atras').val(<?php echo $caja ?>);document.getElementById('div_loader').style.display = 'block';$('#div_modal_venta').load('paginas/detalles/detalles_venta.php/?cod_venta=<?php echo $cod_venta ?>&anular=<?php echo $anular ?>', function(){cerrar_loader();})">
                    <span class="fa fa-search"></span>
                  </button>
                </td>
              </tr>
            <?php
              $total_ventas += $total;
            }
            ?>
          </tbody>
        </table>
        <div class="row float-right mt-3">
          <h3>Total ventas: $<?php echo number_format($total_ventas, 0, '.', '.') ?></h3>
        </div>
      </div>
    </div>

    <div class="tab-pane fade py-2" id="gastos-1" role="tabpanel" aria-labelledby="gastos-tab">
      <div class="table-responsive text-dark text-center py-0 px-1">
        <table class="table text-dark table-sm" id="tabla_gastos">
          <thead>
            <tr class="text-center">
              <th>#</th>
              <th>Descripción</th>
              <th width="120px">Valor</th>
              <th width="280px">Fecha</th>
              <?php
              if ($estado == 'ABIERTA') {
              ?>
                <th></th>
              <?php
              }
              ?>
            </tr>
          </thead>
          <tbody class="overflow-auto">
            <?php
            $total_gastos = 0;
            $num_item = 1;
            foreach ($egresos as $j => $egreso) {
              $descripcion = $egreso['concepto'];
              $valor = $egreso['valor'];

              $fecha_egreso = strftime("%A, %e %b %Y", strtotime($egreso['fecha']));
              $fecha_egreso = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_egreso));

              $fecha_egreso .= date(' | h:i A', strtotime($egreso['fecha']));
            ?>
              <tr>
                <td class="text-center"><?php echo str_pad($num_item, 3, "0", STR_PAD_LEFT) ?></td>
                <td><?php echo $descripcion ?></td>
                <td class="text-right"><strong>$<?php echo number_format($valor, 0, '.', '.') ?></strong></td>
                <td class="text-center"><?php echo $fecha_egreso ?></td>
                <?php
                if ($estado == 'ABIERTA') {
                ?>
                  <td class="text-center p-0">
                    <button class="btn btn-sm btn-danger btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Eliminar_Gasto" onclick="$('#cod_caja_atras').val(<?php echo $cod_caja ?>);$('#caja_atras').val(<?php echo $caja ?>);$('#cod_caja_eliminar_gasto').val(<?php echo $cod_caja ?>);$('#item_eliminar_gasto').val(<?php echo $j ?>)">
                      <span class="fa fa-trash"></span>
                    </button>
                  </td>
                <?php
                }
                ?>
              </tr>
            <?php
              $num_item++;
              $total_gastos += $valor;
            }

            if ($estado == 'ABIERTA') {
            ?>
              <tr>
                <td class="p-1" colspan="2">
                  <input type="text" class="form-control p-1" name="concepto_egreso" id="concepto_egreso" placeholder="Descripción Egreso" autocomplete="off">
                </td>
                <td class="p-1 text-right">
                  <input type="text" class="form-control p-1 moneda text-right" name="valor_egreso" id="valor_egreso" placeholder="Valor Egreso" autocomplete="off">
                </td>
                <td class="p-1 text-center">
                  <button class="btn btn-sm btn-info btn-round" id="btn_agregar_egreso" onclick="agregar_egreso('<?php echo $cod_caja ?>')">AGREGAR EGRESO</button>
                </td>
              </tr>
            <?php
            }
            ?>
          </tbody>
        </table>
        <div class="row float-right mt-3">
          <h3>Total gastos: $<?php echo number_format($total_gastos, 0, '.', '.') ?></h3>
        </div>
      </div>
    </div>

    <div class="tab-pane fade py-2" id="ingresos-1" role="tabpanel" aria-labelledby="ingresos-tab">
      <div class="table-responsive text-dark text-center py-0 px-1">
        <table class="table text-dark table-sm" id="tabla_ingresos">
          <thead>
            <tr class="text-center">
              <th>Cod</th>
              <th>Descripción</th>
              <th>Valor</th>
              <th>Método</th>
              <th>Fecha</th>
              <?php
              if ($estado == 'ABIERTA') {
              ?>
                <th></th>
              <?php
              }
              ?>
            </tr>
          </thead>
          <tbody class="overflow-auto">
            <?php
            $num_item = 1;
            $total_ingresos = 0;
            foreach ($ingresos as $i => $ingreso) {
              $descripcion = $ingreso['descripcion'];
              $valor = $ingreso['valor'];
              $metodo_pago = '---';
              if (isset($ingreso['metodo']))
                $metodo_pago = $ingreso['metodo'];

              $text_color = '';
              if ($metodo_pago != 'Efectivo' && $metodo_pago != 'Descuento' && $metodo_pago != 'Crédito')
                $text_color = 'text-danger';

              $fecha_ingreso = strftime("%A, %e %b %Y", strtotime($ingreso['fecha']));
              $fecha_ingreso = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_ingreso));

              $fecha_ingreso .= date(' | h:i A', strtotime($ingreso['fecha']));
            ?>
              <tr>
                <td class="text-center"><?php echo str_pad($num_item, 3, "0", STR_PAD_LEFT) ?></td>
                <td><?php echo $descripcion ?></td>
                <td class="text-right"><strong>$<?php echo number_format($valor, 0, '.', '.') ?></strong></td>
                <td class="text-center <?php echo $text_color ?>"><?php echo $metodo_pago ?></td>
                <td class="text-center"><?php echo $fecha_ingreso ?></td>
                <?php
                if ($estado == 'ABIERTA') {
                  if (isset($ingreso['cod_unico'])) {
                    $cod_unico = $ingreso['cod_unico'];
                ?>
                    <td class="text-center p-0">
                      <a class="btn btn-sm btn-outline-danger btn-round p-0 px-1" onclick="eliminar_ingreso('<?php echo $cod_caja ?>','<?php echo $caja ?>','<?php echo $i ?>','<?php echo $cod_unico ?>');">
                        <span class="fa fa-trash"></span>
                      </a>
                    </td>
                <?php
                  }
                }
                ?>
              </tr>
            <?php
              $total_ingresos += $valor;
              $num_item++;
            }

            if ($estado == 'ABIERTA') {
            ?>
              <tr>
                <td class="p-1 text-center"><?php echo str_pad($num_item, 3, "0", STR_PAD_LEFT) ?></td>
                <td class="p-1">
                  <input type="text" class="form-control p-1" name="descripcion_ingreso" id="descripcion_ingreso" placeholder="Descripción Ingreso" autocomplete="off">
                </td>
                <td class="p-1 text-right">
                  <input type="text" class="form-control p-1 moneda text-right" name="valor_ingreso" id="valor_ingreso" placeholder="Valor Ingreso" autocomplete="off">
                </td>
                <td class="p-1 text-center">
                  <button class="btn btn-sm btn-info btn-round" id="btn_agregar_ingreso" onclick="agregar_ingreso('<?php echo $cod_caja ?>')">AGREGAR INGRESO</button>
                </td>
              </tr>
            <?php
            }
            ?>
          </tbody>
        </table>
        <div class="row float-right mt-3">
          <h3>Total ingresos: $<?php echo number_format($total_ingresos, 0, '.', '.') ?></h3>
        </div>
      </div>
    </div>

  </div>
</div>
<div class="modal-footer p-2">
  <div class="col text-left">
    <button type="button" class="btn btn-sm btn-secondary btn-round " data-bs-dismiss="modal">Cerrar</button>
  </div>
  <?php
  if ($estado == 'CREADA') {
  ?>
    <div class="row m-0 mx-2">
      <input type="number" class="form-control form-control-sm col" id="cantidad_inicial" placeholder="Cantidad Inicial (kg)">
      <button class="btn btn-warning btn-round btn-sm col-auto" onclick="agregar_cantidad_inicial('<?php echo $cod_caja ?>')">GUARDAR CANTIDAD</button>
    </div>

    <div class="row m-0">
      <input type="text" class="form-control p-1 moneda w-auto" name="base_nueva" id="base_nueva" placeholder="Nueva Base">
      <button class="btn btn-sm btn-info btn-round w-auto" id="btn_agregar_base" onclick="agregar_base('<?php echo $cod_caja ?>')">AGREGAR BASE</button>
    </div>

    <button class="btn btn-sm btn-outline-primary btn-round" id="btn_abrir_caja" onclick="abrir_caja('<?php echo $cod_caja ?>')">ABRIR CAJA</button>
  <?php
  }
  if ($estado == 'ABIERTA') {
  ?>
    <div class="row m-0 mx-2">
      <input type="number" class="form-control form-control-sm col" id="cantidad_final" placeholder="Cantidad Final (kg)">
      <button class="btn btn-warning btn-round btn-sm col-auto" onclick="agregar_cantidad_final('<?php echo $cod_caja ?>')">GUARDAR CANTIDAD</button>
    </div>
    <button class="btn btn-sm btn-outline-primary btn-round" data-bs-toggle="modal" data-bs-target="#Modal_confirmacion_cerrar" onclick="$('#cod_caja_confirm').val(<?php echo $cod_caja ?>);$('#caja_confirm').val(<?php echo $caja ?>);document.getElementById('Modal_Ver').classList.remove('show');">CERRAR CAJA</button>
  <?php
  }
  ?>
</div>

<!-- #END# Tabla Productos -->
<script type="text/javascript">
  $(document).ready(function() {
    //$('.Data_Table').DataTable();
  });
  $('input.moneda').keyup(function(event) {
    if (event.which >= 37 && event.which <= 40) {
      event.preventDefault();
    }
    $(this).val(function(index, value) {
      return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
    });
  });

  function abrir_caja(cod_caja) {
    document.getElementById('div_loader').style.display = 'block';
    $.ajax({
      type: "POST",
      data: "cod_caja=" + cod_caja + "&caja=<?php echo $caja ?>",
      url: "procesos/abrir_caja.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          $('#div_cierre_caja').load('paginas/detalles/caja.php/?cod_caja=<?php echo $cod_caja ?>&caja=<?php echo $caja ?>', function() {
            cerrar_loader();
          });
          $('#div_tabla_caja').load('tablas/caja.php');
        } else {
          w_alert({
            titulo: datos['consulta'],
            tipo: 'danger'
          });
          if (datos['consulta'] == 'Reload') {
            document.getElementById('div_login').style.display = 'block';
            cerrar_loader();

          }
          cerrar_loader();
        }
      }
    });
  }

  function agregar_base(cod_caja) {
    document.getElementById('div_loader').style.display = 'block';
    base = document.getElementById("base_nueva").value;
    if (base != '' && base >= 0) {
      $.ajax({
        type: "POST",
        data: "cod_caja=" + cod_caja + "&caja=<?php echo $caja ?>" + "&base=" + base,
        url: "procesos/agregar_base.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            $('#div_cierre_caja').load('paginas/detalles/caja.php/?cod_caja=<?php echo $cod_caja ?>&caja=<?php echo $caja ?>', function() {
              cerrar_loader();
            });
            $('#div_tabla_caja').load('tablas/caja.php', function() {
              cerrar_loader();
            });
          } else {
            w_alert({
              titulo: datos['consulta'],
              tipo: 'danger'
            });
            if (datos['consulta'] == 'Reload') {
              document.getElementById('div_login').style.display = 'block';
              cerrar_loader();

            }
            cerrar_loader();
          }
        }
      });
    } else {
      w_alert({
        titulo: 'Ingrese la base nueva',
        tipo: 'danger'
      });
      cerrar_loader();
    }
  }

  function agregar_cantidad_inicial(cod_caja) {
    document.getElementById('div_loader').style.display = 'block';
    cantidad_inicial = document.getElementById("cantidad_inicial").value;
    if (cantidad_inicial != '' && cantidad_inicial >= 0) {
      $.ajax({
        type: "POST",
        data: "cod_caja=" + cod_caja + "&caja=<?php echo $caja ?>" + "&cantidad_inicial=" + cantidad_inicial,
        url: "procesos/agregar_cantidad_inicial.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            $('#div_cierre_caja').load('paginas/detalles/caja.php/?cod_caja=<?php echo $cod_caja ?>&caja=<?php echo $caja ?>', function() {
              cerrar_loader();
            });
            $('#div_tabla_caja').load('tablas/caja.php', function() {
              cerrar_loader();
            });
          } else {
            w_alert({
              titulo: datos['consulta'],
              tipo: 'danger'
            });
            if (datos['consulta'] == 'Reload') {
              document.getElementById('div_login').style.display = 'block';
              cerrar_loader();

            }
            cerrar_loader();
          }
        }
      });
    } else {
      w_alert({
        titulo: 'Ingrese la cantidad inicial',
        tipo: 'danger'
      });
      cerrar_loader();
    }
  }

  function agregar_cantidad_final(cod_caja) {
    document.getElementById('div_loader').style.display = 'block';
    cantidad_final = document.getElementById("cantidad_final").value;
    if (cantidad_final != '' && cantidad_final >= 0) {
      $.ajax({
        type: "POST",
        data: "cod_caja=" + cod_caja + "&caja=<?php echo $caja ?>" + "&cantidad_final=" + cantidad_final,
        url: "procesos/agregar_cantidad_final.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            $('#div_cierre_caja').load('paginas/detalles/caja.php/?cod_caja=<?php echo $cod_caja ?>&caja=<?php echo $caja ?>', function() {
              cerrar_loader();
            });
            $('#div_tabla_caja').load('tablas/caja.php', function() {
              cerrar_loader();
            });
          } else {
            w_alert({
              titulo: datos['consulta'],
              tipo: 'danger'
            });
            if (datos['consulta'] == 'Reload') {
              document.getElementById('div_login').style.display = 'block';
              cerrar_loader();

            }
            cerrar_loader();
          }
        }
      });
    } else {
      w_alert({
        titulo: 'Ingrese la cantidad final',
        tipo: 'danger'
      });
      cerrar_loader();
    }
  }

  function cambiar_observaciones(observaciones) {
    document.getElementById('div_loader').style.display = 'block';
    $.ajax({
      type: "POST",
      data: "observaciones=" + observaciones + "&cod_caja=<?php echo $cod_caja ?>",
      url: "procesos/cambiar_observaciones_caja.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          $('#div_cierre_caja').load('paginas/detalles/caja.php/?cod_caja=<?php echo $cod_caja ?>&caja=<?php echo $caja ?>', function() {
            cerrar_loader();
          });
          $('#div_tabla_caja').load('tablas/caja.php', function() {
            cerrar_loader();
          });
        } else {
          w_alert({
            titulo: datos['consulta'],
            tipo: 'danger'
          });
          if (datos['consulta'] == 'Reload') {
            document.getElementById('div_login').style.display = 'block';
            cerrar_loader();

          }
          cerrar_loader();
        }
      }
    });
  }

  function agregar_ingreso(cod_caja) {
    document.getElementById('div_loader').style.display = 'block';
    valor_ingreso = document.getElementById("valor_ingreso").value;
    descripcion_ingreso = document.getElementById("descripcion_ingreso").value;
    if (descripcion_ingreso != '') {
      if (valor_ingreso != '' && valor_ingreso >= 0) {
        $.ajax({
          type: "POST",
          data: "cod_caja=" + cod_caja + "&caja=<?php echo $caja ?>" + "&descripcion_ingreso=" + descripcion_ingreso + "&valor_ingreso=" + valor_ingreso,
          url: "procesos/agregar_ingreso.php",
          success: function(r) {
            datos = jQuery.parseJSON(r);
            if (datos['consulta'] == 1) {
              w_alert({
                titulo: 'Ingreso agregado con exito',
                tipo: 'success'
              });
              $('#div_cierre_caja').load('paginas/detalles/caja.php/?cod_caja=<?php echo $cod_caja ?>&caja=<?php echo $caja ?>', function() {
                cerrar_loader();
              });
            } else {
              w_alert({
                titulo: datos['consulta'],
                tipo: 'danger'
              });
              if (datos['consulta'] == 'Reload') {
                document.getElementById('div_login').style.display = 'block';
                cerrar_loader();

              }
              cerrar_loader();
            }
          }
        });
      } else
        w_alert({
          titulo: 'Ingrese el valor del ingreso',
          tipo: 'danger'
        });
    } else
      w_alert({
        titulo: 'Ingrese la descripcion del ingreso',
        tipo: 'danger'
      });
    cerrar_loader();
    setTimeout('$("#ingresos-tab").click();', 500);
  }

  function agregar_egreso(cod_caja) {
    document.getElementById('div_loader').style.display = 'block';
    valor_egreso = document.getElementById("valor_egreso").value;
    concepto_egreso = document.getElementById("concepto_egreso").value;
    if (concepto_egreso != '') {
      if (valor_egreso != '' && valor_egreso >= 0) {
        $.ajax({
          type: "POST",
          data: "cod_caja=" + cod_caja + "&caja=<?php echo $caja ?>" + "&concepto_egreso=" + concepto_egreso + "&valor_egreso=" + valor_egreso,
          url: "procesos/agregar_egreso.php",
          success: function(r) {
            datos = jQuery.parseJSON(r);
            if (datos['consulta'] == 1) {
              w_alert({
                titulo: 'Egreso agregado con exito',
                tipo: 'success'
              });
              $('#div_cierre_caja').load('paginas/detalles/caja.php/?cod_caja=<?php echo $cod_caja ?>&caja=<?php echo $caja ?>', function() {
                cerrar_loader();
              });
              setTimeout('$("#gastos-tab").click();', 200);
            } else {
              w_alert({
                titulo: datos['consulta'],
                tipo: 'danger'
              });
              if (datos['consulta'] == 'Reload') {
                document.getElementById('div_login').style.display = 'block';
                cerrar_loader();

              }
              cerrar_loader();
            }
          }
        });
      } else
        w_alert({
          titulo: 'Ingrese el valor del egreso',
          tipo: 'danger'
        });
    } else
      w_alert({
        titulo: 'Ingrese la descripción del egreso',
        tipo: 'danger'
      });
    cerrar_loader();
  }

  function eliminar_pago_servicio(cod_caja, caja, item, cod_servicio, cod_unico) {
    document.getElementById('div_loader').style.display = 'block';
    $.ajax({
      type: "POST",
      data: "cod_caja=" + cod_caja + "&caja=" + caja + "&item=" + item + "&cod_servicio=" + cod_servicio + "&cod_unico=" + cod_unico,
      url: "procesos/eliminar_pago_servicio.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          w_alert({
            titulo: 'Pago Eliminado',
            tipo: 'success'
          });
          $('#div_cierre_caja').load('paginas/detalles/caja.php/?cod_caja=<?php echo $cod_caja ?>&caja=<?php echo $caja ?>', function() {
            cerrar_loader();
          });
        } else
          w_alert({
            titulo: datos['consulta'],
            tipo: 'danger'
          });
        if (datos['consulta'] == 'Reload') {
          document.getElementById('div_login').style.display = 'block';
          cerrar_loader();

        }

        cerrar_loader();
      }
    });
  }

  function eliminar_ingreso(cod_caja, caja, item, cod_unico) {
    document.getElementById('div_loader').style.display = 'block';
    $.ajax({
      type: "POST",
      data: "cod_caja=" + cod_caja + "&caja=" + caja + "&item=" + item + "&cod_unico=" + cod_unico,
      url: "procesos/eliminar_ingreso.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          w_alert({
            titulo: 'Ingreso Eliminado',
            tipo: 'success'
          });
          $('#div_cierre_caja').load('paginas/detalles/caja.php/?cod_caja=<?php echo $cod_caja ?>&caja=<?php echo $caja ?>', function() {
            cerrar_loader();
          });
        } else
          w_alert({
            titulo: datos['consulta'],
            tipo: 'danger'
          });
        if (datos['consulta'] == 'Reload') {
          document.getElementById('div_login').style.display = 'block';
          cerrar_loader();

        }

        cerrar_loader();
      }
    })
  };

  $('#btn_imprimir_cierre').click(function() {
    //document.getElementById('div_loader').style.display = 'block';
    $.ajax({
      type: "POST",
      data: "cod_caja=<?php echo $cod_caja ?>&caja=<?php echo $caja ?>",
      url: "procesos/generar_cierre_pdf.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          ruta = datos['ruta_pdf'];
          imprimir_PDF(ruta);
          w_alert({
            titulo: 'Impresión Generada',
            tipo: 'success'
          });
        } else {
          w_alert({
            titulo: datos['consulta'],
            tipo: 'danger'
          });
          if (datos['consulta'] == 'Reload') {
            document.getElementById('div_login').style.display = 'block';

          }
        }
        cerrar_loader();
      }
    });

  });
</script>