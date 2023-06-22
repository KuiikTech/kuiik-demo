<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');

require_once "../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$num_tabla = 1;
session_set_cookie_params(7 * 24 * 60 * 60);
session_start();

if (isset($_SESSION['usuario_restaurante'])) {
  $usuario = $_SESSION['usuario_restaurante'];

  $sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
  $result_e = mysqli_query($conexion, $sql_e);
  $ver_e = mysqli_fetch_row($result_e);

  $cedula = $ver_e[1];

  $nombre_usuario = $ver_e[2] . ' ' . $ver_e[3];
  $rol = $ver_e[5];

  require_once "../clases/permisos.php";
  $obj_permisos = new permisos();
  $acceso = $obj_permisos->buscar_permiso($usuario, 'Caja', 'VER');

  if (1) { // ($acceso == 'SI') {
?>

    <div id="div_tabla_caja"></div>

    <!-- Modal Nuevo cierre-->
    <div class="modal fade" id="Modal_Ver" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="overflow-y: scroll;">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="div_cierre_caja">
        </div>
      </div>
    </div>

    <!-- Modal confirmacion cerrar caja-->
    <div class="modal fade" id="Modal_confirmacion_cerrar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow-y: scroll;">
      <div class="row">
        <div class="modal-dialog modal-sm" role="document">
          <div class="modal-content shadow-lg">
            <div class="modal-header text-center p-2 bg-danger">
              <h5 class="modal-title text-white">Está seguro de cerrar la caja?</h5>
            </div>
            <div class="modal-body p-2">
              <div class="row m-0">
                <input type="number" name="cod_caja_confirm" id="cod_caja_confirm" hidden="">
                <input type="number" name="caja_confirm" id="caja_confirm" hidden="">
                <button type="button" class="btn btn-sm btn-outline-secondary btn-round col m-1" onclick="atras_caja_confirm()" id="btn_close_confirm">NO</button>
                <button type="button" class="btn btn-sm btn-outline-primary btn-round col m-1" id="btn_cerrar_caja">SI</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <!-- Modal detalles de venta-->
    <div class="modal fade" id="Modal_venta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="row m-0 p-1">
            <input type="number" name="cod_caja_atras" id="cod_caja_atras" hidden="">
            <input type="number" name="caja_atras" id="caja_atras" hidden="">
            <button onclick="atras_caja()" class="btn btn-sm btn-outline-primary btn-round p-1 m-0" style="width: 100px; height: 32px;">
              <span class="fa fa-chevron-left"></span> Atras
            </button>

          </div>
          <div id="div_modal_venta"></div>
        </div>
      </div>
    </div>

    <!-- Modal Eliminar Gasto-->
    <div class="modal fade" id="Modal_Eliminar_Gasto" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header text-center p-2">
            <h5 class="modal-title">Seguro desea eliminar este Gasto?</h5>
          </div>
          <div class="modal-body">
            <input type="number" name="cod_caja_eliminar_gasto" id="cod_caja_eliminar_gasto" hidden="">
            <input type="number" name="item_eliminar_gasto" id="item_eliminar_gasto" hidden="">
            <div class="row">
              <div class="col text-center">
                <button type="button" class="btn btn-sm btn-secondary btn-round btn-block px-5" data-bs-dismiss="modal" id="close_Modal_Eliminar_Gasto">NO</button>
              </div>
              <div class="col text-center">
                <button type="button" class="btn btn-sm btn-outline-primary btn-round btn-block" id="btnEliminarGasto">SI, Eliminar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Eliminar Recarga-->
    <div class="modal fade" id="Modal_Eliminar_Recarga" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header text-center p-2">
            <h5 class="modal-title">Seguro desea eliminar este Recarga?</h5>
          </div>
          <div class="modal-body">
            <input type="number" name="cod_caja_eliminar_recarga" id="cod_caja_eliminar_recarga" hidden="">
            <input type="number" name="item_eliminar_recarga" id="item_eliminar_recarga" hidden="">
            <div class="row">
              <div class="col text-center">
                <button type="button" class="btn btn-sm btn-secondary btn-round btn-block px-5" data-bs-dismiss="modal" id="close_Modal_Eliminar_Recarga">NO</button>
              </div>
              <div class="col text-center">
                <button type="button" class="btn btn-sm btn-outline-primary btn-round btn-block" id="btnEliminarRecarga">SI, Eliminar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal detalles de factura-->
    <div class="modal fade" id="Modal_ver_caja" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content sombra_modal" id="contenedor_pdf"></div>
      </div>
    </div>

    <script type="text/javascript">
      $(document).ready(function() {
        document.title = 'Caja | Restaurante | W-POS | Kuiik';
        $('.active').removeClass("active")
        document.getElementById('a_caja').classList.add("active");

        document.getElementById('div_loader').style.display = 'block';
        $('#div_tabla_caja').load('tablas/caja.php', function() {
          cerrar_loader();
        });
      });

      $('input.moneda').keyup(function(event) {
        if (event.which >= 37 && event.which <= 40) {
          event.preventDefault();
        }
        $(this).val(function(index, value) {
          return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
        });
      });

      $('#btn_cerrar_caja').click(function() {
        document.getElementById('div_loader').style.display = 'block';
        cod_caja = document.getElementById("cod_caja_confirm").value;
        caja = document.getElementById("caja_confirm").value;
        $.ajax({
          type: "POST",
          data: "cod_caja=" + cod_caja + "&caja=" + caja,
          url: "procesos/cerrar_caja.php",
          success: function(r) {
            datos = jQuery.parseJSON(r);
            if (datos['consulta'] == 1) {
              $('#btn_close_confirm').click();
              $('#div_cierre_caja').load('paginas/detalles/caja.php/?cod_caja=' + cod_caja + '&caja=' + caja, function() {
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
      });

      function atras_caja() {
        cod_caja = document.getElementById("cod_caja_atras").value;
        caja = document.getElementById("caja_atras").value;
        $('#Modal_venta').modal('toggle');
        $('#Modal_Ver').modal('show');
        <?php
        if ($rol == 'Administrador') {
        ?>
          $('#div_cierre_caja').load('paginas/detalles/caja.php/?cod_caja=' + cod_caja + '&caja=' + caja, function() {
            cerrar_loader();
          });
        <?php
        } else {
        ?>
          $('#div_cierre_caja').load('paginas/detalles/caja_mesero.php/?cod_caja=' + cod_caja + '&caja=' + caja, function() {
            cerrar_loader();
          });
        <?php
        }
        ?>
      }

      function atras_caja_confirm() {
        cod_caja = document.getElementById("cod_caja_confirm").value;
        caja = document.getElementById("caja_confirm").value;
        $('#Modal_confirmacion_cerrar').modal('toggle');
        $('#Modal_Ver').modal('show');
        $('#div_cierre_caja').load('paginas/detalles/caja.php/?cod_caja=' + cod_caja + '&caja=' + caja, function() {
          cerrar_loader();
        });
      }

      $('#btnEliminarGasto').click(function() {
        document.getElementById('div_loader').style.display = 'block';
        cod_caja = document.getElementById("cod_caja_eliminar_gasto").value;
        caja = document.getElementById("caja_atras").value;
        item = document.getElementById("item_eliminar_gasto").value;
        $.ajax({
          type: "POST",
          data: "cod_caja=" + cod_caja + "&caja=" + caja + "&item=" + item,
          url: "procesos/eliminar_egreso.php",
          success: function(r) {
            datos = jQuery.parseJSON(r);
            if (datos['consulta'] == 1) {
              w_alert({
                titulo: 'Egreso eliminado con exito',
                tipo: 'success'
              });
              $('#div_cierre_caja').load('paginas/detalles/caja.php/?cod_caja=' + cod_caja + '&caja=' + caja, function() {
                cerrar_loader();
              });
              $('#close_Modal_Eliminar_Gasto').click();
              $('#Modal_Ver').modal('show');
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
        cerrar_loader();
      });

      $('#btnEliminarRecarga').click(function() {
        document.getElementById('div_loader').style.display = 'block';
        cod_caja = document.getElementById("cod_caja_eliminar_recarga").value;
        caja = document.getElementById("caja_atras").value;
        item = document.getElementById("item_eliminar_recarga").value;
        $.ajax({
          type: "POST",
          data: "cod_caja=" + cod_caja + "&caja=" + caja + "&item=" + item,
          url: "procesos/eliminar_recarga.php",
          success: function(r) {
            datos = jQuery.parseJSON(r);
            if (datos['consulta'] == 1) {
              w_alert({
                titulo: 'Recarga eliminada con exito',
                tipo: 'success'
              });
              $('#div_cierre_caja').load('paginas/detalles/caja.php/?cod_caja=' + cod_caja + '&caja=' + caja, function() {
                cerrar_loader();
              });
              $('#close_Modal_Eliminar_Recarga').click();
              $('#Modal_Ver').modal('show');
              setTimeout('$("#recargas-tab").click();', 200);
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
        cerrar_loader();
      });
    </script>


  <?php
  } else
    require_once 'error_403.php';
} else {
  ?>
  <script type="text/javascript">
    document.getElementById('div_login').style.display = 'block';
    cerrar_loader();
    
  </script>
<?php
}
?>