<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');

require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();

$num_tabla = 1;
session_set_cookie_params(7*24*60*60);
session_start();

if(isset($_SESSION['usuario_restaurante']))
{
  $usuario = $_SESSION['usuario_restaurante'];
  ?>

  <div id="div_tabla_categorias"></div>

  <!-- Modal Nueva Categoría-->
  <div class="modal fade" id="Modal_Nueva_Categoria" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header text-center">
          <h5 class="modal-title">Agregar Nueva Categoría</h5>
        </div>
        <div class="modal-body pb-1">
          <form id="frmnueva" autocomplete="off">
            <div class="form-group">
              <div class="form-line">
                <label><span class="requerido">*</span>Nombre:</label>
                <input type="text" class="form-control form-control-sm" id="nombre_categoria" name="nombre_categoria">
              </div>
            </div>
          </form>
          <span class="requerido">*</span>Campo Requerido
        </div>
        <div class="modal-footer">
          <div class="justify-content: flex-end;"></div>
          <button type="button" class="btn btn-sm btn-secondary btn-round" data-bs-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btnAgregar">Agregar Categoría</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Editar categoria-->
  <div class="modal fade" id="Modal_Editar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header text-center">
          <h5 class="modal-title">Editar Categoría</h5>
        </div>
        <div class="modal-body">
          <form id="frmnuevo_U" autocomplete="off">
            <input type="text" name="cod_categoria_U" id="cod_categoria_U" hidden="">
            <div class="form-group form-group-sm">
              <div class="form-line">
                <label>Descripción:</label>
                <input type="text" class="form-control form-control-sm" id="descripcion_categoria_U" name="descripcion_categoria_U">
              </div>
            </div>
            <div class="form-group form-group-sm">
              <div class="form-line">
                <label>Inventario (Gr):</label>
                <input type="text" class="form-control form-control-sm" id="inventario_categoria_U" name="inventario_categoria_U">
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-secondary btn-round" data-bs-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-sm btn-outline-primary btn-round" data-bs-dismiss="modal" id="btnEditar">Editar Categoría</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Eliminar categoria-->
  <div class="modal fade" id="Modal_Eliminar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header text-center">
          <h3 class="modal-title">Seguro desea eliminar este Categoría?</h3>
        </div>
        <div class="modal-body">
          <input type="number" name="cod_categoria_delete" id="cod_categoria_delete" hidden="">
          <div class="row">
            <div class="col">
              <button type="button" class="btn btn-sm btn-secondary btn-round btn-block" data-bs-dismiss="modal">NO</button>
            </div>
            <div class="col">
              <button type="button" class="btn btn-sm btn-outline-primary btn-round btn-block" id="btnEliminar">SI, Eliminar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript">
   $(document).ready(function()
   {
    document.title = 'Categorías | Restaurante | W-POS | Kuiik';
    $('.active').removeClass("active")
    document.getElementById('categorias').classList.add("active");

    document.getElementById('div_loader').style.display = 'block';
    $('#div_tabla_categorias').load('tablas/categorias.php', function(){cerrar_loader();});
  });


   $('input.moneda').keyup(function(event)
   {
    if(event.which >= 37 && event.which <= 40)
    {
      event.preventDefault();
    }
    $(this).val(function(index, value)
    {
      return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
    });
  });

   $('#btnAgregar').click(function()
   {
    document.getElementById('div_loader').style.display = 'block';
    document.getElementById("btnAgregar").disabled = true;
    datos=$('#frmnueva').serialize();
    $.ajax({
      type:"POST",
      data:datos,
      url:"procesos/agregar.php",
      success:function(r)
      {
        datos=jQuery.parseJSON(r);
        if(datos['consulta'] == 1)
        {
          $('#frmnueva')[0].reset();
          w_alert({ titulo: 'Categoría Agregada Correctamente', tipo: 'success' });
          $("#Modal_Nueva_Categoria").modal('toggle');
          $('#div_tabla_categorias').load('tablas/categorias.php', function(){cerrar_loader();});
          document.getElementById("btnAgregar").disabled = false;
        }
        else
        {
          w_alert({ titulo: datos['consulta'], tipo: 'danger' });
          if(datos['consulta'] == 'Reload')
          {
            document.getElementById('div_login').style.display = 'block';
cerrar_loader();
            
          }
          document.getElementById("btnAgregar").disabled = false;
          cerrar_loader();
        }
      }
    });
  });

   $('#btnEliminar').click(function()
   {
    document.getElementById('div_loader').style.display = 'block';
    cod_categoria = document.getElementById("cod_categoria_delete").value;
    $.ajax({
      type:"POST",
      data:"cod_categoria=" + cod_categoria,
      url:"procesos/eliminar.php",
      success:function(r)
      {
        datos=jQuery.parseJSON(r);
        if(datos['consulta'] == 1)
        {
          w_alert({ titulo: 'Categoría Eliminada Correctamente', tipo: 'success' });
          $('#div_tabla_categorias').load('tablas/categorias.php', function(){cerrar_loader();});
          $("#Modal_Eliminar").modal('toggle');
        }
        else
        {
          w_alert({ titulo: datos['consulta'], tipo: 'danger' });
          if(datos['consulta'] == 'Reload')
          {
            document.getElementById('div_login').style.display = 'block';
cerrar_loader();
            
          }
          cerrar_loader();
        }
      }
    });
  });

</script>


<?php 
}
else
{
  header("Location:login.php");
} 
?>