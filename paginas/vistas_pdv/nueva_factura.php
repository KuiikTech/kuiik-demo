<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();
$conexion = $obj->conexion();
session_set_cookie_params(7 * 24 * 60 * 60);
session_start();

if (isset($_SESSION['usuario_restaurante'])) {
  $usuario = $_SESSION['usuario_restaurante'];

  require_once "../../clases/permisos.php";
  $obj_permisos = new permisos();
  $acceso = $obj_permisos->buscar_permiso($usuario, 'Facturas', 'CREAR');

  if ($acceso == 'SI') {
    if (isset($_SESSION['cod_cliente_fact']))
      $cod_cliente = $_SESSION['cod_cliente_fact'];
    else
      $cod_cliente = '';

    $cliente = array(
      'codigo' => '',
      'id' => '',
      'nombre' => '',
      'telefono' => ''
    );

    if ($cod_cliente != '') {
      $sql = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro`, `tipo`, `info` FROM `clientes` WHERE codigo = '$cod_cliente'";
      $result = mysqli_query($conexion, $sql);
      $ver = mysqli_fetch_row($result);

      $cliente = array(
        'codigo' => $ver[0],
        'id' => $ver[1],
        'nombre' => $ver[2],
        'telefono' => $ver[3]
      );
    }
?>
    <!-- Tabla Configuración de Mesas -->
    <div class="card">
      <div class="card-body pb-0">
        <div class="d-sm-flex align-items-center mb-4">
          <h4 class="card-title text-center">Nueva Factura</h4>
        </div>
        <div id="div_cliente">
          <div class="text-center row h5 m-0">
            <h4 class="col mt-2">Datos de cliente</h4>
            <div class="btn-float-right">
              <button class="btn btn-sm btn-outline-primary btn-round px-2" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_cliente').load('paginas/detalles/agregar_cliente.php/?new_fact=1', function(){cerrar_loader();});">
                <span class="fa fa-plus"></span>
              </button>
            </div>

          </div>
          <div class="row m-0 border-top pt-3 border-bottom border-3">
            <input type="text" name="cod_cliente" id="cod_cliente" hidden="" value="<?php echo $cod_cliente ?>">
            <p class="row m-0 pl-0">
              <span class="col-4 col-sm-4 col-md-6 col-lg-5 d-flex justify-content-end pr-0">
                <?php
                if ($cod_cliente != '') {
                ?>
                  <a class="p-0 text-primary" href="javascript:seleccionar_cliente_fact('')">
                    <span class="fa fa-times text-danger f-16"></span>
                  </a>
                <?php
                }
                ?>
                Cédula/NIT:
              </span>
              <span class="col-8 col-sm-8 col-md-6 col-lg-7 text-left"><b class="text-truncate w-100" id="b_id_cliente"><?php echo $cliente['id'] ?></b></span>
            </p>
            <p class="row m-0 pl-0">
              <span class="col-4 col-sm-4 col-md-6 col-lg-5 d-flex justify-content-end pr-0"> Nombre: </span>
              <span class="col-8 col-sm-8 col-md-6 col-lg-7 text-left"><b class="text-truncate w-100" id="b_nombre_cliente"><?php echo $cliente['nombre'] . ' ' ?></b></span>
            </p>
            <p class="row m-0 pl-0">
              <span class="col-4 col-sm-4 col-md-6 col-lg-5 d-flex justify-content-end pr-0">
                <a class="p-1" href="javascript:document.getElementById('div_search').hidden = false;document.getElementById('a_plus').hidden = true;" id="a_plus">
                  <span class="fa fa-search f-16 text-success"></span>
                </a>
                Teléfono: </span>
              <span class="col-8 col-sm-8 col-md-6 col-lg-7 text-left"><b class="text-truncate w-100" id="b_telefono_cliente"><?php echo $cliente['telefono'] ?></b></span>
            </p>
          </div>
        </div>
        <div class="form-group my-2" id="div_search" hidden="">
          <div class="row m-0 p-1">
            <a class="p-1 col-1 text-center" href="javascript:document.getElementById('div_search').hidden = true;document.getElementById('a_plus').hidden = false;">
              <span class="fa fa-times"></span>
            </a>
            <input type="text" class="form-control form-control-sm col" name="input_busqueda_fact" id="input_busqueda_fact" placeholder="Cédula/NIT - Nombre - Teléfono" autocomplete="off">
            <button class="btn btn-sm btn-outline-primary btn-round col-2" id="btn_buscar_cliente_fact"><span class="fas fa-search"></span></button>
          </div>
        </div>
        <div id="tabla_busqueda_cliente"></div>
        <hr class="my-1">
        <div class="row pt-0 text-center">
          <h4 class="col text-center mb-0">Items factura</h4>
        </div>
        <hr class="my-1">
        <div class="row" id="div_items_fact"></div>

      </div>
      <div class="card-footer text-right">
        <button class="btn btn-sm btn-outline-primary btn-round" id="btn_generar_factura" <?php if (!isset($_SESSION['items_factura']) || !isset($_SESSION['cod_cliente_fact'])) {
                                                                                    echo 'disabled=""';
                                                                                  } ?>>Generar Factura</button>
      </div>
    </div>

    <!-- Modal Nuevo cliente-->
    <div class="modal fade" id="Modal_Nuevo_Cliente" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-center">
            <h5 class="modal-title">Agregar Nuevo Cliente</h5>
          </div>
          <div class="modal-body">
            <form id="frmnuevo" autocomplete="off">
              <div class="row form-group">
                <div class="form-line col-6">
                  <label>Identificación:</label>
                  <input type="number" class="form-control form-control-sm" id="identificacion_cliente" name="identificacion_cliente">
                </div>
              </div>
              <div class="row form-group form-group-sm">
                <div class="form-line col">
                  <label>Nombre:</label>
                  <input type="text" class="form-control form-control-sm" id="nombre_cliente" name="nombre_cliente">
                </div>
                <div class="form-line col">
                  <label>Apellido:</label>
                  <input type="text" class="form-control form-control-sm" id="apellido_cliente" name="apellido_cliente">
                </div>
              </div>
              <div class="form-group form-group-sm">
                <div class="form-line">
                  <label>Telefono:</label>
                  <input type="text" class="form-control form-control-sm" id="telefono_cliente" name="telefono_cliente">
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-dismiss="modal" id="btnAgregar">Agregar Cliente</button>
          </div>
        </div>
      </div>
    </div>

    <script type="text/javascript">
      document.getElementById('div_loader').style.display = 'block';
      $('#div_items_fact').load('tablas/items_factura.php', function() {
        cerrar_loader();
      });

      $(".select2").select2();

      function seleccionar_cliente_fact(cod_cliente) {
        $.ajax({
          type: "POST",
          data: "cod_cliente=" + cod_cliente,
          url: "procesos/asignar_cliente_fact.php",
          success: function(r) {
            datos = jQuery.parseJSON(r);
            if (datos['consulta'] == 1) {
              if (datos['cedula'] == '---')
                w_alert({
                  titulo: 'Cliente removido correctamente',
                  tipo: 'success'
                });
              else
                w_alert({
                  titulo: 'Cliente Asignado correctamente',
                  tipo: 'success'
                });

              click_item('vistas_pdv/nueva_factura');
            } else
              w_alert({
                titulo: datos['consulta'],
                tipo: 'danger'
              });
            if (datos['consulta'] == 'Reload') {
              document.getElementById('div_login').style.display = 'block';
              cerrar_loader();

            }
          }
        });
      }

      $('#btn_generar_factura').click(function() {
        document.getElementById('div_loader').style.display = 'block';
        $.ajax({
          type: "POST",
          url: "procesos/generar_factura.php",
          success: function(r) {
            datos = jQuery.parseJSON(r);
            if (datos['consulta'] == 1) {
              w_alert({
                titulo: 'Factura Generada Correctamente',
                tipo: 'success'
              });
              click_item('facturas');
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
      });

      $('#input_busqueda_fact').keypress(function(e) {
        if (e.keyCode == 13)
          $('#btn_buscar_cliente_fact').click();
      });

      $('#btn_buscar_cliente_fact').click(function() {
        document.getElementById('div_loader').style.display = 'block';
        input_busqueda = document.getElementById("input_busqueda_fact").value;
        input_busqueda = input_busqueda.replace(/ /g, "***");
        if (input_busqueda != '' && input_busqueda.length > 2)
          $('#tabla_busqueda_cliente').load('tablas/tabla_busqueda_cliente_fact.php/?page=1&input_buscar=' + input_busqueda, function() {
            cerrar_loader();
          });
        else {
          w_alert({
            titulo: 'Ingrese al menos 3 caracteres',
            tipo: 'danger'
          });
          document.getElementById("input_busqueda_fact").focus();
        }
        cerrar_loader();
      });
    </script>

<?php
  } else
    require_once '../error_403.php';
} else
  header("Location:../login.php");
?>