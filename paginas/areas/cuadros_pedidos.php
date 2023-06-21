<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$sql_caja = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `egresos`, `creador`, `cajero`, `finalizador`, `estado`, `info`, `kilos_inicio`, `kilos_fin` FROM `caja` WHERE estado = 'ABIERTA'";
$result_caja = mysqli_query($conexion, $sql_caja);
$mostrar_caja = mysqli_fetch_row($result_caja);

if ($mostrar_caja != null) {
  $fecha_inicio = $mostrar_caja[2];

  $area = $_GET['area'];

  $sql = "SELECT `codigo`, `productos`, `mesa`, `solicitante`, `fecha_registro`, `fecha_envio`, `fecha_entrega`, `estado`, `area` FROM `pedidos` WHERE fecha_registro > '$fecha_inicio' AND estado != 'DESPACHADO' AND estado != 'CANCELADO' AND estado != 'TERMINADO' ORDER BY FIELD(estado,'PREPARANDO','PENDIENTE'), fecha_registro ASC";
  $result = mysqli_query($conexion, $sql);

  $orden = 1;
?>
  <div class="contenedor_pedidos">
    <?php
    while ($mostrar = mysqli_fetch_row($result)) {
      $btn_terminado = 'disabled=""';
      $cod_pedido = $mostrar[0];
      $cod_mesa = $mostrar[2];
      $sql_mesa = "SELECT `cod_mesa`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_apertura` FROM `mesas` WHERE cod_mesa='$cod_mesa'";
      $result_mesa = mysqli_query($conexion, $sql_mesa);
      $ver_mesa = mysqli_fetch_row($result_mesa);

      $nombre_mesa = $ver_mesa[1];

      $solicitante = $mostrar[3];

      $sql_e = "SELECT nombre, rol, foto FROM usuarios WHERE codigo = '$solicitante'";
      $result_e = mysqli_query($conexion, $sql_e);
      $ver_e = mysqli_fetch_row($result_e);

      $solicitante = $ver_e[0];

      $estado_pedido = $mostrar[7];
      $productos_pedido = array();
      if ($mostrar[1] != '')
        $productos_pedido = json_decode($mostrar[1], true);
      $color_text = '';

      if ($estado_pedido != 'TERMINADO')
        $terminar = 1;

    ?>
      <div class="card item m-1" id="div_card_<?php echo $cod_pedido ?>">
        <div class="card-header border-bottom p-1 d-flex bg-info">
          <div class="col">
            <h6 class="m-0 text-dark">Mesa: <b><?php echo $nombre_mesa ?></b></h6>
          </div>
          <div class="col text-right">
            <h6 class="m-0"><?php echo $solicitante ?></h6>
          </div>
        </div>
        <div class="card-body p-0">
          <ul class="list-group list-group-small list-group-flush">
            <?php
            $items_bar = 0;
            foreach ($productos_pedido as $i => $producto) {
              $visible = 1;
              $notas = array();
              $cant = $producto['cant'];
              $total_producto = $producto['valor_unitario'] * $producto['cant'];
              $nombre_producto = $producto['descripcion'];
              $valor_unitario = $producto['valor_unitario'];
              $area_real = '';
              if (isset($producto['area']))
                $area_real = $producto['area'];

              $fecha_preparando = '';
              $fecha_despachado = '';
              $fecha_cancelado = '';

              $fecha_registro = date("Y-m-d h:i A", strtotime($producto['fecha_registro']));
              if ($producto['fecha_preparando'] != '')
                $fecha_preparando = date("Y-m-d h:i A", strtotime($producto['fecha_preparando']));
              if ($producto['fecha_despachado'] != '')
                $fecha_despachado = date("Y-m-d h:i A", strtotime($producto['fecha_despachado']));
              if ($producto['fecha_cancelado'] != '')
                $fecha_cancelado = date("Y-m-d h:i A", strtotime($producto['fecha_cancelado']));


              $estado = $producto['estado'];

              if ($producto['notas'] != NULL)
                $notas = $producto['notas'];

              $mesero = $producto['creador'];

              $sql_e = "SELECT nombre, apellido, rol, foto FROM usuarios WHERE codigo = '$mesero'";
              $result_e = mysqli_query($conexion, $sql_e);
              $ver_e = mysqli_fetch_row($result_e);

              if ($ver_e != null)
                $mesero = $ver_e[0];

              if ($estado == 'PENDIENTE') {
                $bg_tr = 'bg_pendiente';
                $color_text = 'text-danger';
                $terminar = 1;
              } else if ($estado == 'PREPARANDO') {
                $bg_tr = 'bg_preparando';
                $btn_terminado = 'disabled=""';
                $terminar = 1;
              } else if ($estado == 'DESPACHADO') {
                $bg_tr = 'bg_despachado';
                $btn_terminado = '';
                $terminar = 1;
              } else if ($estado == 'CANCELADO') {
                $nombre_producto = '<s>' . $nombre_producto . '</s>';
                $bg_tr = 'bg_cancelado';
                $btn_terminado = '';
              }

              $preparador = '';
              if (isset($producto['preparador']))
                $preparador = $producto['preparador'];


              $sql_e = "SELECT nombre, apellido, rol, foto FROM usuarios WHERE codigo = '$preparador'";
              $result_e = mysqli_query($conexion, $sql_e);
              $ver_e = mysqli_fetch_row($result_e);

              if ($ver_e != null)
                $preparador = $ver_e[0];

              $despachador = '';
              if (isset($producto['despachador']))
                $despachador = $producto['despachador'];

              $sql_e = "SELECT nombre, apellido, rol, foto FROM usuarios WHERE codigo = '$despachador'";
              $result_e = mysqli_query($conexion, $sql_e);
              $ver_e = mysqli_fetch_row($result_e);

              if ($ver_e != null)
                $despachador = $ver_e[0];

              $cancelador = '';
              if (isset($producto['cancelador']))
                $cancelador = $producto['cancelador'];

              $sql_e = "SELECT nombre, apellido, rol, foto FROM usuarios WHERE codigo = '$cancelador'";
              $result_e = mysqli_query($conexion, $sql_e);
              $ver_e = mysqli_fetch_row($result_e);

              if ($ver_e != null)
                $cancelador = $ver_e[0];

              if ($area == 'Bar') {
                if ($area_real != 'Bar')
                  $visible = 0;
                else
                  $items_bar++;
              }

              if ($visible == 1) {
            ?>
                <li class="list-group-item d-flex row px-0 m-1 p-1 <?php echo $bg_tr ?>">
                  <div class="col-lg-8 col-md-8 col-sm-8 col-8">
                    <h6 class="go-stats__label mb-1 text-dark"><b><?php echo $cant ?></b> -<b><?php echo $nombre_producto ?></b> [$<?php echo number_format($valor_unitario, 0, '.', '.') ?>]</h6>
                    <?php
                    if ($producto['notas'] != NULL) {
                    ?>
                      <div class="go-stats__meta pl-3 lh-1">
                        <span class="mr-2">
                          <?php
                          foreach ($notas as $j => $nota) {
                            echo '<small>* ' . $nota . '</small><br>';
                          }
                          ?>
                        </span>
                      </div>
                    <?php
                    }
                    ?>
                  </div>
                  <div class="col-lg-4 col-md-4 col-sm-4 col-4 d-flex">
                    <div class="go-stats__chart d-flex m-auto">
                      <?php
                      if ($estado == 'PENDIENTE' || $estado == 'PREPARANDO') {
                        if ($estado == 'PENDIENTE') {
                      ?>
                          <button class="btn btn-sm btn-info btn-round ml-1 p-1" id="pre_<?php echo $cod_pedido . '_' . $i ?>" onclick="preparando_pedido('<?php echo $cod_mesa ?>','<?php echo $cod_pedido ?>','<?php echo $i ?>','<?php echo $area ?>')">
                            PREPARAR
                          </button>
                        <?php
                        }
                        if ($estado == 'PREPARANDO') {
                        ?>
                          <button class="btn btn-sm btn-success btn-round ml-1 p-1" id="des_<?php echo $cod_pedido . '_' . $i ?>" onclick="pedido_despachado('<?php echo $cod_mesa ?>','<?php echo $cod_pedido ?>','<?php echo $i ?>','<?php echo $area ?>')">
                            DESPACHAR
                          </button>
                      <?php
                        }
                      } else if ($estado == 'CANCELADO')
                        echo '<b>CANCELADO</b>';

                      ?>
                    </div>
                  </div>
                  <span class="dropdown font-sans-serif btn-reveal-trigger">
                    <a href="#" class="text-dark btn-sm dropdown-toggle dropdown-caret-none" type="button" id="dropdown-weather-update" data-bs-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false"><span class="fas fa-chevron-down fs--2"></span></a>
                    <div class="dropdown-menu dropdown-menu-end border py-2" aria-labelledby="dropdown-weather-update" style="min-width: 40rem !important;">
                      <p class="text-dark m-0">Fecha de Registro: <b><?php echo $fecha_registro ?></b> [<?php echo $mesero ?>]</p>
                      <p class="text-dark m-0">Fecha de Preparación: <b><?php echo $fecha_preparando ?></b> [<?php echo $preparador ?>]</p>
                      <p class="text-dark m-0">Fecha de Despacho: <b><?php echo $fecha_despachado ?></b> [<?php echo $despachador ?>]</p>
                      <p class="text-dark m-0">Fecha de Cancelación: <b><?php echo $fecha_cancelado ?></b> [<?php echo $cancelador ?>]</p>
                    </div>
                  </span>
                </li>
            <?php
              }
            }
            ?>

          </ul>
        </div>
        <div class="card-footer border-top py-1">
          <div class="row">
            <div class="col">
              <?php
              if ($area != 'Bar') {
              ?>
                <button class="btn btn-sm btn-info btn-round btn-icon ml-1" <?php echo $btn_terminado ?> id="ter_<?php echo $cod_pedido ?>" onclick="pedido_terminado('<?php echo $cod_pedido ?>','<?php echo $area ?>')">
                  TERMINADO
                </button>
                <?php
              } else {
                if ($items_bar == 0) {
                ?>
                  <script type="text/javascript">
                    document.getElementById("div_card_<?php echo $cod_pedido ?>").hidden = true;
                  </script>
                <?php
                }
              }
              if (!isset($terminar)) {
                ?>
                <script type="text/javascript">
                  document.getElementById("ter_<?php echo $cod_pedido ?>").click();
                </script>
              <?php
              }
              ?>
            </div>
            <div class="col-2">
              <span class="dot text-warning px-3 pt-2 pb-0">
                <h4 class="text-dark"><b><?php echo $orden ?></b></h4>
              </span>
            </div>
          </div>
        </div>
      </div>
    <?php
      $orden++;
    }
    ?>
  </div>



  <?php
  $sql = "SELECT `codigo`, `productos`, `mesa`, `solicitante`, `fecha_registro`, `fecha_envio`, `fecha_entrega`, `estado`, `area` FROM `pedidos` WHERE fecha_registro> '$fecha_inicio' AND estado = 'TERMINADO' ORDER BY FIELD(estado,'PENDIENTE','DESPACHADO','CANCELADO'), fecha_envio DESC";
  $result = mysqli_query($conexion, $sql);
  ?>
  <hr>
  <div class="row text-center">
    <div class="col">PEDIDOS ENTREGADOS</div>
  </div>
  <div class="contenedor_pedidos">
    <?php
    while ($mostrar = mysqli_fetch_row($result)) {
      $btn_terminado = 'disabled=""';
      $cod_pedido = $mostrar[0];
      $cod_mesa = $mostrar[2];
      $sql_mesa = "SELECT `cod_mesa`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_apertura` FROM `mesas` WHERE cod_mesa='$cod_mesa'";
      $result_mesa = mysqli_query($conexion, $sql_mesa);
      $ver_mesa = mysqli_fetch_row($result_mesa);
      $nombre_mesa = $ver_mesa[1];

      $solicitante = $mostrar[3];

      $sql_e = "SELECT nombre, rol, foto FROM usuarios WHERE codigo = '$solicitante'";
      $result_e = mysqli_query($conexion, $sql_e);
      $ver_e = mysqli_fetch_row($result_e);

      $solicitante = $ver_e[0];

      $estado = $mostrar[7];
      $productos_pedido = json_decode($mostrar[1], true);

      $color_text = '';

    ?>
      <div class="card item m-1" id="div_card_ter_<?php echo $cod_pedido ?>">
        <div class="card-header border-bottom p-1 d-flex bg-gray">
          <div class="col">
            <h6 class="m-0 text-dark">Mesa: <b><?php echo $nombre_mesa ?></b></h6>
          </div>
          <div class="col text-right">
            <h6 class="m-0"><?php echo $solicitante ?></h6>
          </div>
        </div>
        <div class="card-body p-0">
          <ul class="list-group list-group-small list-group-flush">
            <?php
            $items_bar = 0;
            foreach ($productos_pedido as $i => $producto) {
              $visible = 1;
              $notas = array();
              $cant = $producto['cant'];
              $total_producto = $producto['valor_unitario'] * $producto['cant'];
              $nombre_producto = $producto['descripcion'];
              $valor_unitario = $producto['valor_unitario'];

              $estado = $producto['estado'];
              $area_real = '';
              if (isset($producto['area']))
                $area_real = $producto['area'];

              $fecha_preparando = '';
              $fecha_despachado = '';
              $fecha_cancelado = '';

              $fecha_registro = date("Y-m-d h:i A", strtotime($producto['fecha_registro']));
              if ($producto['fecha_preparando'] != '')
                $fecha_preparando = date("Y-m-d h:i A", strtotime($producto['fecha_preparando']));
              if ($producto['fecha_despachado'] != '')
                $fecha_despachado = date("Y-m-d h:i A", strtotime($producto['fecha_despachado']));
              if ($producto['fecha_cancelado'] != '')
                $fecha_cancelado = date("Y-m-d h:i A", strtotime($producto['fecha_cancelado']));

              if ($producto['notas'] != NULL)
                $notas = $producto['notas'];

              $mesero = $producto['creador'];

              $sql_e = "SELECT nombre, apellido, rol, foto FROM usuarios WHERE codigo = '$mesero'";
              $result_e = mysqli_query($conexion, $sql_e);
              $ver_e = mysqli_fetch_row($result_e);

              if ($ver_e != null)
                $mesero = $ver_e[0] . ' ' . $ver_e[1];

              if ($estado == 'PENDIENTE') {
                $bg_tr = 'bg_pendiente';
                $color_text = 'text-danger';
                $terminar = 1;
              } else if ($estado == 'PREPARANDO') {
                $bg_tr = 'bg_preparando';
                $btn_terminado = 'disabled=""';
                $terminar = 1;
              } else if ($estado == 'DESPACHADO') {
                $bg_tr = 'bg_despachado';
                $btn_terminado = '';
                $terminar = 1;
              } else if ($estado == 'CANCELADO') {
                $nombre_producto = '<s>' . $nombre_producto . '</s>';
                $bg_tr = 'bg_cancelado';
                $btn_terminado = '';
              }

              $preparador = '';
              if (isset($producto['preparador']))
                $preparador = $producto['preparador'];


              $sql_e = "SELECT nombre, apellido, rol, foto FROM usuarios WHERE codigo = '$preparador'";
              $result_e = mysqli_query($conexion, $sql_e);
              $ver_e = mysqli_fetch_row($result_e);

              if ($ver_e != null)
                $preparador = $ver_e[0];

              $despachador = '';
              if (isset($producto['despachador']))
                $despachador = $producto['despachador'];

              $sql_e = "SELECT nombre, apellido, rol, foto FROM usuarios WHERE codigo = '$despachador'";
              $result_e = mysqli_query($conexion, $sql_e);
              $ver_e = mysqli_fetch_row($result_e);

              if ($ver_e != null)
                $despachador = $ver_e[0];

              $cancelador = '';
              if (isset($producto['cancelador']))
                $cancelador = $producto['cancelador'];

              $sql_e = "SELECT nombre, apellido, rol, foto FROM usuarios WHERE codigo = '$cancelador'";
              $result_e = mysqli_query($conexion, $sql_e);
              $ver_e = mysqli_fetch_row($result_e);

              if ($ver_e != null)
                $cancelador = $ver_e[0];

              if ($area == 'Bar') {
                if ($area_real != 'Bar')
                  $visible = 0;
                else
                  $items_bar++;
              }

              if ($visible == 1) {
            ?>
                <li class="list-group-item d-flex row px-0 m-1 p-0 <?php echo $bg_tr ?>">
                  <div class="col-lg-8 col-md-8 col-sm-8 col-8">
                    <h6 class="go-stats__label mb-1 text-dark"><b><?php echo $cant ?></b> -<b><?php echo $nombre_producto ?></b> [$<?php echo number_format($valor_unitario, 0, '.', '.') ?>]</h6>
                  </div>
                  <div class="col-lg-4 col-md-4 col-sm-4 col-4 d-flex">
                    <div class="go-stats__chart d-flex m-auto">
                      <?php
                      if ($estado == 'DESPACHADO')
                        echo '<b>DESPACHADO</b>';
                      else if ($estado == 'CANCELADO')
                        echo '<b>CANCELADO</b>';

                      ?>
                    </div>
                  </div>
                  <?php
                  if ($producto['notas'] != NULL) {
                  ?>
                    <div class="go-stats__meta pl-3">
                      <span class="mr-2">
                        <?php
                        foreach ($notas as $i => $nota) {
                          echo '* ' . $nota . '<br>';
                        }
                        ?>
                      </span>
                    </div>
                  <?php
                  }
                  ?>
                  <span class="dropdown font-sans-serif btn-reveal-trigger">
                    <a href="#" class="text-dark btn-sm dropdown-toggle dropdown-caret-none" type="button" id="dropdown-weather-update" data-bs-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false"><span class="fas fa-chevron-down fs--2"></span></a>
                    <div class="dropdown-menu dropdown-menu-end border py-2" aria-labelledby="dropdown-weather-update" style="min-width: 40rem !important;">
                      <p class="text-dark m-0">Fecha de Registro: <b><?php echo $fecha_registro ?></b> [<?php echo $mesero ?>]</p>
                      <p class="text-dark m-0">Fecha de Preparación: <b><?php echo $fecha_preparando ?></b> [<?php echo $preparador ?>]</p>
                      <p class="text-dark m-0">Fecha de Despacho: <b><?php echo $fecha_despachado ?></b> [<?php echo $despachador ?>]</p>
                      <p class="text-dark m-0">Fecha de Cancelación: <b><?php echo $fecha_cancelado ?></b> [<?php echo $cancelador ?>]</p>
                    </div>
                  </span>
                </li>
              <?php
              }
            }

            if ($area == 'Bar') {
              if ($items_bar == 0) {
              ?>
                <script type="text/javascript">
                  document.getElementById("div_card_ter_<?php echo $cod_pedido ?>").hidden = true;
                </script>
            <?php
              }
            }
            ?>
          </ul>
        </div>
      </div>
    <?php
      $orden++;
    }
    ?>
  </div>

  <script type="text/javascript">
    function pedido_despachado(cod_mesa, cod_pedido, num_item, area) {
      document.getElementById('div_loader').style.display = 'block';
      document.getElementById("des_" + cod_pedido + "_" + num_item).disabled = true;
      $.ajax({
        type: "POST",
        data: "cod_pedido=" + cod_pedido + "&num_item=" + num_item + "&cod_mesa=" + cod_mesa,
        url: "../../procesos/despachar_pedido.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            w_alert({
              titulo: 'Pedido despachado',
              tipo: 'success'
            });
            $('#div_contenido').load('cuadros_pedidos.php/?area=' + area, cerrar_loader());
            clearInterval(intervalo_pedidos);
            intervalo_pedidos = setInterval("cambios('Cocina')", 30000);
          } else {
            w_alert({
              titulo: datos['consulta'],
              tipo: 'danger'
            });
            if (datos['consulta'] == 'Reload') {
              document.getElementById('div_login').style.display = 'block';
              cerrar_loader();
              
            }
          }
          document.getElementById("des_" + cod_pedido + "_" + num_item).disabled = false;
          document.getElementById('div_loader').style.display = 'none';
        }
      });
    }

    function preparando_pedido(cod_mesa, cod_pedido, num_item, area) {
      document.getElementById('div_loader').style.display = 'block';
      document.getElementById("pre_" + cod_pedido + "_" + num_item).disabled = true;
      $.ajax({
        type: "POST",
        data: "cod_pedido=" + cod_pedido + "&num_item=" + num_item + "&cod_mesa=" + cod_mesa,
        url: "../../procesos/preparar_pedido.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            w_alert({
              titulo: 'Preparando pedido',
              tipo: 'success'
            });
            $('#div_contenido').load('cuadros_pedidos.php/?area=' + area, cerrar_loader());
            clearInterval(intervalo_pedidos);
            intervalo_pedidos = setInterval("cambios('Cocina')", 30000);
          } else {
            w_alert({
              titulo: datos['consulta'],
              tipo: 'danger'
            });
            if (datos['consulta'] == 'Reload') {
              document.getElementById('div_login').style.display = 'block';
              cerrar_loader();
              
            }
          }
          document.getElementById("pre_" + cod_pedido + "_" + num_item).disabled = false;
          document.getElementById('div_loader').style.display = 'none';
        }
      });
    }

    function pedido_terminado(cod_pedido, area) {
      document.getElementById('div_loader').style.display = 'block';
      document.getElementById("ter_" + cod_pedido).disabled = true;
      $.ajax({
        type: "POST",
        data: "cod_pedido=" + cod_pedido,
        url: "../../procesos/terminar_pedido.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            w_alert({
              titulo: 'Pedido terminado',
              tipo: 'success'
            });
            $('#div_contenido').load('cuadros_pedidos.php/?area=' + area, cerrar_loader());
            clearInterval(intervalo_pedidos);
            intervalo_pedidos = setInterval("cambios('Cocina')", 30000);
          } else {
            w_alert({
              titulo: datos['consulta'],
              tipo: 'danger'
            });
            if (datos['consulta'] == 'Reload') {
              document.getElementById('div_login').style.display = 'block';
              cerrar_loader();
              
            }
          }
          document.getElementById("ter_" + cod_pedido).disabled = false;
          document.getElementById('div_loader').style.display = 'none';
        }
      });
    }
  </script>

<?php
} else {
?>
  <div class="row m-0 p-2">
    <div class="bg-warning p-2 rounded-round text-center">
      <h4 class="text-white mb-0">La caja no se encuenta ABIERTA</h4>
    </div>
  </div>
<?php
}
?>