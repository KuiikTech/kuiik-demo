<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();
$conexion_1 = $obj->conexion_m1();
$conexion_2 = $obj->conexion_m2();
$conexion = $obj->conexion();

$fecha_inicial = date('Y-m-d 00:00:00', strtotime($_GET['fecha_inicial']));
$fecha_final = date('Y-m-d 23:59:59', strtotime($_GET['fecha_final']));

$sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `local`, `fecha_registro` FROM `servicios` WHERE fecha_entrega_real BETWEEN '$fecha_inicial' AND '$fecha_final' AND estado = 'ENTREGADO' order by fecha_registro ASC";
$result = mysqli_query($conexion, $sql);

$servicios = array();

$fecha_inicial = date('d/m/y', strtotime($_GET['fecha_inicial']));
$fecha_final = date('d/m/y', strtotime($_GET['fecha_final']));

$periodo = $fecha_inicial . ' - ' . $fecha_final;

?>
<div class="text-center p-2">
  <h5 class="text-center">Informe de Servicios</h5>
  <hr class="my-1">
</div>
<div class="p-2">
  <div class="row">
    <p class="row mb-0">
      <span class="text-left"> Periodo: <b> <?php echo $periodo ?> </b></span>
    </p>

    <div class="row m-0 mt-1" id="div_tabla_servicios">
      <div class="border-top text-center px-2">
        <table class="table text-dark table-sm Data_Table" id="tabla_servicios">
          <thead>
            <tr class="text-center">
              <th>#</th>
              <th>Servicio</th>
              <th>Cliente</th>
              <th width="120px">Total</th>
              <th width="120px">Repuestos</th>
              <th width="120px">Accesorios</th>
              <th>Utilidad</th>
              <th>Margen</th>
              <th></th>
            </tr>
          </thead>
          <tbody class="overflow-auto">
            <?php
            $venta_total = 0;
            $costos_totales = 0;
            $utilidad_total = 0;
            $num_item = 1;
            while ($mostrar = mysqli_fetch_row($result)) {
              $cod_servicio = $mostrar[0];

              $informacion = array();
              if ($mostrar[4] != '') {
                $informacion = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[4]);
                $informacion = str_replace('  ', ' ', $informacion);
                $informacion = json_decode($informacion, true);
              }

              if (isset($informacion['total_servicios']))
                $total_servicio = $informacion['total_servicios'];
              else
                $total_servicio = 0;

              if ($mostrar[2] != '') {
                $cliente = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[2]);
                $cliente = str_replace('  ', ' ', $cliente);
                $cliente = json_decode($cliente, true);
              }
              $cod_cliente = $cliente['codigo'];

              $sql_cliente = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro` FROM `clientes` WHERE `codigo`='$cod_cliente'";
              $result_cliente = mysqli_query($conexion, $sql_cliente);
              $cliente_2 = $result_cliente->fetch_object();

              if ($cliente_2 != null) {
                $cliente = json_encode($cliente_2, JSON_UNESCAPED_UNICODE);
                $cliente = json_decode($cliente, true);
              } else {
                $cliente['direccion'] = '';
                $cliente['correo'] = '';
              }

              $accesorios = array();
              if ($mostrar[6] != '') {
                $accesorios = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[6]);
                $accesorios = str_replace(' ', ' ', $accesorios);
                $accesorios = json_decode($accesorios, true);
              }

              $total_accesorios = 0;
              $valor_accesorios = 0;
              foreach ($accesorios as $i => $accesorio) {
                $cant = $accesorio['cant'];
                $costo_unitario = $accesorio['costo_unitario'];
                $valor_unitario = $accesorio['valor_unitario'];

                $costo_total = $cant * $costo_unitario;
                $total_accesorios += $costo_total;

                $valor_total = $cant * $valor_unitario;
                $valor_accesorios += $valor_total;
              }
              $repuestos = array();
              if ($mostrar[5] != '') {
                $repuestos = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[5]);
                $repuestos = str_replace('  ', ' ', $repuestos);
                $repuestos = json_decode($repuestos, true);
              }

              $total_repuestos = 0;
              foreach ($repuestos as $i => $repuesto) {
                $cant = $repuesto['cant'];
                if (isset($repuesto['costo_unitario'])) {
                  $costo = $repuesto['costo_unitario'];
                  $costo_total = $cant * $costo;
                  $total_repuestos += $costo_total;
                }
              }

              $total_servicio = intval($total_servicio) + intval($valor_accesorios);

              $utilidad = $total_servicio - $total_repuestos - $total_accesorios;

              if ($total_servicio == 0)
                $margen = 0;
              else
                $margen = round(($utilidad / $total_servicio) * 100, 2);


              $text_utilidad = '';
              if ($utilidad < 0)
                $text_utilidad = 'text-danger';

              $venta_total += $total_servicio;
              $costos_totales += $total_repuestos + $total_accesorios;
              $utilidad_total += $utilidad;

            ?>
              <tr>
                <td class="text-center"><?php echo $num_item ?></td>
                <td class="text-center"><?php echo str_pad($cod_servicio, 3, "0", STR_PAD_LEFT) ?></td>
                <td class="text-left"><?php echo ucwords(strtolower($cliente['nombre'])) ?></td>
                <td class="text-right"><strong>$<?php echo number_format($total_servicio, 0, '.', '.') ?></strong></td>
                <td class="text-right"><strong>$<?php echo number_format($total_repuestos, 0, '.', '.') ?></strong></td>
                <td class="text-right"><strong>$<?php echo number_format($total_accesorios, 0, '.', '.') ?></strong></td>
                <td class="text-right <?php echo $text_utilidad ?>"><strong>$<?php echo number_format($utilidad, 0, '.', '.') ?></strong></td>
                <td class="text-right"><?php echo $margen ?> %</td>
                <td class="text-center">
                  <button class="btn btn-outline-primary btn-round p-0 px-1" onclick="mostrar_servicio('<?php echo $cod_servicio ?>')">
                    <span class="fa fa-search"></span>
                  </button>
                </td>
              </tr>
            <?php
              $num_item++;
            }

            $margen_total = intval(($utilidad_total / $venta_total) * 100);

            $fecha_inicial = date('Y-m-d 00:00:00', strtotime($_GET['fecha_inicial']));
            $fecha_final = date('Y-m-d 23:59:59', strtotime($_GET['fecha_final']));

            $sql = "(SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `finalizador`, `egresos`, `info`, `kilos_fin` FROM `caja` WHERE (`fecha_apertura` BETWEEN '$fecha_inicial' AND '$fecha_final') AND (`fecha_cierre` BETWEEN '$fecha_inicial' AND '$fecha_final'))
            UNION ALL (SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `finalizador`, `egresos`, `info`, `kilos_fin` FROM `caja2` WHERE (`fecha_apertura` BETWEEN '$fecha_inicial' AND '$fecha_final') AND (`fecha_cierre` BETWEEN '$fecha_inicial' AND '$fecha_final'))
            UNION ALL (SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `finalizador`, `egresos`, `info`, `kilos_fin` FROM `caja3` WHERE (`fecha_apertura` BETWEEN '$fecha_inicial' AND '$fecha_final') AND (`fecha_cierre` BETWEEN '$fecha_inicial' AND '$fecha_final'))";
            $result = mysqli_query($conexion_1, $sql);

            $totales_ingresos = array();
            $resultado1 = 0;

            while ($mostrar = mysqli_fetch_row($result)) {
              $servicios = array();
              if ($mostrar[16] != NULL)
                $servicios = json_decode($mostrar[16], true);

              foreach ($servicios as $i => $ingreso) {
                $metodo = $ingreso['metodo'];
                $valor = $ingreso['valor'];

                $fecha_ingreso = date('Y-m-d', strtotime($ingreso['fecha']));

                if (isset($totales_ingresos[$metodo]))
                  $totales_ingresos[$metodo] += $valor;
                else
                  $totales_ingresos[$metodo] = $valor;

                $resultado1 += $valor;
              }
            }
            ?>
          </tbody>
        </table>
        <table class="table text-dark table-sm Data_Table" id="tabla_info">
          <thead>
            <tr class="text-center">
              <th class="text-right">Venta total</th>
              <th class="text-right">Costo total</th>
              <th class="text-right">Utilidad Total</th>
              <th class="text-right">%</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="text-right h4"><strong>$<?php echo number_format($venta_total, 0, '.', '.') ?></strong></td>
              <td class="text-right h4 text-warning"><strong>$<?php echo number_format($costos_totales, 0, '.', '.') ?></strong></td>
              <td class="text-right h4 text-success"><strong>$<?php echo number_format($utilidad_total, 0, '.', '.') ?></strong></td>
              <td class="text-right"><?php echo $margen_total ?> %</td>
            </tr>
          </tbody>
        </table>

        <table class="table text-dark table-sm Data_Table" id="tabla_ingresos1">
          <tbody>
            <tr>
              <td class="text-right h4" colspan="2"><strong>Ingresos totales MOVILAB 1</strong></td>
            </tr>
            <?php
            foreach ($totales_ingresos as $metodo => $valor) {
            ?>
              <tr>
                <td class="text-right"><?php echo $metodo ?></td>
                <td class="text-right"><strong>$<?php echo number_format($valor, 0, '.', '.') ?></strong></td>
              </tr>
            <?php
            }
            ?>
            <tr>
              <td class="text-right"><strong>Total M1</strong></td>
              <td class="text-right"><strong>$<?php echo number_format($resultado1, 0, '.', '.') ?></strong></td>
            </tr>
          </tbody>
        </table>

        <?php

        $fecha_inicial = date('Y-m-d 00:00:00', strtotime($_GET['fecha_inicial']));
        $fecha_final = date('Y-m-d 23:59:59', strtotime($_GET['fecha_final']));

        $sql = "(SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `finalizador`, `egresos`, `info`, `kilos_fin` FROM `caja` WHERE (`fecha_apertura` BETWEEN '$fecha_inicial' AND '$fecha_final') AND (`fecha_cierre` BETWEEN '$fecha_inicial' AND '$fecha_final'))
            UNION ALL (SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `finalizador`, `egresos`, `info`, `kilos_fin` FROM `caja2` WHERE (`fecha_apertura` BETWEEN '$fecha_inicial' AND '$fecha_final') AND (`fecha_cierre` BETWEEN '$fecha_inicial' AND '$fecha_final'))
            UNION ALL (SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `finalizador`, `egresos`, `info`, `kilos_fin` FROM `caja3` WHERE (`fecha_apertura` BETWEEN '$fecha_inicial' AND '$fecha_final') AND (`fecha_cierre` BETWEEN '$fecha_inicial' AND '$fecha_final'))";
        $result = mysqli_query($conexion_2, $sql);

        $totales_ingresos = array();
        $resultado2 = 0;

        while ($mostrar = mysqli_fetch_row($result)) {
          $servicios = array();
          if ($mostrar[16] != NULL)
            $servicios = json_decode($mostrar[16], true);

          foreach ($servicios as $i => $ingreso) {
            $metodo = $ingreso['metodo'];
            $valor = $ingreso['valor'];

            $fecha_ingreso = date('Y-m-d', strtotime($ingreso['fecha']));

            if (isset($totales_ingresos[$metodo]))
              $totales_ingresos[$metodo] += $valor;
            else
              $totales_ingresos[$metodo] = $valor;

            $resultado2 += $valor;
          }
        }
        ?>
        <table class="table text-dark table-sm Data_Table" id="tabla_ingresos2">
          <tbody>
            <tr>
              <td class="text-right h4" colspan="2"><strong>Ingresos totales MOVILAB 2</strong></td>
            </tr>
            <?php
            foreach ($totales_ingresos as $metodo => $valor) {
            ?>
              <tr>
                <td class="text-right"><?php echo $metodo ?></td>
                <td class="text-right"><strong>$<?php echo number_format($valor, 0, '.', '.') ?></strong></td>
              </tr>
            <?php
            }
            ?>
            <tr>
              <td class="text-right"><strong>Total M2</strong></td>
              <td class="text-right"><strong>$<?php echo number_format($resultado2, 0, '.', '.') ?></strong></td>
            </tr>
            <tr>
              <td class="text-right h4 bg-success text-white" colspan="2"><strong>Totales M1+M2: $<?php echo number_format($resultado1 + $resultado2, 0, '.', '.') ?></strong></td>
            </tr>
            <tr>
              <td class="text-right h5" colspan="2"><strong>Margen M1+M2: $<?php echo number_format((($resultado1 + $resultado2) * $margen_total) / 100, 0, '.', '.') ?></strong></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal Ver-->
<div class="modal fade" id="Modal_Ver" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content" id="div_modal_producto">
    </div>
  </div>
</div>

<!-- Modal Editar Stock-->
<div class="modal fade" id="Modal_Edit_Stock" tabindex="0" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" id="div_edit_stock"></div>
  </div>
</div>