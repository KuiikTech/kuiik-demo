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
  $acceso = $obj_permisos->buscar_permiso($usuario, 'Productos', 'VER');

  if ($acceso == 'SI') {
    $sql_categorias = "SELECT `cod_categoria`, `nombre` FROM `categorias_productos`";
    $result_categorias = mysqli_query($conexion, $sql_categorias);

    $sql_categorias_U = "SELECT `cod_categoria`, `nombre` FROM `categorias_productos`";
    $result_categorias_U = mysqli_query($conexion, $sql_categorias_U);

?>

    <div id="div_tabla_productos"></div>

    <!-- Modal Nuevo producto-->
    <div class="modal fade" id="Modal_Nuevo_Producto" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header text-center p-2">
            <h5 class="modal-title">Agregar Nuevo Producto</h5>
          </div>
          <div class="modal-body pb-1">
            <form id="frmnuevo" autocomplete="off">
              <div class="form-group form-group-sm mb-0">
                <div class="form-line">
                  <label><span class="requerido">*</span>Descripción:</label>
                  <input type="text" class="form-control form-control-sm" id="descripcion_producto" name="descripcion_producto">
                </div>
              </div>
              <div class="form-group form-group-sm mb-0">
                <div class="form-line">
                  <label><span class="requerido">*</span>Categoría:
                    <button type="button" class="btn btn-sm btn-outline-primary btn-round p-0" data-bs-toggle="modal" data-bs-target="#Modal_Nueva_Categoria">
                      <span class="fa fa-plus"></span>
                    </button>
                  </label>
                  <select class="form-control form-control-sm select2" id="categoria_producto" name="categoria_producto">
                    <option value="">Seleccionar Categoría</option>
                    <?php
                    while ($ver_categorias = mysqli_fetch_row($result_categorias)) {
                    ?>
                      <option value="<?php echo $ver_categorias[0] ?>"><?php echo $ver_categorias[1] ?></option>
                    <?php
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="form-group form-group-sm mb-0">
                <div class="form-line">
                  <label>Valor:</label>
                  <input type="text" class="form-control form-control-sm moneda" id="valor_producto" name="valor_producto">
                </div>
              </div>
              <div class="form-group form-group-sm mb-0">
                <div class="form-line">
                  <label>Barcode:</label>
                  <input type="text" class="form-control form-control-sm" id="barcode" name="barcode">
                </div>
              </div>
              <div class="form-group form-group-sm mb-0">
                <label><span class="requerido">*</span>Tipo</label>
                <select class="form-control" id="tipo_producto" name="tipo_producto">
                  <option value="">Selecionar Tipo</option>
                  <option value="Preparación">Preparación</option>
                  <option value="Producto">Producto</option>
                </select>
              </div>
              <div class="form-group form-group-sm mb-0">
                <label><span class="requerido">*</span>Area</label>
                <select class="form-control" id="area_producto" name="area_producto">
                  <option value="">Selecionar Area</option>
                  <option value="Horno">Horno</option>
                  <option value="Cocina">Cocina</option>
                  <option value="Bar">Bar</option>
                  <option value="Horno2">Horno 2</option>
                </select>
              </div>

            </form>
            <br>
            <span class="requerido">*</span>Campo Requerido
          </div>
          <div class="modal-footer p-2">
            <div class="justify-content: flex-end;"></div>
            <button type="button" class="btn btn-sm btn-secondary btn-round" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btnAgregar">Agregar Producto</button>
          </div>
        </div>
      </div>
    </div>


    <!-- Modal Editar producto-->
    <div class="modal fade" id="Modal_Editar" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header text-center p-2">
            <h5 class="modal-title">Editar Producto</h5>
          </div>
          <div class="modal-body">
            <form id="frmnuevo_U" autocomplete="off">
              <input type="text" name="cod_producto_U" id="cod_producto_U" hidden="">
              <div class="form-group form-group-sm mb-0">
                <div class="form-line">
                  <label><span class="requerido">*</span>Descripción:</label>
                  <input type="text" class="form-control form-control-sm" id="descripcion_producto_U" name="descripcion_producto_U">
                </div>
              </div>
              <div class="form-group form-group-sm mb-0">
                <div class="form-line">
                  <label><span class="requerido">*</span>Categoría:
                    <button type="button" class="btn btn-sm btn-outline-primary btn-round p-0" data-bs-toggle="modal" data-bs-target="#Modal_Nueva_Categoria">
                      <span class="fa fa-plus"></span>
                    </button>
                  </label>
                  <select class="form-control form-control-sm select2" id="categoria_producto_U" name="categoria_producto_U">
                    <option value="">Seleccionar Categoría</option>
                    <?php
                    while ($ver_categorias_U = mysqli_fetch_row($result_categorias_U)) {
                    ?>
                      <option value="<?php echo $ver_categorias_U[0] ?>"><?php echo $ver_categorias_U[1] ?></option>
                    <?php
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="form-group form-group-sm mb-0">
                <div class="form-line">
                  <label>Valor:</label>
                  <input type="text" class="form-control form-control-sm moneda" id="valor_producto_U" name="valor_producto_U">
                </div>
              </div>
              <div class="form-group form-group-sm mb-0">
                <div class="form-line">
                  <label>Barcode:</label>
                  <input type="text" class="form-control form-control-sm" id="barcode_U" name="barcode_U">
                </div>
              </div>
              <div class="form-group form-group-sm mb-0">
                <label><span class="requerido">*</span>Tipo</label>
                <select class="form-control" id="tipo_producto_U" name="tipo_producto_U">
                  <option value="">Selecionar Tipo</option>
                  <option value="Preparación">Preparación</option>
                  <option value="Producto">Producto</option>
                </select>
              </div>
              <div class="form-group form-group-sm mb-0">
                <label><span class="requerido">*</span>Area</label>
                <select class="form-control" id="area_producto_U" name="area_producto_U">
                  <option value="">Selecionar Area</option>
                  <option value="Horno">Horno</option>
                  <option value="Cocina">Cocina</option>
                  <option value="Bar">Bar</option>
                  <option value="Horno2">Horno 2</option>
                </select>
              </div>
            </form>
            <span class="requerido">*</span>Campo Requerido
          </div>
          <div class="modal-footer p-2">
            <button type="button" class="btn btn-sm btn-secondary btn-round" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-sm btn-outline-primary btn-round" data-bs-dismiss="modal" id="btnEditar">Editar Producto</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Eliminar producto-->
    <div class="modal fade" id="Modal_Eliminar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header text-center p-2">
            <h5 class="modal-title">Seguro desea eliminar este Producto?</h5>
          </div>
          <div class="modal-body">
            <input type="number" name="cod_producto_delete" id="cod_producto_delete" hidden="">
            <div class="row">
              <div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6 text-center">
                <button type="button" class="btn btn-sm btn-secondary btn-round px-4" data-bs-dismiss="modal">NO</button>
              </div>
              <div class="col-sm-6 col-xs-6 col-md-6 col-lg-6 col-6 text-center">
                <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btnEliminar">SI, Eliminar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Nueva Categoría-->
    <div class="modal fade" id="Modal_Nueva_Categoria" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header text-center">
            <h5 class="modal-title">Agregar Nueva Categoría</h5>
          </div>
          <div class="modal-body pb-1">
            <form id="frmnueva_cat" autocomplete="off">
              <div class="form-group">
                <div class="form-line">
                  <label><span class="requerido">*</span>Nombre:</label>
                  <input type="text" class="form-control form-control-sm" id="nombre_categoria" name="nombre_categoria">
                </div>
              </div>
            </form>
            <span class="requerido">*</span>Campo Requerido
          </div>
          <div class="modal-footer p-2">
            <div class="justify-content: flex-end;"></div>
            <button type="button" class="btn btn-sm btn-secondary btn-round" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btnAgregar_cat">Agregar Categoría</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Ver-->
    <div class="modal fade" id="Modal_Ver" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" id="div_modal_producto">
        </div>
      </div>
    </div>

    <!-- Modal Editar Stock-->
    <div class="modal fade" id="Modal_Edit_Stock" tabindex="0" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="div_edit_stock"></div>
      </div>
    </div>

    <script type="text/javascript">
      $(document).ready(function() {
        document.title = 'Productos | Restaurante | W-POS | WitSoft';
        $('.active').removeClass("active")
        document.getElementById('a_productos').classList.add("active");

        document.getElementById('div_loader').style.display = 'block';
        $('#div_tabla_productos').load('tablas/productos.php', function() {
          cerrar_loader();
        });
      });

      //$('.select2').select2();

      $('input.moneda').keyup(function(event) {
        if (event.which >= 37 && event.which <= 40) {
          event.preventDefault();
        }
        $(this).val(function(index, value) {
          return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
        });
      });

      $('#btnAgregar').click(function() {
        document.getElementById('div_loader').style.display = 'block';
        document.getElementById("btnAgregar").disabled = true;
        datos_i = new FormData($("#frmnuevo")[0]);
        $.ajax({
          type: "POST",
          data: datos_i,
          contentType: false,
          processData: false,
          url: "procesos/agregar_producto.php",
          success: function(r) {
            datos = jQuery.parseJSON(r);
            if (datos['consulta'] == 1) {
              $('#frmnuevo')[0].reset();
              w_alert({
                titulo: 'Producto Agregado Correctamente',
                tipo: 'success'
              });
              $('#div_tabla_productos').load('tablas/productos.php', function() {
                cerrar_loader();
              });
              $("#Modal_Nuevo_Producto").modal('toggle');
              document.getElementById("btnAgregar").disabled = false;
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
              cerrar_loader()
            }
          }
        });
      });

      $('#btnAgregar_cat').click(function() {
        document.getElementById('div_loader').style.display = 'block';
        document.getElementById("btnAgregar_cat").disabled = true;
        datos = $('#frmnueva_cat').serialize();
        $.ajax({
          type: "POST",
          data: datos,
          url: "procesos/agregar.php",
          success: function(r) {
            datos = jQuery.parseJSON(r);
            if (datos['consulta'] == 1) {
              $('#frmnueva_cat')[0].reset();
              w_alert({
                titulo: 'Categoría Agregada Correctamente',
                tipo: 'success'
              });
              $("#Modal_Nueva_Categoria").modal('toggle');
              var nueva_categoria = new Option(datos['nombre'], datos['cod_categoria'], true, true);
              $('#categoria_producto').append(nueva_categoria).trigger('change');
              document.getElementById("btnAgregar_cat").disabled = false;
            } else {
              w_alert({
                titulo: datos['consulta'],
                tipo: 'danger'
              });
              if (datos['consulta'] == 'Reload') {
                document.getElementById('div_login').style.display = 'block';
                cerrar_loader();
                
              }
              document.getElementById("btnAgregar_cat").disabled = false;
            }
            cerrar_loader();
          }
        });
      });

      $('#btnEditar').click(function() {
        document.getElementById('div_loader').style.display = 'block';
        document.getElementById("btnEditar").disabled = true;
        datos_i = new FormData($("#frmnuevo_U")[0]);
        $.ajax({
          type: "POST",
          data: datos_i,
          contentType: false,
          processData: false,
          url: "procesos/editar_producto.php",
          success: function(r) {
            datos = jQuery.parseJSON(r);
            if (datos['consulta'] == 1) {
              $('#frmnuevo_U')[0].reset();
              w_alert({
                titulo: 'Producto Actualizado Correctamente',
                tipo: 'success'
              });
              $('#div_tabla_productos').load('tablas/productos.php', function() {
                cerrar_loader();
              });
              $("#Modal_Editar").modal('toggle');
              document.getElementById("btnEditar").disabled = false;
            } else {
              w_alert({
                titulo: datos['consulta'],
                tipo: 'danger'
              });
              if (datos['consulta'] == 'Reload') {
                document.getElementById('div_login').style.display = 'block';
                cerrar_loader();
                
              }
              document.getElementById("btnEditar").disabled = false;
            }
            cerrar_loader();
          }
        });

      });

      $('#btnEliminar').click(function() {
        document.getElementById('div_loader').style.display = 'block';
        cod_producto = document.getElementById("cod_producto_delete").value;
        $.ajax({
          type: "POST",
          data: "cod_producto=" + cod_producto,
          url: "procesos/eliminar.php",
          success: function(r) {
            datos = jQuery.parseJSON(r);
            if (datos['consulta'] == 1) {
              w_alert({
                titulo: 'Producto Eliminado Correctamente',
                tipo: 'success'
              });
              $('#div_tabla_productos').load('tablas/productos.php', function() {
                cerrar_loader();
              });
              $("#Modal_Eliminar").modal('toggle');
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

      function actualizar_producto(cod_producto) {
        document.getElementById("btnEditar").disabled = false;
        $.ajax({
          type: "POST",
          data: "cod_producto=" + cod_producto,
          url: "procesos/obtener_datos.php",
          success: function(r) {
            datos = jQuery.parseJSON(r);
            $('#cod_producto_U').val(datos['codigo']);
            $('#descripcion_producto_U').val(datos['descripcion']);
            $('#valor_producto_U').val(datos['valor']);
            $('#categoria_producto_U').val(datos['categoria']).trigger('change');
            $('#barcode_U').val(datos['barcode']);
            $('#area_producto_U').val(datos['area']);
            $('#tipo_producto_U').val(datos['tipo']);
          }
        });
      }

      function cambiar_estado(cod_producto) {
        $.ajax({
          type: "POST",
          data: "cod_producto=" + cod_producto,
          url: "procesos/cambiar_estado.php",
          success: function(r) {
            datos = jQuery.parseJSON(r);
            if (datos['consulta'] == 1) {
              document.getElementById('btn_estado_' + cod_producto).innerHTML = datos['estado'];
              document.getElementById('btn_estado_' + cod_producto).classList.remove("btn-success");
              document.getElementById('btn_estado_' + cod_producto).classList.remove("btn-danger");
              if (datos['estado'] == 'DISPONIBLE')
                document.getElementById('btn_estado_' + cod_producto).classList.add("btn-success");
              else
                document.getElementById('btn_estado_' + cod_producto).classList.add("btn-danger");
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