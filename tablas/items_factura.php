<?php
session_set_cookie_params(7 * 24 * 60 * 60);
session_start();
?>
<table class="table text-dark table-sm" id="tabla_items">
  <thead>
    <tr class="text-center">
      <th>#</th>
      <th>Descripción</th>
      <th width="120px">Cant</th>
      <th width="150px">Valor</th>
      <th width="80px">Total</th>
      <th></th>
    </tr>
  </thead>
  <tbody class="overflow-auto">
    <?php
    $total = 0;
    $i = 1;
    if (isset($_SESSION['items_factura'])) {
      $items_factura = $_SESSION['items_factura'];
      
      foreach ($items_factura as $i => $item) {
        $total_item = $item['valor_unitario'] * $item['cant'];
        $total += $total_item;
        $descripcion = $item['descripcion'];
        $cant = $item['cant'];
        $valor_unitario = $item['valor_unitario'];
    ?>
        <tr>
          <td class="text-center  p-1"><?php echo str_pad($i, 3, "0", STR_PAD_LEFT) ?></td>
          <td class="p-1"><?php echo $descripcion ?></td>
          <td class="text-center p-1"><?php echo $cant ?></td>
          <td class="text-right p-1">$<?php echo number_format($valor_unitario, 0, '.', '.') ?></td>
          <td class="text-right p-1"><strong>$<?php echo number_format($total_item, 0, '.', '.') ?></strong></td>
          <td class="text-center p-1">
            <button class="btn btn-danger btn-round p-0 px-1" onclick="eliminar_item('<?php echo $i ?>')">
              <span class="fa fa-trash fs--1"></span>
            </button>
          </td>
        </tr>
    <?php
      }
    }
    ?>
    <tr>
      <td class="text-center  p-1"><?php echo str_pad($i, 3, "0", STR_PAD_LEFT) ?></td>
      <td>
        <input type="text" class="form-control form-control-sm" name="input_descripcion" id="input_descripcion" autocomplete="off">
      </td>
      <td class="text-center">
        <input type="number" class="form-control form-control-sm" name="input_cantidad" id="input_cantidad">
      </td>
      <td class="text-center">
        <input type="text" class="form-control form-control-sm moneda" name="valor_unitario" id="valor_unitario">
      </td>
      <td colspan="2" class="text-center">
        <button class="btn btn-outline-success btn-round p-0 px-1" id="btn_agregar_item">
          <span class="fa fa-plus"></span>
        </button>
      </td>
    </tr>
    <tr>
      <td colspan="5" class="bg-white p-1"></td>
    </tr>
    <tr class="bg-white">
      <td colspan="4" class="text-right p-1">
        <h4 class="mb-0">Total</h4>
      </td>
      <td colspan="2" class="text-right p-1">
        <h4 class="mb-0">$<?php echo number_format($total, 0, '.', '.') ?></h4>
      </td>
    </tr>
  </tbody>
</table>

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

  $('#valor_unitario').keypress(function(e) {
    if (e.keyCode == 13)
      $('#btn_agregar_item').click();
  });

  $('#btn_agregar_item').click(function() {
    document.getElementById('div_loader').style.display = 'block';
    document.getElementById("btn_agregar_item").disabled = true;
    descripcion = document.getElementById("input_descripcion").value;
    cantidad = document.getElementById("input_cantidad").value;
    valor_unitario = document.getElementById("valor_unitario").value;
    if (descripcion != '' && cantidad != '' && valor_unitario != '') {
      $.ajax({
        type: "POST",
        data: "descripcion=" + descripcion + "&cantidad=" + cantidad + "&valor_unitario=" + valor_unitario,
        url: "procesos/agregar_item_factura.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            w_alert({
              titulo: 'Item Agregado Correctamente',
              tipo: 'success'
            });
            $('#div_items_fact').load('tablas/items_factura.php', function() {
              cerrar_loader();
            });
            document.getElementById("btn_agregar_item").disabled = false;
            document.getElementById("btn_generar_factura").disabled = datos['btn_generar'];
          } else {
            w_alert({
              titulo: datos['consulta'],
              tipo: 'danger'
            });
            if (datos['consulta'] == 'Reload') {
              document.getElementById('div_login').style.display = 'block';
              cerrar_loader();

            }
            document.getElementById("btn_agregar_item").disabled = false;
          }
        }
      });
    } else {
      if (descripcion == '') {
        w_alert({
          titulo: 'Debe ingresar una descripción',
          tipo: 'danger'
        });
        document.getElementById("input_descripcion").focus();
      } else if (cantidad == '') {
        w_alert({
          titulo: 'Debe ingresar una cantidad',
          tipo: 'danger'
        });
        document.getElementById("input_cantidad").focus();
      } else if (valor_unitario == '') {
        w_alert({
          titulo: 'Debe ingresar un valor unitario',
          tipo: 'danger'
        });
        document.getElementById("valor_unitario").focus();
      }
      cerrar_loader();
      document.getElementById("btn_agregar_item").disabled = false;
    }
  });

  function eliminar_item(num_item) {
    document.getElementById('div_loader').style.display = 'block';
    $.ajax({
      type: "POST",
      data: "num_item=" + num_item,
      url: "procesos/eliminar_item_factura.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          w_alert({
            titulo: 'Item Eliminado Correctamente',
            tipo: 'success'
          });
          $('#div_items_fact').load('tablas/items_factura.php', function() {
            cerrar_loader();
          });
          document.getElementById("btn_generar_factura").disabled = datos['btn_generar'];
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