<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();
$conexion = $obj->conexion();

$num_tabla = 1;
session_set_cookie_params(7 * 24 * 60 * 60);
session_start();

if (isset($_SESSION['usuario_restaurante'])) {
  $usuario = $_SESSION['usuario_restaurante'];

  require_once "../clases/permisos.php";
  $obj_permisos = new permisos();
  $acceso = $obj_permisos->buscar_permiso($usuario, 'PDV', 'VER');

  if ($acceso == 'SI') {
    $sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
    $result_e = mysqli_query($conexion, $sql_e);
    $ver_e = mysqli_fetch_row($result_e);

    $rol = $ver_e[5];

    $sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado` FROM `caja` WHERE estado = 'ABIERTA'";
    $result = mysqli_query($conexion, $sql);
    $mostrar = mysqli_fetch_row($result);

    if ($mostrar != NULL) {

?>
      <div class="row m-0" id="div_row_mesas"></div>
      <div class="row m-1" id="div_row_ventas_dia" overflow-y: scroll; hidden></div>
      <div class="row m-0" id="div_cont_prod_cuenta" hidden="">
        <div class="col-md-7 mb-2 p-0 py-1">
          <div class="card" id="div_productos"></div>
        </div>
        <div class="col-md-5 p-1">
          <div class="card" id="div_cuenta"></div>
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

      <!-- Modal Generar factura-->
      <div class="modal fade" id="Modal_Generar_Factura" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header text-center">
              <h3 class="modal-title">Desea generar una factura a partir de esta venta?</h3>
            </div>
            <div class="modal-body">
              <input type="number" name="cod_venta_fact" id="cod_venta_fact" hidden="">
              <div class="row px-4 pb-3">
                <label class="col-label pr-2">Cliente: </label>
                <select class="col form-control select2" id="cod_cliente_fact" name="cod_cliente_fact" style="width: 80% !important">
                  <option value="">Buscar cliente </option>
                  <?php
                  $sql_clientes = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro`, `tipo`, `info` FROM `clientes` WHERE codigo != 0 order by nombre";
                  $result_clientes = mysqli_query($conexion, $sql_clientes);
                  while ($mostrar_clientes = mysqli_fetch_row($result_clientes)) {
                    $str_check = '';
                    $nombre = $mostrar_clientes[1] . ' - ' . $mostrar_clientes[2] . ' (' . $mostrar_clientes[3] . ')';
                  ?>
                    <option value="<?php echo $mostrar_clientes[0] ?>" <?php echo $str_check ?>><?php echo $nombre ?></option>
                  <?php
                  }
                  ?>
                </select>
              </div>
              <div class="row m-0 p-1">
                <div class="col text-center">
                  <button type="button" class="btn btn-sm btn-outline-secondary btn-round px-5" data-bs-dismiss="modal">NO</button>
                </div>
                <div class="col text-center">
                  <button type="button" class="btn btn-sm btn-outline-primary btn-round px-3" id="btn_generar_factura">SI, Generar</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal detalles de factura-->
      <div class="modal fade" id="Modal_factura" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
          <div class="modal-content" id="contenedor_pdf"></div>
        </div>
      </div>


      <script type="text/javascript">
        $(document).ready(function() {
          document.title = 'Punto de Venta | Restaurante PDV | Kuiik';
          $('.active').removeClass("active")
          document.getElementById('a_pdv').classList.add("active");

          //$(".select2").select2();

          document.getElementById('div_loader').style.display = 'block';
          $('#div_row_mesas').load('paginas/vistas_pdv/pdv_mesas.php', function() {
            cerrar_loader();
          });
          //document.getElementById('div_loader').style.display = 'block';
          //$('#div_row_ventas_dia').load('paginas/vistas_pdv/ventas_dia.php', function() {
          // cerrar_loader();
          //});

          $('#btnAgregar').click(function() {
            document.getElementById('div_loader').style.display = 'block';
            document.getElementById("btnAgregar").disabled = true;
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
                    titulo: 'Cliente Agregado Correctamente',
                    tipo: 'success'
                  });
                  $("#Modal_Nuevo_Cliente").modal('toggle');
                  var nuevo_cliente = new Option(datos['cedula'] + ' - ' + datos['nombre'] + datos['apellido'], datos['cod_cliente'], true, true);
                  $('#cod_cliente').append(nuevo_cliente).trigger('change');
                  $('#cod_cliente_fact').append(nuevo_cliente).trigger('change');
                  document.getElementById("btnAgregar").disabled = false;
                  cerrar_loader();
                  cod_mesa = document.getElementById("cod_mesa_cancel").value;
                  abrir_mesa(cod_mesa);
                } else {
                  w_alert({
                    titulo: datos['consulta'],
                    tipo: 'danger'
                  });
                  if (datos['consulta'] == 'Reload') {
                    document.getElementById('div_login').style.display = 'block';
                    cerrar_loader();

                  }
                  document.getElementById("btnAgregar").disabled = false;
                  cerrar_loader();
                }
              }
            });
          });

        });


        function agregar_producto() {
          document.getElementById('div_loader').style.display = 'block';
          cod_producto = document.getElementById("cod_producto_pedido").value;
          cod_mesa = document.getElementById("cod_mesa_pedido").value;
          cant = document.getElementById("cantidad_pedido").value;
          if (cant != '' && cant > 0) {
            $.ajax({
              type: "POST",
              data: "cod_producto=" + cod_producto + "&cod_mesa=" + cod_mesa + "&cant=" + cant,
              url: "procesos/agregar_producto_mesa.php",
              success: function(r) {
                datos = jQuery.parseJSON(r);
                if (datos['consulta'] == 1) {
                  document.body.style.overflow = "visible";
                  $('#btn_close_cant_producto').click();
                  mostrar_productos(datos['cod_categoria'], cod_mesa);
                  $('#div_cuenta').load('paginas/vistas_pdv/pdv_cuenta.php/?cod_mesa=' + cod_mesa, function() {
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
          $('.modal-backdrop').remove();
          document.querySelector("body").style.overflow = "auto";
        }

        function eliminar_item(num_item, cod_mesa) {
          document.getElementById('div_loader').style.display = 'block';
          $.ajax({
            type: "POST",
            data: "num_item=" + num_item + "&cod_mesa=" + cod_mesa,
            url: "procesos/eliminar_item.php",
            success: function(r) {
              datos = jQuery.parseJSON(r);
              if (datos['consulta'] == 1) {
                $('#div_cuenta').load('paginas/vistas_pdv/pdv_cuenta.php/?cod_mesa=' + cod_mesa, function() {
                  cerrar_loader();
                });
                mostrar_productos(datos['cod_categoria'], cod_mesa);
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

        function atras() {
          document.getElementById('div_loader').style.display = 'block';
          $('#div_row_mesas').load('paginas/vistas_pdv/pdv_mesas.php', function() {
            cerrar_loader();
          });
          //document.getElementById('div_loader').style.display = 'block';
          // $('#div_row_ventas_dia').load('paginas/vistas_pdv/ventas_dia.php', function() {
          //   cerrar_loader();
          //});
          document.getElementById('div_row_mesas').hidden = false;
          //document.getElementById('div_row_ventas_dia').hidden = false;
          document.getElementById('div_cont_prod_cuenta').hidden = true;
          document.getElementById('div_productos').innerHTML = "";
          document.getElementById('div_cuenta').innerHTML = "";
        }

        $('#btn_generar_factura').click(function() {
          cod_venta = document.getElementById("cod_venta_fact").value;
          document.getElementById('div_loader').style.display = 'block';
          $.ajax({
            type: "POST",
            data: "cod_venta=" + cod_venta + "&cod_cliente=" + cod_cliente,
            url: "procesos/generar_factura.php",
            success: function(r) {
              datos = jQuery.parseJSON(r);
              if (datos['consulta'] == 1) {
                //w_alert({ titulo: 'Factura Generada Correctamente', tipo: 'success' });
                document.querySelector("body").style.overflow = "auto";
                var modal = $("#Modal_Generar_Factura").is(":visible");
                if (modal)
                  $("#Modal_Generar_Factura").modal('toggle');
                $('.modal-backdrop').remove();
                cerrar_loader();
                cod_factura = datos['cod_factura'];
                generar_PDF_factura(cod_factura)

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
      </script>

    <?php
    } else {
    ?>
      <div class="row m-0 p-2">
        <div class="bg-warning p-2 rounded-pill text-center">
          <h4 class="text-white mb-0">No se pueden procesar Ventas, la caja no está ABIERTA</h4>
        </div>
      </div>
  <?php
    }
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