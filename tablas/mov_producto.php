<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();
session_set_cookie_params(7 * 24 * 60 * 60);
session_start();

$cod_producto = $_GET['cod_producto'];
$pos = $_GET['pos'];

$bodega = $_GET['bodega'];

$sql = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo = '$cod_producto'";
$result = mysqli_query($conexion, $sql);
$mostrar = mysqli_fetch_row($result);

$inventario = array();
if ($bodega == 'Principal') {
   if ($mostrar[3] != '')
      $inventario = json_decode($mostrar[3], true);
} else if ($bodega == 'PDV_1') {
   if ($mostrar[6] != '')
      $inventario = json_decode($mostrar[6], true);
} else if ($bodega == 'PDV_2') {
   if ($mostrar[7] != '')
      $inventario = json_decode($mostrar[7], true);
}

$movimientos = array();
if (isset($inventario[$pos])) {
   if ($inventario[$pos]['movimientos'] != '')
      $movimientos = $inventario[$pos]['movimientos'];
}

$str_cod_producto = 'cod_producto=' . $cod_producto . '&pos=' . $pos . '&bodega=' . $bodega;

?>
<div class="text-center border-top pt-4">
   <h4>Movimientos (#<?php echo $pos ?>)</h4>
</div>
<table>
   <tbody>
      <tr role="row" class="odd">
         <td colspan="2" class="text-left p-1">
            <select class="form-control form-control-sm" name="tipo_mov" id="tipo_mov">
               <option value="">Seleccione tipo</option>
               <option value="Retorno">Retorno</option>
               <option value="Salida">Salida</option>
               <option value="Traslado">Traslado</option>
               <option value="Baja">Baja</option>
               <option value="Garantía">Garantía</option>
            </select>
         </td>
         <td class="text-center p-1">
            <input type="number" class="form-control form-control-sm" name="cant_mov" id="cant_mov" placeholder="Cantidad">
         </td>
         <td colspan="2" class="text-left p-1">
            <input type="text" class="form-control form-control-sm" name="obs_mov" id="obs_mov" placeholder="Observaciones del movimiento" autocomplete="off">
            <select class="form-control form-control-sm" name="bodega_traslado" id="bodega_traslado" hidden>
               <option value="">Seleccione la bodega</option>
               <?php
               if ($bodega != 'Principal') {
               ?>
                  <option value="Principal">Principal</option>
               <?php
               }
               if ($bodega != 'PDV_1') {
               ?>
                  <option value="PDV_1">PDV 1</option>
               <?php
               }
               if ($bodega != 'PDV_2') {
               ?>
                  <option value="PDV_2">PDV 2</option>
               <?php
               }
               ?>
            </select>
         </td>
         <td class="text-center p-1">
            <button class="btn btn-outline-primary btn-round py-1" id="btn_agregar_mov">
               <span class="fa fa-save"></span> Procesar
            </button>
         </td>
      </tr>
   </tbody>
</table>
<div class="table-responsive text-dark text-center py-0 px-1">
   <table class="table text-dark table-sm" id="tabla_movimientos_p">
      <thead>
         <tr class="text-center">
            <th class="py-1" width="30px" class="table-plus text-dark datatable-nosort px-1">#</th>
            <th class="py-1" class="px-1">Tipo Movimiento</th>
            <th class="py-1" width="60px">Cantidad</th>
            <th class="py-1">Observaciones</th>
            <th class="py-1">Creador</th>
            <th class="py-1" width="180px">Fecha</th>
         </tr>
      </thead>
      <tbody class="overflow-auto">
         <?php
         $total = 0;
         foreach ($movimientos as $i => $movimiento) {
            $tipo = $movimiento['Tipo'];
            $cant = $movimiento['Cant'];
            $creador = $movimiento['creador'];
            $observaciones = $movimiento['Observaciones'];
            $fecha = $movimiento['fecha'];

            if ($creador != 0) {
               $sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
               $result_e = mysqli_query($conexion, $sql_e);
               $ver_e = mysqli_fetch_row($result_e);
               if ($ver_e != NULL) {
                  $nombre_aux = explode(' ', $ver_e[0]);
                  $apellido_aux = explode(' ', $ver_e[1]);
                  $creador = $nombre_aux[0] . ' ' . $apellido_aux[0];
               } else
                  $creador = '?';
            } else
               $creador = 'Sistema';

            $text_cant = 'text-danger';

            if ($cant > 0)
               $text_cant = 'text-success';

         ?>
            <tr role="row" class="odd">
               <td class="text-center p-1 text-muted"><?php echo $i ?></td>
               <td class="text-left p-1"><?php echo $tipo ?></td>
               <td class="text-center p-1 <?php echo $text_cant ?>"><?php echo $cant ?></td>
               <td class="text-left p-1"><?php echo $observaciones ?></td>
               <td class="text-left p-1"><b><?php echo $creador ?></b></td>
               <td class="text-left p-1"><?php echo $fecha ?></td>
            </tr>
         <?php
         }
         ?>
      </tbody>
   </table>
</div>

<script type="text/javascript">
   $('#tabla_movimientos_p').DataTable();

   $('input.moneda').keyup(function(event) {
      if (event.which >= 37 && event.which <= 40) {
         event.preventDefault();
      }
      $(this).val(function(index, value) {
         return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
      });
   });

   $('#obs_mov').keypress(function(e) {
      if (e.keyCode == 13)
         $('#btn_agregar_mov').click();
   });

   $('#cant_mov').keypress(function(e) {
      if (e.keyCode == 13)
         $('#btn_agregar_mov').click();
   });

   $("#tipo_mov").change(function() {
      var seleccion = $(this).val();
      if (seleccion == 'Traslado') {
         document.getElementById("obs_mov").hidden = true;
         document.getElementById("bodega_traslado").hidden = false;
      } else {
         document.getElementById("obs_mov").hidden = false;
         document.getElementById("bodega_traslado").hidden = true;
      }
   });

   $('#btn_agregar_mov').click(function() {
      document.getElementById('div_loader').style.display = 'block';
      document.getElementById("btn_agregar_mov").disabled = true;
      tipo_mov = document.getElementById("tipo_mov").value;
      cant_mov = document.getElementById("cant_mov").value;
      obs_mov = document.getElementById("obs_mov").value;
      bodega_traslado = document.getElementById("bodega_traslado").value;
      if (tipo_mov != '' && cant_mov != '') {
         if ((tipo_mov == 'Traslado' && bodega_traslado != '') || (tipo_mov != 'Traslado' && obs_mov != '')) {
            $.ajax({
               type: "POST",
               data: "<?php echo $str_cod_producto ?>" + "&tipo_mov=" + tipo_mov + "&cant_mov=" + cant_mov + "&obs_mov=" + obs_mov + "&bodega_traslado=" + bodega_traslado,
               url: "procesos/agregar_mov_inventario.php",
               success: function(r) {
                  datos = jQuery.parseJSON(r);
                  if (datos['consulta'] == 1) {
                     w_alert({
                        titulo: 'Movimiento agregado con exito',
                        tipo: 'success'
                     });
                     document.getElementById('div_loader').style.display = 'block';
                     $('#div_modal_producto').load('paginas/detalles/detalles_producto.php/?cod_producto=<?php echo $cod_producto ?>&bodega=<?php echo $bodega ?>', function() {
                        cerrar_loader();
                     });
                     document.getElementById('div_loader').style.display = 'block';
                     setTimeout(
                        function() {
                           $('#div_operacion').load('tablas/mov_producto.php/?<?php echo $str_cod_producto ?>', function() {
                              cerrar_loader();
                           });
                        }, 500);
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
         } else {
            if (tipo_mov == 'Traslado') {
               if (bodega_traslado == '') {
                  w_alert({
                     titulo: 'Seleccione la bodega para traslado',
                     tipo: 'danger'
                  });
                  document.getElementById("bodega_traslado").focus();
               }
            } else {
               if (obs_mov == '') {
                  w_alert({
                     titulo: 'Escriba las observaciones para el movimiento',
                     tipo: 'danger'
                  });
                  document.getElementById("obs_mov").focus();
               }
            }
         }
      } else {
         if (tipo_mov == '') {
            w_alert({
               titulo: 'Seleccione el tipo de movimiento',
               tipo: 'danger'
            });
            document.getElementById("tipo_mov").focus();
         } else if (cant_mov == '') {
            w_alert({
               titulo: 'Ingrese la cantidad',
               tipo: 'danger'
            });
            document.getElementById("cant_mov").focus();
         }
      }

      cerrar_loader();
      document.getElementById("btn_agregar_mov").disabled = false;
   });

   function eliminar_item_pago(num_item) {
      document.getElementById('div_loader').style.display = 'block';
      $.ajax({
         type: "POST",
         data: "num_item=" + num_item,
         url: "procesos/eliminar_pago_trabajo.php",
         success: function(r) {
            datos = jQuery.parseJSON(r);
            if (datos['consulta'] == 1) {
               w_alert({
                  titulo: 'Item eliminado',
                  tipo: 'success'
               });
               document.getElementById('div_loader').style.display = 'block';
               $('#tabla_pagos').load('tablas/pagos_trabajo.php', function() {
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
</script>