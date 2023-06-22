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

  require_once "../clases/permisos.php";
  $obj_permisos = new permisos();
  $acceso = $obj_permisos->buscar_permiso($usuario, 'Caja Mayor', 'VER');

  if ($acceso == 'SI') {
?>

    <div class="row m-0 card mb-2 p-2">
      <form id="frmnuevo" autocomplete="off">
        <div class="form-group form-group-sm mb-0 row">
          <div class="form-line col-4 p-1">
            <label><span class="requerido">*</span>Descripción:</label>
            <input type="text" class="form-control form-control-sm" id="descripcion_movimiento" name="descripcion_movimiento">
          </div>
          <div class="form-line col-2 p-1">
            <label><span class="requerido">*</span>Valor:</label>
            <input type="text" class="form-control form-control-sm moneda text-right" id="valor_movimiento" name="valor_movimiento">
          </div>
          <div class="form-line col-2 p-1">
            <label><span class="requerido">*</span>Tipo:</label>
            <select class="form-control form-control-sm" id="tipo_movimiento" name="tipo_movimiento">
              <option value="">Selecciona un tipo</option>
              <option value="Ingreso">Ingreso</option>
              <option value="Egreso">Egreso</option>
            </select>
          </div>
          <div class="form-line col-2 p-1">
            <label><span class="requerido">*</span>Tipo:</label>
            <select class="form-control form-control-sm" name="metodo_pago" id="metodo_pago">
              <option value="">Selecciona Método</option>
              <option value="Efectivo">Efectivo</option>
              <option value="Tarjeta">Tarjeta</option>
              <option value="Nequi">Nequi</option>
              <option value="Bancolombia">Bancolombia</option>
              <option value="Daviplata">Daviplata</option>
            </select>
          </div>
          <div class="form-line col-2 pt-3 p-1 text-center">
            <button type="button" class="btn btn-sm btn-outline-info btn-round p-1" id="btn_agregar_movimiento">AGREGAR</button>
          </div>
        </div>
      </form>
    </div>

    <div id="div_tabla_caja"></div>

    <!-- Modal Nuevo cierre-->
    <div class="modal fade" id="Modal_Ver" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="div_cierre_caja">
        </div>
      </div>
    </div>

    <script type="text/javascript">
      $(document).ready(function() {
        document.title = 'Caja Mayor | Restaurante | W-POS | Kuiik';
        $('.active').removeClass("active")
        document.getElementById('a_caja_mayor').classList.add("active");

        document.getElementById('div_loader').style.display = 'block';
        $('#div_tabla_caja').load('tablas/caja_mayor.php', function() {
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

      $('#btn_agregar_movimiento').click(function() {
        document.getElementById('div_loader').style.display = 'block';
        datos = $('#frmnuevo').serialize();
        $.ajax({
          type: "POST",
          data: datos,
          url: "procesos/agregar.php",
          success: function(r) {
            datos = jQuery.parseJSON(r);
            if (datos['consulta'] == 1) {
              $('#frmnuevo')[0].reset();
              w_alert({
                titulo: 'Movimiento agregado Correctamente',
                tipo: 'success'
              });
              document.getElementById('div_loader').style.display = 'block';
              $('#div_tabla_caja').load('tablas/caja_mayor.php', function() {
                cerrar_loader();
              });
            } else
              w_alert({
                titulo: datos['consulta'],
                tipo: 'danger'
              });

            cerrar_loader();
          }
        });
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