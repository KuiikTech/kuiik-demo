<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

session_start();
$rol = '';
if (isset($_SESSION['usuario_restaurante'])) {
  $usuario = $_SESSION['usuario_restaurante'];

  $sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
  $result_e = mysqli_query($conexion, $sql_e);
  $ver_e = mysqli_fetch_row($result_e);

  $cedula = $ver_e[1];

  $nombre_usuario = $ver_e[2] . ' ' . $ver_e[3];
  $rol = $ver_e[5];
  if ($rol == 'Administrador') {

    $cod_cliente = $_GET['cod_cliente'];

    $sql = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro`, `tipo`, `info` FROM `clientes` WHERE codigo = '$cod_cliente' order by nombre ASC";
    $result = mysqli_query($conexion, $sql);
    $mostrar = mysqli_fetch_row($result);

    $identificacion = $mostrar[1];
    $nombre = $mostrar[2];
    $telefono = $mostrar[3];
    $direccion = $mostrar[4];
    $ciudad = $mostrar[5];
    $correo = $mostrar[6];
    $fecha_registro = $mostrar[7];
    $tipo = $mostrar[8];

    if ($tipo == '')
      $tipo = "Regular";

    $cliente = '%"codigo":"' . $cod_cliente . '"%';

    $info = array(
      'user_creditos' => 'Administrador',
    );

    if ($mostrar[9] != '') {
      $info = json_decode($mostrar[9], true);
    }

    $sql_creditos = "SELECT `codigo`, `cod_cliente`, `cliente`, `descripcion`, `valor`, `fecha_registro`, `fecha_pago`, `fecha_ingreso`, `creador`, `cobrador`, `cajero`, `estado` FROM `cuentas_por_cobrar` WHERE cod_cliente = '$cod_cliente' AND estado = 'EN MORA' order by fecha_registro ASC";
    $result_creditos = mysqli_query($conexion, $sql_creditos);

?>
    <div class="modal-header text-center p-2">
      <h5 class="modal-title">Detalles de cliente</h5>
    </div>
    <div class="modal-body p-2">
      <div class="row px-2">
        <p class="row mb-0">
          <span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Identificación: </span>
          <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $identificacion ?> </b></span>
        </p>
        <p class="row mb-0">
          <span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Nombre: </span>
          <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $nombre ?> </b></span>
        </p>
        <p class="row mb-0">
          <span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Telefono: </span>
          <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $telefono ?> </b></span>
        </p>
        <p class="row mb-0">
          <span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Correo: </span>
          <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $correo ?> </b></span>
        </p>
        <p class="row mb-0">
          <span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Dirección: </span>
          <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $direccion ?> </b></span>
        </p>
        <p class="row mb-0">
          <span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Fecha Registro: </span>
          <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $fecha_registro ?> </b></span>
        </p>

        <p class="row mb-0">
          <span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Tipo: </span>
          <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $tipo ?> </b></span>
        </p>
        <hr class="m-0">
        <p class="row mb-0">
          <span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Facturan créditos: </span>
          <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left" ondblclick="document.getElementById('div_input_user').hidden = false;this.hidden = true;"><b> <?php echo $info['user_creditos'] ?> </b></span>
          <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left" hidden id="div_input_user">
            <select class="form-control fotm-control-sm" name="input_users_creditos" id="input_users_creditos" onchange="cambiar_users_credito(<?php echo $cod_cliente ?>,this.value)">
              <option value="Todos" <?php if ($info['user_creditos'] == 'Todos') echo 'selected' ?>>Todos</option>
              <option value="Administrador" <?php if ($info['user_creditos'] == 'Administrador') echo 'selected' ?>>Administrador</option>
            </select>
          </span>
        </p>
      </div>

      <div class="table-responsive text-dark text-center py-0 px-1">
        <table class="table text-dark table-sm Data_Table" id="tabla_creditos">
          <thead>
            <tr class="text-center">
              <th>Cod</th>
              <th>Fecha</th>
              <th>Total</th>
              <th>Estado</th>
              <th>Creador</th>
              <th></th>
            </tr>
          </thead>
          <tbody class="overflow-auto">
            <?php
            $total_creditos = 0;
            while ($mostrar_creditos = mysqli_fetch_row($result_creditos)) {
              $codigo = $mostrar_creditos[0];

              $total = $mostrar_creditos[4];
              $cliente = json_decode($mostrar_creditos[2], true);
              $pagos = json_decode($mostrar_creditos[3], true);

              $estado = $mostrar_creditos[11];

              if ($estado == 'EN MORA')
                $bg_estado = 'bg-danger text-white';
              if ($estado == 'COBRADO')
                $bg_estado = 'bg-info text-white';
              if ($estado == 'INGRESADO')
                $bg_estado = '';

              $creador = $mostrar_creditos[8];

              $sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
              $result_e = mysqli_query($conexion, $sql_e);
              $ver_e = mysqli_fetch_row($result_e);

              if ($ver_e != null)
                $creador = $ver_e[0];

              $cobrador = $mostrar_creditos[9];

              $sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$cobrador'";
              $result_e = mysqli_query($conexion, $sql_e);
              $ver_e = mysqli_fetch_row($result_e);

              if ($ver_e != null)
                $cobrador = $ver_e[0];

              $fecha_venta = strftime("%A, %e %b %Y", strtotime($mostrar_creditos[5]));
              $fecha_venta = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_venta));

              $fecha_venta .= date(' | h:i A', strtotime($mostrar_creditos[5]));
            ?>
              <tr>
                <td class="text-center"><?php echo str_pad($mostrar_creditos[0], 3, "0", STR_PAD_LEFT) ?></td>
                <td><?php echo $fecha_venta ?></td>
                <td class="text-right"><strong>$<?php echo number_format($total, 0, '.', '.') ?></strong></td>
                <td class="text-center <?php echo $bg_estado ?>"><b><?php echo $estado ?></b></td>
                <td class="text-center"><?php echo $creador ?></td>
                <td class="text-center p-1">
                  <button class="btn btn-sm btn-outline-primary btn-round" data-bs-toggle="modal" data-bs-target="#Modal_ver_cuenta" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_modal_cuenta').load('paginas/detalles/detalles_credito.php/?cod_cuenta=<?php echo $codigo ?>', function(){cerrar_loader();});">
                    <span class="fas fa-search"></span>
                  </button>
                </td>
              </tr>
            <?php
              $total_creditos += $total;
            }
            ?>
          </tbody>
        </table>
      </div>
      <div class="row float-right mt-3">
        <h3>Total: $<?php echo number_format($total_creditos, 0, '.', '.') ?></h3>
      </div>

    </div>

    <script type="text/javascript">
      function cambiar_users_credito(cod_cliente, valor) {
        document.getElementById('div_loader').style.display = 'block';

        $.ajax({
          type: "POST",
          data: "cod_cliente=" + cod_cliente + "&valor=" + valor,
          url: "procesos/cambiar_users_credito.php",
          success: function(r) {
            datos = jQuery.parseJSON(r);
            if (datos['consulta'] == 1) {
              w_alert({
                titulo: 'Usuarios cambiado correctamente',
                tipo: 'success'
              });
              $('#div_detalles_cliente').load('paginas/detalles/detalles_cliente_especial.php/?cod_cliente=' + cod_cliente, function() {
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
    </script>

  <?php
  } else
    require_once '../error_403.php';
} else {
  ?>
  <script>
    document.getElementById('div_login').style.display = 'block';
    cerrar_loader();
  </script>
<?php
}
?>