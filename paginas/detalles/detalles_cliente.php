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
}

$cod_cliente = $_GET['cod_cliente'];

$sql = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro`, `tipo` FROM `clientes` WHERE codigo = '$cod_cliente' order by nombre ASC";
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

$sql_visitas = "SELECT count(`codigo`), `cliente`, `productos`, `pago`, `fecha`, `cobrador` FROM `ventas` WHERE cliente LIKE '$cliente' group by DATE_FORMAT(fecha,'%Y-%m-%d') order by fecha ASC";
$result_visitas = mysqli_query($conexion, $sql_visitas);

$sql_compras = "SELECT `codigo`, `cliente`, `productos`, `pago`, `fecha`, `cobrador` FROM `ventas` WHERE cliente LIKE '$cliente' order by fecha ASC";
$result_compras = mysqli_query($conexion, $sql_compras);


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
      <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left">
        <b> <?php echo $tipo ?> </b>
        <?php
        if ($rol == 'Administrador') {
        ?>
          <button class="btn btn-sm btn-outline-primary btn-round p-0 px-1" onclick="cambiar_tipo_cliente(<?php echo $cod_cliente ?>)">
            <span class="fa fa-exchange-alt"></span>
          </button>
        <?php
        } ?>
      </span>
    </p>
  </div>

  <div class="mt-4 py-2 border-top border-bottom">
    <ul class="nav nav-tabs" role="tablist">
      <li class="nav-item">
        <a class="nav-link active text-gray" id="compras-tab" data-bs-toggle="tab" href="#compras" role="tab" aria-controls="compras" aria-selected="true">Compras</a>
      </li>
    </ul>
  </div>
  <div class="tab-content">
    <div class="tab-pane active show p-2" id="compras" role="tabpanel" aria-labelledby="compras-tab">
      <div class="table-responsive text-dark text-center py-0 px-1">
        <table class="table text-dark table-sm Data_Table" id="tabla_compras">
          <thead>
            <tr class="text-center">
              <th>Cod</th>
              <th>Cliente</th>
              <th>Fecha</th>
              <th>Total</th>
              <th>Creador</th>
              <th></th>
            </tr>
          </thead>
          <tbody class="overflow-auto">
            <?php
            $total_compras = 0;
            while ($mostrar_compras = mysqli_fetch_row($result_compras)) {
              $cod_venta = $mostrar_compras[0];

              $total = 0;
              $cliente = json_decode($mostrar_compras[1], true);
              $pagos = json_decode($mostrar_compras[3], true);

              $productos_venta = json_decode($mostrar_compras[2], true);
              foreach ($productos_venta as $i => $producto)
                $total += $producto['valor_unitario'] * $producto['cant'];

              $cobrador = $mostrar_compras[5];

              $sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$cobrador'";
              $result_e = mysqli_query($conexion, $sql_e);
              $ver_e = mysqli_fetch_row($result_e);

              $cobrador = $ver_e[0];

              $fecha_venta = strftime("%A, %e %b %Y", strtotime($mostrar_compras[4]));
              $fecha_venta = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_venta));

              $fecha_venta .= date(' | h:i A', strtotime($mostrar_compras[4]));
            ?>
              <tr>
                <td class="text-center"><?php echo str_pad($mostrar_compras[0], 3, "0", STR_PAD_LEFT) ?></td>
                <td><?php echo $cliente['nombre'] ?></td>
                <td><?php echo $fecha_venta ?></td>
                <td class="text-right"><strong>$<?php echo number_format($total, 0, '.', '.') ?></strong></td>
                <td class="text-center"><?php echo $cobrador ?></td>
                <td class="text-center p-1">
                  <button class="btn btn-sm btn-outline-primary btn-round btn-icon" data-bs-toggle="modal" data-bs-target="#Modal_venta" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_modal_venta').load('paginas/detalles/detalles_venta.php/?cod_venta=<?php echo $cod_venta ?>', function(){cerrar_loader();});">
                    <span class="fa fa-search"></span>
                  </button>
                </td>
              </tr>
            <?php
              $total_compras += $total;
            }
            ?>
          </tbody>
        </table>
      </div>
      <div class="row float-right mt-3">
        <h3>Total: $<?php echo number_format($total_compras, 0, '.', '.') ?></h3>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function cambiar_tipo_cliente(cod_cliente) {
    document.getElementById('div_loader').style.display = 'block';

    $.ajax({
      type: "POST",
      data: "cod_cliente=" + cod_cliente,
      url: "procesos/cambiar_tipo_cliente.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          w_alert({
            titulo: 'Cliente cambiado correctamente',
            tipo: 'success'
          });
          $('#div_detalles_cliente').load('paginas/detalles/detalles_cliente.php/?cod_cliente=' + cod_cliente, function() {
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