<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();
session_start();

$cod_pedido = $_GET['cod_pedido'];
$area = $_GET['area'];
$orden = $_GET['orden'];
$tiempo_alerta = 0;

$sql = "SELECT `codigo`, `productos`, `mesa`, `solicitante`, `fecha_registro`, `fecha_envio`, `fecha_entrega`, `estado`, `area`, `respuesta` FROM `pedidos` WHERE codigo='$cod_pedido'";
$result = mysqli_query($conexion, $sql);
$mostrar = mysqli_fetch_row($result);

if ($mostrar != null) {

  $sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Alerta Pedido'";
  $result = mysqli_query($conexion, $sql);
  $ver = mysqli_fetch_row($result);

  if ($ver != null)
    $tiempo_alerta = $ver[2];

  $cod_pedido = $mostrar[0];

  $respuesta = $mostrar[9];

  $cod_mesa = $mostrar[2];

  if ($respuesta == 'VENTA') {
    $nombre_mesa = 'Venta: ' . $cod_mesa;
  } else {
    $sql_mesa = "SELECT `cod_mesa`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_apertura`, `salon` FROM `mesas` WHERE cod_mesa='$cod_mesa'";
    $result_mesa = mysqli_query($conexion, $sql_mesa);
    $ver_mesa = mysqli_fetch_row($result_mesa);
    if ($ver_mesa != null) {
      $nombre_mesa = 'Mesa: ' . $ver_mesa[1];

      if ($ver_mesa[6] != null) {
        $sql_salon = "SELECT `codigo`, `nombre`, `estado`, `orden` FROM `salones` WHERE codigo='$ver_mesa[6]'";
        $result_salon = mysqli_query($conexion, $sql_salon);
        $ver_salon = mysqli_fetch_row($result_salon);

        if ($ver_salon != null)
          $nombre_mesa .= ' (' . $ver_salon[1] . ')';
      }
    } else {
      $nombre_mesa = 'Mesa: ---';
    }
  }

  $solicitante = $mostrar[3];

  $sql_e = "SELECT nombre, rol, foto FROM usuarios WHERE codigo = '$solicitante'";
  $result_e = mysqli_query($conexion, $sql_e);
  $ver_e = mysqli_fetch_row($result_e);
  if ($ver_e != null)
    $solicitante = $ver_e[0];

  $estado_pedido = $mostrar[7];
  $productos_pedido = array();
  if ($mostrar[1] != '')
    $productos_pedido = json_decode($mostrar[1], true);

  if ($estado_pedido == 'PENDIENTE')
    $bg_card = ''; //'bg-info';
  else
    $bg_card = 'bg-gray';
?>
  <div class="card-header border-bottom p-1 d-flex <?php echo $bg_card ?>">
    <div class="col">
      <h6 class="m-0 text-dark"><b><?php echo $nombre_mesa ?></b></h6>
    </div>
    <div class="col text-right">
      <h6 class="m-0"><?php echo $solicitante ?></h6>
    </div>
  </div>
  <div class="card-body p-0">
    <ul class="list-group list-group-small list-group-flush">
      <?php
      $items_bar = 0;
      $items = 0;
      foreach ($productos_pedido as $i => $producto) {
        $visible = 0;
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

        if ($estado == 'PENDIENTE')
          $bg_tr = 'bg_pendiente';
        else if ($estado == 'PREPARANDO')
          $bg_tr = 'bg_preparando';
        else if ($estado == 'DESPACHADO')
          $bg_tr = 'bg_despachado';
        else if ($estado == 'CANCELADO') {
          $nombre_producto = '<s>' . $nombre_producto . '</s>';
          $bg_tr = 'bg_cancelado';
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


        if ($area_real == 'Bar') {
          if (isset($_SESSION['viewBar'])) {
            if ($_SESSION['viewBar'] == "true")
              $visible = 1;
          }
        }
        if ($area_real == 'Cocina') {
          if (isset($_SESSION['viewCocina'])) {
            if ($_SESSION['viewCocina'] == "true")
              $visible = 1;
          }
        }
        if ($area_real == 'Horno') {
          if (isset($_SESSION['viewHorno'])) {
            if ($_SESSION['viewHorno'] == "true")
              $visible = 1;
          }
        }

        if ($area_real == 'Horno2') {
          if (isset($_SESSION['viewHorno2'])) {
            if ($_SESSION['viewHorno2'] == "true")
              $visible = 1;
          }
        }

        if ($area_real == $area)
          $visible = 1;

        if ($visible == 1) {
          $items++;
      ?>
          <li class="list-group-item d-flex row px-0 m-1 p-1 <?php echo $bg_tr ?>" id="tr_pedido_<?php echo $cod_pedido ?>_<?php echo $i ?>">
            <div class="col-lg-8 col-md-8 col-sm-8 col-8">
              <h6 class="go-stats__label mb-1 text-dark"><span class="fw-bold h4"><?php echo $cant ?></span> - <b><?php echo $nombre_producto ?></b> [$<?php echo number_format($valor_unitario, 0, '.', '.') ?>]</h6>
              <?php
              if ($producto['notas'] != NULL) {
              ?>
                <div class="go-stats__meta pl-3 lh-1">
                  <span class="mr-2">
                    <?php
                    foreach ($notas as $j => $nota) {
                      echo '* ' . $nota . '<br>';
                    }
                    ?>
                  </span>
                </div>
              <?php
              }
              ?>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-4 d-flex" id="col_btn_<?php echo $cod_pedido ?>_<?php echo $i ?>">
              <div class="go-stats__chart d-flex m-auto">
                <?php
                if ($estado == 'PENDIENTE') {
                ?>
                  <button class="btn btn-sm btn-info btn-round ml-1 p-1" id="pre_<?php echo $cod_pedido . '_' . $i ?>" onclick="preparando_pedido('<?php echo $cod_mesa ?>','<?php echo $cod_pedido ?>','<?php echo $i ?>','<?php echo $area ?>','<?php echo $orden ?>')">
                    PREPARAR
                  </button>
                <?php
                }
                if ($estado == 'PREPARANDO') {
                ?>
                  <button class="btn btn-sm btn-success btn-round ml-1 p-1" id="des_<?php echo $cod_pedido . '_' . $i ?>" onclick="pedido_despachado('<?php echo $cod_mesa ?>','<?php echo $cod_pedido ?>','<?php echo $i ?>','<?php echo $area ?>','<?php echo $orden ?>')">
                    DESPACHAR
                  </button>
                <?php
                } else if ($estado == 'CANCELADO')
                  echo '<b>CANCELADO</b>';
                else if ($estado == 'DESPACHADO')
                  echo '<b>DESPACHADO</b>';
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
      <div class="col p-1">
        <?php
        if ($estado_pedido != 'TERMINADO') {
        ?>
          <button class="btn btn-sm btn-info btn-round btn-icon ml-1" id="ter_<?php echo $cod_pedido ?>" onclick="pedido_terminado('<?php echo $cod_pedido ?>','<?php echo $area ?>')">
            TERMINADO
          </button>
        <?php
        }
        ?>

      </div>
      <div class="col-3 p-1">
        <h6 class="text-dark"><b>Orden (<?php echo $orden ?>)</b></h6>
        <div id="orden_pedido_<?php echo $cod_pedido ?>" hidden><?php echo $orden ?></div>
      </div>
    </div>
  </div>
  <?php
  if ($estado_pedido != 'TERMINADO') {
    $fecha_pedido = date("M d Y G:i:s", strtotime($mostrar[5]));
  ?>
    <div class="card-footer border-top py-1">
      <span class="mb-0 m-3" id="tiempo_p_<?php echo $cod_pedido ?>"></span>
      <span class="notification_alert badge bg-danger" style="display: none;" id="alerta_pedido_<?php echo $cod_pedido ?>">[TIEMPO SOBREPASADO]</span>
    </div>
    <script type="text/javascript">
      countup('<?php echo $fecha_pedido ?> GMT-0500', 'tiempo_p_<?php echo $cod_pedido ?>', 'TERMINADO', '<?php echo $tiempo_alerta ?>', 'alerta_pedido_<?php echo $cod_pedido ?>');
    </script>
  <?php
  }

  if ($items == 0) {
  ?>
    <script type="text/javascript">
      $('#div_card_<?php echo $cod_pedido ?>').hide();
    </script>
<?php
  }
}
