<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

session_set_cookie_params(7 * 24 * 60 * 60);
session_start();
if (isset($_SESSION['usuario_restaurante'])) {
  $usuario = $_SESSION['usuario_restaurante'];

  $cantidad = '';
  $indicador = '';
  $indicador_numero = '';

  $sql = "SELECT * FROM `productos` WHERE `estado` != 'ELIMINADO' AND `alerta` != 0 AND `alerta` >= `inventario`";
  $result = mysqli_query($conexion, $sql);
  $dataProducts = mysqli_fetch_all($result, MYSQLI_ASSOC);

  if ($dataProducts != NULL) {
    $dataProducts = json_encode($dataProducts);
    $dataProducts = json_decode($dataProducts, true);
    $cantidad = count($dataProducts);
    $indicador = 'notification-indicator notification-indicator-danger';
    $indicador_numero = '<span class="notification-indicator-number">' . $cantidad . '</span>';
  }

?>

  <a class="nav-link <?php echo $indicador ?> px-0 fa-icon-wait" id="navbarDropdownNotification" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-bell" data-fa-transform="shrink-6" style="font-size: 33px;"></span><?php echo $indicador_numero ?></a>
  <div class="dropdown-menu dropdown-menu-end dropdown-menu-card dropdown-menu-notification" aria-labelledby="navbarDropdownNotification">
    <div class="card card-notification">
      <div class="card-header">
        <div class="row justify-content-between align-items-center">
          <div class="col-auto">
            <h6 class="card-header-title mb-0">Notificaciones</h6>
          </div>
        </div>
      </div>
      <div class="scrollbar-overlay" style="max-height:19rem">
        <div class="list-group list-group-flush fw-normal fs--1">
          <?php
          foreach ($dataProducts as $mostrar) {
            $cod_producto = $mostrar['codigo'];

            if($mostrar['alerta'] > $mostrar['inventario']){
              $notificacion = "El producto <b>" . $mostrar['descripcion'] . "</b> tiene menos de " . $mostrar['alerta'] . " unidades en inventario";
            }else{
              $notificacion = "El producto <b>" . $mostrar['descripcion'] . "</b> está próximo a agotarse";
            }
              

          ?>
            <div class="list-group-item px-2">
              <div class="d-flex">
                <div>
                  <?php echo $notificacion ?>
                  (<b><a class="text-dark" href="javascript:mostrar_producto(<?php echo $cod_producto ?>)">Ver Producto</a></b>)
                </div>
                <div class="dropdown font-sans-serif btn-reveal-trigger">
                  <a href="javascript:ocultar_notificacion('<?php echo $cod_producto ?>')"><span class="fas fa-eye-slash"></span></a>
                </div>
              </div>
            </div>
          <?php
          }
          ?>
        </div>
      </div>
      <div class="card-footer text-center border-top" hidden><a class="card-link d-block" href="#">Ver todas</a></div>
    </div>
  </div>

  <script type="text/javascript">
    function ocultar_notificacion(cod_servicio) {
      $.ajax({
        type: "POST",
        data: "cod_servicio=" + cod_servicio,
        url: "procesos/ocultar_notificacion.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          cargar_notificaciones();
        }
      });
    }

  </script>

<?php
}
?>