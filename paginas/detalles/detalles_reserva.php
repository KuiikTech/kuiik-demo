<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$cod_reserva = $_GET['cod_reserva'];

$sql = "SELECT `codigo`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_registro`, `cod_cliente`, `pagos`, `fecha_llegada`, `descuentos`, `code`, `creador` FROM `reservas` WHERE codigo = '$cod_reserva' order by nombre ASC";
$result = mysqli_query($conexion, $sql);
$mostrar = mysqli_fetch_row($result);

$cod_reserva = $mostrar[0];
$nombre = $mostrar[1];
$descripcion = $mostrar[2];
$productos = array();
if ($mostrar[3] != '')
  $productos = json_decode($mostrar[3], true);
$estado_reserva = $mostrar[4];
$fecha_registro = date('Y-m-d G:i A', strtotime($mostrar[5]));
$cod_cliente = $mostrar[6];

$cliente = array(
  'codigo' => '',
  'id' => '',
  'nombre' => '',
  'telefono' => '',
);

if ($cod_cliente != '') {
  $sql = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro` FROM `clientes` WHERE codigo = '$cod_cliente'";
  $result = mysqli_query($conexion, $sql);
  $ver = mysqli_fetch_row($result);

  $cliente = array(
    'codigo' => $ver[0],
    'id' => $ver[1],
    'nombre' => $ver[2],
    'telefono' => $ver[3]
  );
}

$pagos = array();
if ($mostrar[7] != '')
  $pagos = json_decode($mostrar[7], true);

$fecha_llegada = 'Sin fecha';
if ($mostrar[8] != null)
  $fecha_llegada = date('Y-m-d G:i A', strtotime($mostrar[8]));
$descuentos = $mostrar[9];
$code = $mostrar[10];

if ($estado_reserva == 'PENDIENTE')
  $text_estado = 'text-danger';
if ($estado_reserva == 'CANCELADA')
  $text_estado = 'text-info';
if ($estado_reserva == 'PROCESADA')
  $text_estado = 'text-success';

?>
<div class="modal-header text-center p-2">
  <h5 class="modal-title">Detalles de reserva</h5>
</div>
<div class="modal-body p-2">
  <div class="row m-0 border-top pt-3 border-bottom border-3">
    <input type="text" name="cod_cliente" id="cod_cliente" hidden="" value="<?php echo $cod_cliente ?>">
    <p class="row m-0 pl-0">
      <span class="col-4 col-sm-4 col-md-6 col-lg-5 d-flex justify-content-end pr-0">
        <?php
        if ($estado_reserva == 'PENDIENTE') {
          if ($cod_cliente != '') {
        ?>
            <a class="p-0 text-primary" href="javascript:seleccionar_cliente_r('')">
              <span class="fa fa-times text-danger f-16"></span>
            </a>
        <?php
          }
        }
        ?>
        Cédula/NIT:
      </span>
      <?php
      if ($cod_cliente != '') {
      ?>
        <span class="col-8 col-sm-8 col-md-6 col-lg-7 text-left"><b class="text-truncate w-100" id="b_id_cliente"><?php echo $cliente['id'] ?></b></span>
        <?php
      } else {
        if ($estado_reserva == 'PENDIENTE') {
        ?>
          <span class="col-8 col-sm-8 col-md-6 col-lg-7 text-left"><input type="text" class="form-control form-control-sm" name="input_cc_cliente" id="input_cc_cliente" placeholder="Busqueda por C.C/NIT" onkeydown="if(event.key=== 'Enter'){buscar_x_cc(this.value,'<?php echo $cod_reserva ?>')}" autocomplete="off"></span>
      <?php
        }
      }
      ?>
    </p>
    <p class="row m-0 pl-0">
      <span class="col-4 col-sm-4 col-md-6 col-lg-5 d-flex justify-content-end pr-0"> Nombre: </span>
      <span class="col-8 col-sm-8 col-md-6 col-lg-7 text-left"><b class="text-truncate w-100" id="b_nombre_cliente"><?php echo $cliente['nombre'] ?></b></span>
    </p>
    <p class="row m-0 pl-0">
      <span class="col-4 col-sm-4 col-md-6 col-lg-5 d-flex justify-content-end pr-0">
        <?php
        if ($estado_reserva == 'PENDIENTE') {
        ?>
          <a class="p-1" href="javascript:document.getElementById('div_search').hidden = false;document.getElementById('a_plus').hidden = true;" id="a_plus">
            <span class="fa fa-search f-16 text-success"></span>
          </a>
        <?php
        }
        ?>
        Teléfono: </span>
      <span class="col-8 col-sm-8 col-md-6 col-lg-7 text-left"><b class="text-truncate w-100" id="b_telefono_cliente"><?php echo $cliente['telefono'] ?></b></span>
    </p>
  </div>
  <?php
  if ($estado_reserva == 'PENDIENTE') {
  ?>
    <div class="form-group my-2" id="div_search" hidden="">
      <div class="row m-0 p-1">
        <a class="p-1 col-1 text-center" href="javascript:document.getElementById('div_search').hidden = true;document.getElementById('a_plus').hidden = false;">
          <span class="fa fa-times"></span>
        </a>
        <input type="text" class="form-control form-control-sm col" name="input_busqueda_r" id="input_busqueda_r" placeholder="Cédula/NIT - Nombre - Teléfono" autocomplete="off">
        <button class="btn btn-sm btn-outline-primary btn-round col-2" id="btn_buscar_cliente_r"><span class="fas fa-search"></span></button>
      </div>
    </div>
    <div class="row m-0 p-1" id="tabla_busqueda_cliente_r"></div>
  <?php
  }
  ?>
  <div class="row px-2">
    <p class="row mb-0">
      <span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Fecha Registro: </span>
      <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $fecha_registro ?> </b></span>
    </p>
    <div class="row mb-0">
      <span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Fecha Llegada: </span>
      <?php
      if ($estado_reserva == 'PENDIENTE') {
      ?>
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left" ondblclick="document.getElementById('div_input_fecha').hidden = false;this.hidden = true;">
          <b> <?php echo $fecha_llegada ?> </b>
        </div>
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left" id="div_input_fecha" hidden="">
          <?php
          if ($mostrar[8] != null)
            $fecha_llegada = date('Y-m-d G:i:s', strtotime($mostrar[8]));
          ?>
          <div class="input-group">
            <input type="datetime-local" class="form-control form-control-sm" name="input_fecha_llegada" id="input_fecha_llegada" autocomplete="off" placeholder="Selecciona una fecha" value="<?php echo $fecha_llegada ?>">
            <button class="btn btn-sm btn-outline-primary btn-round" id="btn_guardar_fecha_llegada"><span class="fa fa-save"></span> Guardar</button>
          </div>
        </div>
      <?php
      } else {
      ?>
        <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $fecha_llegada ?> </b></span>
      <?php
      }
      ?>
    </div>
    <p class="row mb-0">
      <span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Estado: </span>
      <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left <?php echo $text_estado ?>"><b> <?php echo $estado_reserva ?> </b></span>
    </p>
  </div>
  <hr>
  <div class="row m-0 p-1">
    <h5 class="text-center">Productos</h5>
    <hr class="m-0">
    <div class="table-responsive text-dark text-center py-0 px-1">
      <table class="table text-dark table-sm w-100">
        <thead>
          <tr>
            <th class="p-1"></th>
            <th class="p-1"><b>Producto</b></th>
            <th width="120px" class="p-1 text-center"><b>Valor</b></th>
            <th width="100px" class="p-1 text-center"><b>Cant</b></th>
            <th width="120px" class="p-1 text-center"><b>Total</b></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $total_reserva = 0;
          foreach ($productos as $i => $producto) {
            $cod_producto = $producto['codigo'];
            $cant = $producto['cant'];
            $descripcion_str = $producto['descripcion'];
            $valor_unitario = $producto['valor_unitario'];
            $estado = $producto['estado'];

            $bg_tr = '';
            $bg_dot = '';
            if ($estado == 'PENDIENTE')
              $bg_dot = 'bg-danger';
            if ($estado == 'PREPARANDO')
              $bg_dot = 'bg-warning';
            if ($estado == 'DESPACHADO')
              $bg_dot = 'bg-success';

            $valor_total = $cant * $valor_unitario;
            $total_reserva += $valor_total;

            $valor_unitario = number_format($valor_unitario, 0, '.', '.');
            $valor_total = number_format($valor_total, 0, '.', '.');
          ?>
            <tr class="<?php echo $bg_tr ?>" title="Estado: <?php echo $estado ?>">
              <td width="20px" class="text-center py-1 px-0">
                <?php
                if ($estado_reserva == 'PENDIENTE') {
                ?>
                  <a class="p-0 text-primary" href="javascript:eliminar_item_r('<?php echo $i ?>','<?php echo $cod_reserva ?>')">
                    <span class="fa fa-times text-danger f-16"></span>
                  </a>
                <?php
                }
                ?>
              </td>
              <td class="p-1 text-left"><?php echo $descripcion_str ?></td>
              <td class="text-right p-1">
                <?php
                if ($estado_reserva == 'PENDIENTE') {
                ?>
                  <input type="text" class="form-control moneda text-right" id="input_valor_<?php echo $i ?>" name="input_valor_<?php echo $i ?>" value="<?php echo $valor_unitario ?>" onchange="guardar_valor_producto_r(this.value,'<?php echo $i ?>','<?php echo $cod_reserva ?>')">
                <?php
                } else {
                ?>
                  $<b><?php echo $valor_unitario ?></b>
                <?php
                }
                ?>
              </td>
              <td class="text-right p-1">
                <?php
                if ($estado_reserva == 'PENDIENTE') {
                ?>
                  <input type="number" class="form-control text-center" id="input_cant_<?php echo $i ?>" name="input_cant_<?php echo $i ?>" value="<?php echo $cant ?>" onchange="guardar_cant_producto_r(this.value,'<?php echo $i ?>','<?php echo $cod_reserva ?>')">
                <?php
                } else {
                ?>
                  <b><?php echo $cant ?></b>
                <?php
                }
                ?>
              </td>
              <td class="text-right p-1">$<b><?php echo $valor_total ?></b></td>
            </tr>
          <?php
          }
          ?>
        </tbody>
      </table>
    </div>
    <div class="row">
      <table class="table text-dark mb-1">
        <tbody>
          <tr hidden="">
            <td class="p-1">
              <h4>Subtotal</h4>
            </td>
            <td class="p-1 text-right">
              <h3 class="m-0"><?php echo '$' . number_format($total_reserva, 0, '.', '.'); ?></h3>
            </td>
            <input type="number" name="sub_total" id="sub_total" value="<?php echo $total_reserva ?>" hidden="">
          </tr>
          <tr>
            <td class="p-1">
              <h4>TOTAL</h4>
            </td>
            <td class="p-1 text-right">
              <h3 class="m-0" id="total_a_pagar"><?php echo '$' . number_format($total_reserva, 0, '.', '.'); ?></h3>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="row m-0 py-2 text-center">
      <h5 class="mb-0">Agregar metodos de pago</h5>
    </div>
    <div class="row m-0 p-0 px-1">
      <table class="table text-dark table-sm w-100">
        <tbody>
          <?php
          $total_pagos = 0;
          foreach ($pagos as $j => $pago) {
            $valor_pago = $pago['valor'];
            $total_pagos += $valor_pago;

            $creador = $pago['creador'];
            $fecha_pago = $pago['fecha'];

            $sql_e = "SELECT nombre, apellido, rol, foto, color FROM `usuarios` WHERE codigo = '$creador'";
            $result_e = mysqli_query($conexion, $sql_e);
            $ver_e = mysqli_fetch_row($result_e);
            if ($ver_e != null) {
              $nombre_aux = explode(' ', $ver_e[0]);
              $apellido_aux = explode(' ', $ver_e[1]);
              $creador = $nombre_aux[0] . ' ' . $apellido_aux[0];
            }
          ?>
            <tr>
              <td class="p-1"><?php echo $pago['tipo'] ?></td>
              <td class="p-1 lh-1 text-center" width="150px">
                <?php echo $creador ?>
                <br>
                <small><?php echo $fecha_pago ?></small>
              </td>
              <td width="200px" class="text-right p-1 h4">$<?php echo number_format($valor_pago, 0, '.', '.'); ?></td>
              <td width="20px" class="text-center py-1 px-0">
                <?php
                if ($estado_reserva == 'PENDIENTE') {
                ?>
                  <a class="p-0 text-danger" href="javascript:eliminar_pago_r('<?php echo $cod_reserva ?>','<?php echo $j ?>')">
                    <span class="fa fa-times"></span>
                  </a>
                <?php
                }
                ?>
              </td>
            </tr>

          <?php
          }
          $saldo = $total_reserva - $total_pagos;
          $text_saldo = 'text-danger';
          if ($saldo == 0) {
            $disabled_saldo = '';
            $text_saldo = 'text-success';
          }
          if ($estado_reserva == 'PENDIENTE') {
          ?>
            <tr>
              <td class="text-center" colspan="2">
                <select class="form-control form-control-sm" id="input_metodo_pago" name="input_metodo_pago">
                  <option value="">Seleccione uno...</option>
                  <option value="Efectivo">Efectivo</option>
                  <option value="Tarjeta">Tarjeta</option>
                  <option value="Nequi">Nequi</option>
                  <option value="Bancolombia">Bancolombia</option>
                  <option value="Daviplata">Daviplata</option>
                  <option value="Crédito">Crédito</option>
                  <option value="Descuento">Descuento</option>
                </select>
              </td>
              <td class="text-center">
                <input type="text" class="form-control form-control-sm moneda" id="valor_pago" name="valor_pago" placeholder="Valor">
              </td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-round" id="btn_agregar_pago_r">+</button>
              </td>
            </tr>
          <?php
          }
          ?>
          <tr>
            <td class="text-right p-1" colspan="2"><b>Total Pagos</b></td>
            <td class="text-right p-1 h4" width="120px"><b>$<?php echo number_format($total_pagos, 0, '.', '.'); ?></b></td>
            <td></td>
          </tr>
          <tr>
            <td class="text-right p-1" colspan="2"><b>Saldo</b></td>
            <td class="text-right p-1 h4 <?php echo $text_saldo ?>" width="120px"><b>$<?php echo number_format($saldo, 0, '.', '.'); ?></b></td>
            <td></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <?php
  if ($estado_reserva == 'PENDIENTE') {
  ?>
    <hr class="my-1">
    <div class="row m-0 py-0 px-1">
      <?php
      $num_item = 0;
      $sql_categorias = "SELECT `cod_categoria`, `nombre` FROM `categorias_productos` order by nombre ASC";
      $result_categorias = mysqli_query($conexion, $sql_categorias);
      while ($mostrar_categorias = mysqli_fetch_row($result_categorias)) {
        $nombre_cat = ucwords(mb_strtolower($mostrar_categorias[1]));
        $cod_categoria = $mostrar_categorias[0];
      ?>
        <button type="button" class="btn btn-sm btn-outline-secondary btn-round mt-1 w-auto" onclick="mostrar_productos_r('<?php echo $cod_categoria ?>','<?php echo $cod_reserva ?>')"><?php echo $nombre_cat ?></button>
      <?php
        $num_item++;
      }
      ?>
    </div>
    <hr class="my-1">
    <div class="row clearfix mx-4">
      <input type="text" class="form-control form-control-sm" id="busqueda" name="busqueda" autocomplete="off" placeholder="Busqueda de productos" onKeyUp="mostrar_busqueda_r('<?php echo $cod_reserva ?>');">
    </div>
    <hr class="my-1">
    <div class="conatiner px-0" id="div_tabla_productos_r"></div>
  <?php
  }
  ?>
</div>
<div class="modal-footer p-2">
  <div class="col text-left">
    <button type="button" class="btn btn-sm btn-secondary btn-round " data-bs-dismiss="modal">Cerrar</button>
  </div>
  <?php
  if ($estado_reserva == 'PENDIENTE') {
  ?>
    <div class="col" id="div_input_mesa" hidden>
      <div class="row m-0 p-1 d-flex justify-content-end">
        <div class="col-auto text-right">
          <select class="form-control form-control-sm" id="input_mesa_reserva" name="input_mesa_reserva">
            <option value="">Seleccione una mesa...</option>
            <?php
            $sql_mesas = "SELECT `cod_mesa`, `nombre` FROM `mesas` WHERE `estado` = 'LIBRE' order by nombre ASC";
            $result_mesas = mysqli_query($conexion, $sql_mesas);
            while ($mostrar_mesas = mysqli_fetch_row($result_mesas)) {
              $nombre_mesa = ucwords(mb_strtolower($mostrar_mesas[1]));
              $cod_mesa = $mostrar_mesas[0];
            ?>
              <option value="<?php echo $cod_mesa ?>"><?php echo $nombre_mesa ?></option>
            <?php
            }
            ?>
          </select>
        </div>
        <button class="btn btn-sm btn-outline-success btn-round col-auto" id="btn_procesar_reserva">ENVIAR</button>
      </div>
    </div>
    <div class="col text-right" id="div_btn_procesar">
      <button class="btn btn-sm btn-outline-warning btn-round" id="btn_cancelar_reserva">CANCELAR RESERVA</button>
      <button class="btn btn-sm btn-outline-primary btn-round" onclick="document.getElementById('div_btn_procesar').hidden = true;document.getElementById('div_input_mesa').hidden = false;">PASAR A MESA</button>
    </div>
  <?php
  }
  ?>
</div>


<script type="text/javascript">
  $('input.moneda').keyup(function(event) {
    if (event.which >= 37 && event.which <= 40) {
      event.preventDefault();
    }

    $(this).val(function(index, value) {
      return value
        .replace(/\D/g, "")
        .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
    });
  });

  function cambiar_estado_compra(estado, cod_compra) {
    $.ajax({
      type: "POST",
      data: "cod_compra=" + cod_compra + "&estado=" + estado,
      url: "procesos/cambiar_estado_compra.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          w_alert({
            titulo: 'Estado cambiado',
            tipo: 'success'
          });
          document.getElementById('div_loader').style.display = 'block';
          $('#div_modal_reserva').load('paginas/detalles/detalles_reserva.php/?cod_reserva=<?php echo $cod_reserva ?>', function() {
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
        }
      }
    });
  }

  function mostrar_productos_r(cod_categoria, cod_reserva) {
    document.getElementById('div_loader').style.display = 'block';
    $('#div_tabla_productos_r').load('paginas/vistas_pdv/pdv_productos_reserva.php/?cod_categoria=' + cod_categoria + '&cod_reserva=' + cod_reserva, function() {
      cerrar_loader();
    });
  }

  function mostrar_busqueda_r(cod_reserva) {
    var busqueda = document.getElementById("busqueda").value;
    busqueda = busqueda.replace(/ /g, "***");
    if (busqueda != '') {
      if (busqueda.length > 2) {
        document.getElementById('div_loader').style.display = 'block';
        $('#div_tabla_productos_r').load('paginas/vistas_pdv/pdv_productos_reserva.php/?consulta=' + busqueda + '&cod_reserva=' + cod_reserva, function() {
          cerrar_loader();
        });
      } else {
        document.getElementById('div_loader').style.display = 'block';
        $('#div_tabla_productos_r').load('paginas/vistas_pdv/pdv_productos_reserva.php/?consulta0=' + busqueda + '&cod_reserva=' + cod_reserva, function() {
          cerrar_loader();
        });
      }
    }
  }

  $('#input_busqueda_r').keypress(function(e) {
    if (e.keyCode == 13)
      $('#btn_buscar_cliente_r').click();
  });

  $('#btn_buscar_cliente_r').click(function() {
    document.getElementById('div_loader').style.display = 'block';
    input_busqueda = document.getElementById("input_busqueda_r").value;
    input_busqueda = input_busqueda.replace(/ /g, "***");
    if (input_busqueda != '' && input_busqueda.length > 2)
      $('#tabla_busqueda_cliente_r').load('tablas/tabla_busqueda_cliente_r.php/?page=1&input_buscar=' + input_busqueda, function() {
        cerrar_loader();
      });
    else {
      w_alert({
        titulo: 'Ingrese al menos 3 caracteres',
        tipo: 'danger'
      });
      document.getElementById("input_busqueda_r").focus();
    }
    cerrar_loader();
  });

  function seleccionar_cliente_r(cod_cliente) {
    document.getElementById('div_loader').style.display = 'block';
    $.ajax({
      type: "POST",
      data: "cod_reserva=<?php echo $cod_reserva ?>" + "&cod_cliente=" + cod_cliente,
      url: "procesos/asignar_cliente_reserva.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          if (cod_cliente != '')
            w_alert({
              titulo: 'Cliente Seleccionado',
              tipo: 'success'
            });
          else
            w_alert({
              titulo: 'Cliente Descartado',
              tipo: 'success'
            });
          $('#div_modal_reserva').load('paginas/detalles/detalles_reserva.php/?cod_reserva=<?php echo $cod_reserva ?>', function() {
            cerrar_loader();
          });
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
  }

  function agregar_producto_r() {
    document.getElementById('div_loader').style.display = 'block';
    cod_producto = document.getElementById("cod_producto_pedido").value;
    cod_reserva = document.getElementById("cod_reserva_pedido").value;
    cant = document.getElementById("cantidad_pedido").value;
    if (cant != '' && cant > 0) {
      $.ajax({
        type: "POST",
        data: "cod_producto=" + cod_producto + "&cod_reserva=" + cod_reserva + "&cant=" + cant,
        url: "procesos/agregar_producto_reserva.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            document.body.style.overflow = "visible";
            $('#btn_close_cant_producto').click();
            mostrar_productos_r(datos['cod_categoria'], cod_reserva);
            $('#div_modal_reserva').load('paginas/detalles/detalles_reserva.php/?cod_reserva=<?php echo $cod_reserva ?>', function() {
              cerrar_loader();
            });
          } else {
            if (datos['consulta'] == 'Reload')
              location.reload();
            else {
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
        }
      });
    } else {
      w_alert({
        titulo: 'Ingrese una cantidad válida. Mayor o igual a 1',
        tipo: 'danger'
      });
      cerrar_loader();
    }

    $('#Modal_Cantidad_Producto').toggle();
    document.querySelector("body").style.overflow = "auto";
  }

  $('#btn_agregar_pago_r').click(function() {
    document.getElementById('div_loader').style.display = 'block';
    metodo_pago = document.getElementById("input_metodo_pago").value;
    valor_pago = document.getElementById("valor_pago").value;
    $.ajax({
      type: "POST",
      data: "cod_reserva=<?php echo $cod_reserva ?>" + "&metodo_pago=" + metodo_pago + "&valor_pago=" + valor_pago,
      url: "procesos/agregar_pago_reserva.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          w_alert({
            titulo: 'Pago Agregado',
            tipo: 'success'
          });
          $('#div_modal_reserva').load('paginas/detalles/detalles_reserva.php/?cod_reserva=<?php echo $cod_reserva ?>', function() {
            cerrar_loader();
          });
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

  $('#btn_guardar_fecha_llegada').click(function() {
    document.getElementById('div_loader').style.display = 'block';
    input_fecha = document.getElementById("input_fecha_llegada").value;
    if (input_fecha != '') {
      $.ajax({
        type: "POST",
        data: "cod_reserva=<?php echo $cod_reserva ?>" + "&input_fecha=" + input_fecha,
        url: "procesos/guardar_fecha_llegada.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            w_alert({
              titulo: 'Fecha Guardada',
              tipo: 'success'
            });
            $('#div_modal_reserva').load('paginas/detalles/detalles_reserva.php/?cod_reserva=<?php echo $cod_reserva ?>', function() {
              cerrar_loader();
            });
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
    } else {
      w_alert({
        titulo: 'Ingrese una fecha válida',
        tipo: 'danger'
      });
      cerrar_loader();
      document.getElementById('input_fecha_llegada').focus();
    }
  });

  function eliminar_pago_r(cod_reserva, item) {
    document.getElementById('div_loader').style.display = 'block';
    $.ajax({
      type: "POST",
      data: "cod_reserva=" + cod_reserva + "&item=" + item,
      url: "procesos/eliminar_pago_reserva.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          w_alert({
            titulo: 'Descuento Eliminado',
            tipo: 'success'
          });
          $('#div_modal_reserva').load('paginas/detalles/detalles_reserva.php/?cod_reserva=<?php echo $cod_reserva ?>', function() {
            cerrar_loader();
          });
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
  }

  $('#btn_procesar_reserva').click(function() {
    document.getElementById('div_loader').style.display = 'block';
    document.getElementById("btn_procesar_reserva").disabled = true;
    cod_mesa = document.getElementById("input_mesa_reserva").value;
    if (cod_mesa != '') {
      $.ajax({
        type: "POST",
        data: "cod_reserva=<?php echo $cod_reserva ?>" + "&cod_mesa=" + cod_mesa,
        url: "procesos/procesar_reserva.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            w_alert({
              titulo: 'Reserva procesada con exito',
              tipo: 'success'
            });
            $('#div_modal_reserva').load('paginas/detalles/detalles_reserva.php/?cod_reserva=<?php echo $cod_reserva ?>', function() {
              cerrar_loader();
            });
            notificacion_reservas('PENDIENTE');
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
      cerrar_loader();
      document.getElementById("btn_procesar_reserva").disabled = false;
    } else {
      w_alert({
        titulo: 'Seleccione una mesa',
        tipo: 'danger'
      });
      document.getElementById("input_mesa_reserva").focus();
    }
    cerrar_loader();
    document.getElementById("btn_procesar_reserva").disabled = false;
  });

  function eliminar_item_r(num_item, cod_reserva) {
    document.getElementById('div_loader').style.display = 'block';
    $.ajax({
      type: "POST",
      data: "num_item=" + num_item + "&cod_reserva=" + cod_reserva,
      url: "procesos/eliminar_item_r.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          $('#div_modal_reserva').load('paginas/detalles/detalles_reserva.php/?cod_reserva=<?php echo $cod_reserva ?>', function() {
            cerrar_loader();
          });
          mostrar_productos_r(datos['cod_categoria'], cod_reserva);
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

  function guardar_valor_producto_r(valor, item, cod_reserva) {
    document.getElementById('div_loader').style.display = 'block';
    $.ajax({
      type: "POST",
      data: "valor=" + valor + "&item=" + item + "&cod_reserva=" + cod_reserva,
      url: "procesos/guardar_valor_producto_r.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          w_alert({
            titulo: 'Valor Guardado',
            tipo: 'success'
          });
          $('#div_modal_reserva').load('paginas/detalles/detalles_reserva.php/?cod_reserva=<?php echo $cod_reserva ?>', function() {
            cerrar_loader();
          });
        } else {
          if (datos['consulta'] == 'Reload')
            location.reload();
          else {
            w_alert({
              titulo: datos['consulta'],
              tipo: 'danger'
            });
            cerrar_loader();
          }
        }
      }
    });
  }

  function guardar_cant_producto_r(cant, item, cod_reserva) {
    document.getElementById('div_loader').style.display = 'block';
    $.ajax({
      type: "POST",
      data: "cant=" + cant + "&item=" + item + "&cod_reserva=" + cod_reserva,
      url: "procesos/guardar_cant_producto_r.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          w_alert({
            titulo: 'Cantidad Guardada',
            tipo: 'success'
          });
          $('#div_modal_reserva').load('paginas/detalles/detalles_reserva.php/?cod_reserva=<?php echo $cod_reserva ?>', function() {
            cerrar_loader();
          });
        } else {
          if (datos['consulta'] == 'Reload')
            location.reload();
          else {
            w_alert({
              titulo: datos['consulta'],
              tipo: 'danger'
            });
            cerrar_loader();
          }
        }
      }
    });
  }

  $('#btn_cancelar_reserva').click(function() {
    document.getElementById('div_loader').style.display = 'block';
    $.ajax({
      type: "POST",
      data: "cod_reserva=<?php echo $cod_reserva ?>",
      url: "procesos/cancelar_reserva.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          w_alert({
            titulo: 'Reserva Cancelada',
            tipo: 'success'
          });
          $("#Modal_Ver").modal('toggle');
          $('.modal-backdrop').remove();

          click_item('reservas');
          notificacion_reservas('PENDIENTE');
        } else {
          if (datos['consulta'] == 'Reload')
            location.reload();
          else {
            w_alert({
              titulo: datos['consulta'],
              tipo: 'danger'
            });
            cerrar_loader();
          }
        }
      }
    });
  });
</script>