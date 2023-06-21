<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$cod_usuario = $_GET['cod_usuario'];

$sql = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `foto`, `telefono`, `rol`, `fecha_registro`, `estado` , `comisiones`, `costos` FROM `usuarios` WHERE codigo = '$cod_usuario'";
$result = mysqli_query($conexion, $sql);
$mostrar = mysqli_fetch_row($result);

$cedula = $mostrar[1];
$nombre = $mostrar[2];
$apellido = $mostrar[3];
$telefono = $mostrar[6];
$rol = $mostrar[7];
$costos = $mostrar[11];

$usuario = '%"codigo":"' . $cod_usuario . '"%';

$sql_produc_carta = "SELECT count(`codigo`), `usuario`, `productos`, `pago`, `fecha`, `cobrador` FROM `ventas` WHERE usuario LIKE '$usuario' group by DATE_FORMAT(fecha,'%Y-%m-%d') order by fecha ASC";
$result_produc_carta = mysqli_query($conexion, $sql_produc_carta);

$comisiones = array();
if ($mostrar[10] != '')
  $comisiones = json_decode($mostrar[10], true);

?>
<div class="modal-header text-center">
  <h5 class="modal-title">Detalles de usuario</h5>
</div>
<div class="modal-body p-2">
  <p class="row mb-0">
    <span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right"> Nombre: </span>
    <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $nombre . ' ' . $apellido ?> </b></span>
  </p>
  <p class="row mb-0">
    <span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right"> Cédula: </span>
    <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $cedula ?> </b></span>
  </p>
  <p class="row mb-0">
    <span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right"> Teléfono: </span>
    <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $telefono ?> </b></span>
  </p>
  <p class="row mb-0">
    <span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right"> Rol: </span>
    <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $rol ?> </b></span>
  </p>
  <div class="row mb-0">
    <span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right"> Ver Costos: </span>
    <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left">
      <div class="form-check form-switch">
        <input class="form-check-input " id="SwitchCheckC_costos" type="checkbox" onchange="guardar_costos('costos='+this.checked)" <?php if ($costos == 'true') echo 'checked'; ?>>
        <label class="form-check-label" for="SwitchCheck_costos"></label>
      </div>
    </span>
  </div>

  <div class="pt-0">
    <div class="mt-4 py-2 border-top border-bottom">
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active text-gray" id="comisiones-tab" data-bs-toggle="tab" href="#comisiones" role="tab" aria-controls="comisiones" aria-selected="false">Comisiones</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-gray" id="pagos-tab" data-bs-toggle="tab" href="#pagos" role="tab" aria-controls="pagos" aria-selected="false">Pagos</a>
        </li>
      </ul>
    </div>
    <div class="tab-content">
      <div class="tab-pane active show" id="comisiones" role="tabpanel" aria-labelledby="comisiones-tab">
        <div class="text-center">
          <h5 class="text-center">Lista de comisiones pendientes por pagar
            <button class="btn btn-sm btn-outline-primary btn-round" data-bs-toggle="modal" data-bs-target="#Modal_confirmacion_pagar" onclick="$('#cod_usuario_confirm').val(<?php echo $cod_usuario ?>);document.getElementById('Modal_Ver').classList.remove('show');">Pagar</button>
          </h5>
        </div>
        <div class="table-responsive text-dark text-center py-0 px-1">
          <table class="table text-dark table-sm text-sm" id="tabla_gastos">
            <thead>
              <tr class="text-center">
                <th class="p-1">Cod</th>
                <th class="p-1">Producto</th>
                <th class="p-1" width="120px">Valor Comisión</th>
                <th class="p-1" width="120px">Cant</th>
                <th class="p-1" width="120px">Valor Total</th>
                <th class="p-1" width="120px">Fecha</th>
              </tr>
            </thead>
            <tbody class="overflow-auto">
              <?php
              $total_comisiones = 0;

              foreach ($comisiones as $c => $comision) {
                $cod_producto = $comision['cod_producto'];
                $num_inv = $comision['num_inv'];
                $cant = $comision['cant'];
                $descripcion = $comision['descripcion'];
                $valor_unitario = $comision['valor_unitario'];
                $porcentaje = $comision['porcentaje'];
                $total_comision = $comision['total_comision'];

                $fecha_comision = strftime("%A, %e %b %Y", strtotime($comision['fecha']));
                $fecha_comision = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_comision));
              ?>
                <tr>
                  <td class="p-1 text-center"><?php echo str_pad($cod_producto, 3, "0", STR_PAD_LEFT) ?></td>
                  <td class="p-1"><?php echo $descripcion ?></td>
                  <td class="p-1 text-right"><strong>$<?php echo number_format($valor_unitario * $porcentaje, 0, '.', '.') ?></strong></td>
                  <td class="p-1 text-center"><?php echo $cant ?></td>
                  <td class="p-1 text-right"><strong>$<?php echo number_format($total_comision, 0, '.', '.') ?></strong></td>
                  <td class="p-1 text-center"><?php echo $fecha_comision ?></td>
                </tr>
              <?php
                $total_comisiones += $total_comision;
              }
              ?>
            </tbody>
          </table>
          <div class="row float-right mt-3">
            <h3>Total Comisiones: $<?php echo number_format($total_comisiones, 0, '.', '.') ?></h3>
          </div>
        </div>
      </div>

      <div class="tab-pane fade" id="pagos" role="tabpanel" aria-labelledby="pagos-tab" hidden>
        <div class="text-center">
          <h5 class="text-center">Pagos</h5>
        </div>
        <div class="table-responsive text-dark text-center py-0 px-1">
          <table class="table text-dark table-sm" id="tabla_gastos">
            <thead>
              <tr class="text-center">
                <th class="p-1" width="50px">Cod</th>
                <th class="p-1" width="120px">Total Comisión</th>
                <th class="p-1" width="120px">Creador</th>
                <th class="p-1" width="120px">Fecha</th>
                <th class="p-1" width="50px"></th>
              </tr>
            </thead>
            <tbody class="overflow-auto">
              <?php
              //while ($mostrar_pagos = mysqli_fetch_row($result_pagos)) {
                while(0){
                $total_comision = 0;
                $cod_pago = $mostrar_pagos[0];
                $creador = $mostrar_pagos[3];

                $comisiones = array();
                if ($mostrar_pagos[1] != '')
                  $comisiones = json_decode($mostrar_pagos[1], true);

                foreach ($comisiones as $j => $comision)
                  $total_comision += $comision['total_comision'];

                $fecha_comision = strftime("%A, %e %b %Y", strtotime($mostrar_pagos[4]));
                $fecha_comision = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_comision));

                $sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
                $result_e = mysqli_query($conexion, $sql_e);
                $ver_e = mysqli_fetch_row($result_e);
                $nombre_aux = explode(' ', $ver_e[0]);
                $apellido_aux = explode(' ', $ver_e[1]);
                $creador = $nombre_aux[0] . ' ' . $apellido_aux[0];

              ?>
                <tr>
                  <td class="p-1 text-center"><?php echo str_pad($cod_pago, 3, "0", STR_PAD_LEFT) ?></td>
                  <td class="p-1 text-right"><strong>$<?php echo number_format($total_comision, 0, '.', '.') ?></strong></td>
                  <td class="p-1 text-center"><?php echo $creador ?></td>
                  <td class="p-1 text-center"><?php echo $fecha_comision ?></td>
                  <td class="p-1 text-center">
                    <button class="btn btn-outline-info btn-round p-1" onclick="imprimir_pago('<?php echo $cod_pago ?>')">
                      <i class="material-icons-two-tone">print</i>
                    </button>
                  </td>
                </tr>
              <?php
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  function imprimir_pago(cod_pago) {
    document.getElementById('div_loader').style.display = 'block';
    $.ajax({
      type: "POST",
      data: "cod_pago=" + cod_pago,
      url: "procesos/generar_ticket_pago.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          ruta_pdf = datos['ruta_pdf'];

          $("#Modal_ticket").modal('show');
          $('#contenedor_pdf').load('paginas/detalles/ver_ticket_pdf.php/?ruta=' + ruta_pdf + '&imprimir=1', function() {
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
          cerrar_loader()
        }
      }
    });
  }

  function guardar_costos(datos) {
    document.getElementById('div_loader').style.display = 'block';
    $.ajax({
      type: "POST",
      data: datos+'&cod_usuario=<?php echo $cod_usuario ?>',
      url: "procesos/guardar_check_costos.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          document.getElementById('div_loader').style.display = 'block';
          $('#div_modal_usuario').load('paginas/detalles/detalles_usuario.php/?cod_usuario=<?php echo $cod_usuario ?>', function() {
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
          cerrar_loader()
        }
      }
    });
  }
</script>