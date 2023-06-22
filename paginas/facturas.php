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
  $acceso = $obj_permisos->buscar_permiso($usuario, 'Facturas', 'VER');

  if ($acceso == 'SI') {

?>

    <div class="card">
      <div class="card-body">
        <div class="d-sm-flex align-items-center mb-4 text-center">
          <h4 class="card-title">INGRESE UN RANGO DE FECHAS</h4>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group form-group-sm">
              <input type="date" class="form-control" id="fecha_inicial" name="fecha_inicial" value="<?php echo $fecha ?>">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-group-sm">
              <input type="date" class="form-control" id="fecha_final" name="fecha_final" value="<?php echo $fecha ?>">
            </div>
          </div>
          <div class="col-md-4">
            <button type="button" class="btn btn-outline-primary btn-block font-weight-medium auth-form-btn btn-round" id="btn_filtrar">Buscar</button>
          </div>

        </div>
      </div>
    </div>
    <br>

    <div id="contenido"></div>

    <script type="text/javascript">
      $(document).ready(function() {
        document.title = 'Facturas | Restaurante | Kuiik';
        $('.active').removeClass("active")
        document.getElementById('a_facturas').classList.add("active");

        document.getElementById('div_loader').style.display = 'block';
        $('#contenido').load('tablas/facturas.php/?fecha_inicial=<?php echo $fecha ?>&fecha_final=<?php echo $fecha ?>', function() {
          cerrar_loader();
        });
      });

      $('.select2').select2();

      $('input.moneda').keyup(function(event) {
        if (event.which >= 37 && event.which <= 40) {
          event.preventDefault();
        }
        $(this).val(function(index, value) {
          return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
        });
      });

      $('#btn_filtrar').click(function() {
        fecha_inicial = document.getElementById("fecha_inicial").value;
        fecha_final = document.getElementById("fecha_final").value;

        if (fecha_inicial != '') {
          if (fecha_final != '') {
            document.getElementById('div_loader').style.display = 'block';
            $('#contenido').load('tablas/facturas.php/?fecha_inicial=' + fecha_inicial + '&fecha_final=' + fecha_final, function() {
              cerrar_loader();
            });
          } else
            w_alert({
              titulo: 'Ingrese la fecha final',
              tipo: 'danger'
            });
        } else
          w_alert({
            titulo: 'Ingrese la fecha inicial',
            tipo: 'danger'
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