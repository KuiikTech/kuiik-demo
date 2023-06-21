<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();
session_set_cookie_params(7 * 24 * 60 * 60);
session_start();
$usuario = $_SESSION['usuario_restaurante'];

$codigo = 0;

if (isset($_GET['cod_mesa']))
  $codigo = $_GET['cod_mesa'];
if (isset($_GET['cod_espacio']))
  $codigo = $_GET['cod_espacio'];
if (isset($_GET['new_fact']))
  $codigo = $_GET['new_fact'];


?>
<div class="text-center row">
  <h4 class="col mt-2">Agregar nuevo cliente</h4>
</div>
<div class="row border-top m-0 p-1 pt-3 border-bottom border-3">
  <form id="form_nuevo_cliente" class="px-1">
    <div class="container m-0 p-0">
      <div class="row pb-1 px-0 m-0">
        <label class="col-sm-4 col-form-label p-0 text-right"><span class="requerido">*</span>Cédula/NIT: </label>
        <div class="col-sm-8">
          <input type="text" class="form-control form-control-sm" name="input_identificacion_cliente" id="input_identificacion_cliente" autocomplete="off" placeholder="Busqueda por C.C/NIT" onkeydown="if(event.key=== 'Enter'){buscar_x_cc(this.value,'<?php echo $codigo ?>')}">
        </div>
      </div>

      <div class="row pb-1 px-1 m-0">
        <label class="col-sm-4 col-form-label p-0 text-right"><span class="requerido">*</span>Nombre: </label>
        <div class="col-sm-8">
          <input type="text" class="form-control form-control-sm" name="input_nombre" id="input_nombre" autocomplete="off">
        </div>
      </div>

      <div class="row pb-1 px-1 m-0">
        <label class="col-sm-4 col-form-label p-0 text-right"><span class="requerido">*</span>Telefono: </label>
        <div class="col-sm-8">
          <input type="text" class="form-control form-control-sm" name="input_telefono" id="input_telefono" autocomplete="off">
        </div>
      </div>

      <div class="row pb-1 px-1 m-0">
        <label class="col-sm-4 col-form-label p-0 text-right"><span class="requerido">*</span>Correo: </label>
        <div class="col-sm-8">
          <input type="text" class="form-control form-control-sm" name="input_correo" id="input_correo" autocomplete="off">
        </div>
      </div>

      <div class="row pb-1 px-1 m-0">
        <label class="col-sm-4 col-form-label p-0 text-right">Dirección: </label>
        <div class="col-sm-8">
          <input type="text" class="form-control form-control-sm" name="input_direccion" id="input_direccion" autocomplete="off">
        </div>
      </div>
    </div>
  </form>
  <br>
  <span><span class="requerido">*</span>Campo Requerido</span>
</div>
<div class="card-footer p-2 row">
  <div class="col text-right">
    <?php
    if (isset($_GET['cod_mesa'])) {
    ?>
      <button class="btn btn-sm btn-secondary btn-round px-2" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_cuenta').load('paginas/vistas_pdv/pdv_cuenta.php/?cod_mesa=<?php echo $codigo ?>', function(){cerrar_loader();});" id="btn_close_add_client">
        Cancelar
      </button>
    <?php
    }
    if (isset($_GET['cod_espacio'])) {
    ?>
      <button class="btn btn-sm btn-secondary btn-round px-2" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_cliente').load('paginas/detalles/cliente_nuevo_servicio.php/?cod_espacio=<?php echo $codigo ?>', cerrar_loader());" id="btn_close_add_client_esp">
        Cancelar
      </button>
    <?php
    }
    if (isset($_GET['new_fact'])) {
    ?>
      <button class="btn btn-sm btn-secondary btn-round px-2" onclick="click_item('vistas_pdv/nueva_factura')" id="btn_close_add_client_fact">
        Cancelar
      </button>
    <?php
    }
    ?>
    <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_nuevo_cliente">Guardar Cliente</button>
  </div>
</div>

<script type="text/javascript">
  $('#btn_nuevo_cliente').click(function() {
    document.getElementById('div_loader').style.display = 'block';
    datos = $('#form_nuevo_cliente').serialize();
    $.ajax({
      type: "POST",
      data: datos,
      url: "procesos/agregar.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          $('#form_nuevo_cliente')[0].reset();
          $('#btn_close_add_client').click();
          w_alert({
            titulo: 'Cliente Agregado Correctamente',
            tipo: 'success'
          });
          <?php
          if (isset($_GET['new_fact'])) {
          ?>
            setTimeout("seleccionar_cliente_fact(datos['codigo'])", 300);
          <?php
          } else {
          ?>
            setTimeout("seleccionar_cliente(datos['codigo'])", 300);
          <?php
          }
          ?>
        } else
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
    });
  });
</script>