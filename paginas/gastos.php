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
  
  require_once "../clases/permisos.php";
  $obj_permisos = new permisos();
  $acceso = $obj_permisos->buscar_permiso($usuario,'Gastos','VER');

  if($acceso == 'SI')
  {
    ?>

    <div id="div_tabla_gastos"></div>

    <!-- Modal Nuevo gasto-->
    <div class="modal fade" id="Modal_Nuevo_Gasto" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-center">
            <h5 class="modal-title">Agregar Nuevo Gasto</h5>
          </div>
          <div class="modal-body bg-white">
            <form id="frmnuevo" autocomplete="off">
              <div class="row form-group form-group-sm">
                <div class="form-line col-md-8">
                  <label><span class="requerido">*</span>Descripción:</label>
                  <input type="text" class="form-control form-control-sm" id="descripcion_gasto" name="descripcion_gasto">
                </div>
                <div class="form-line col-md-4">
                  <label><span class="requerido">*</span>Valor:</label>
                  <input type="text" class="form-control moneda" id="valor_gasto" name="valor_gasto">
                </div>
              </div>
              <div class="row form-group form-group-sm">
                <div class="form-line col-md-8">
                  <label>Num Factura:</label>
                  <input type="text" class="form-control form-control-sm" id="num_factura_gasto" name="num_factura_gasto">
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary btn-round" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-sm btn-outline-primary btn-round" data-bs-dismiss="modal" id="btnAgregar">Agregar Gasto</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Editar gasto-->
    <div class="modal fade" id="Modal_Editar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-center">
            <h5 class="modal-title">Editar Gasto</h5>
          </div>
          <div class="modal-body">
            <form id="frmnuevo_U" autocomplete="off">
              <input type="text" name="cod_gasto_U" id="cod_gasto_U" hidden="">
              <div class="row form-group form-group-sm">
                <div class="form-line col-md-8">
                  <label>Descripción:</label>
                  <input type="text" class="form-control form-control-sm" id="descripcion_gasto_U" name="descripcion_gasto_U">
                </div>
                <div class="form-line col-md-4">
                  <label>Valor:</label>
                  <input type="text" class="form-control moneda" id="valor_gasto_U" name="valor_gasto_U">
                </div>
              </div>
              <div class="row form-group form-group-sm">
                <div class="form-line col-md-8">
                  <label>Num Factura:</label>
                  <input type="text" class="form-control form-control-sm" id="num_factura_gasto_U" name="num_factura_gasto_U">
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary btn-round" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-sm btn-outline-primary btn-round" data-bs-dismiss="modal" id="btnEditar">Editar Gasto</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Eliminar gasto-->
    <div class="modal fade" id="Modal_Eliminar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-center">
            <h3 class="modal-title">Seguro desea eliminar este Gasto?</h3>
          </div>
          <div class="modal-body">
            <input type="number" name="cod_gasto_delete" id="cod_gasto_delete" hidden="">
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
      document.title = 'Gastos | Restaurante | W-POS | WitSoft';
      $('.active').removeClass("active")
      document.getElementById('gastos').classList.add("active");

      document.getElementById('div_loader').style.display = 'block';
      $('#div_tabla_gastos').load('tablas/gastos.php', function(){cerrar_loader();});
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

     $('#valor_gasto').keypress(function(e){
      if(e.keyCode==13)
        $('#btnAgregar').click();
    });

     $('#btnAgregar').click(function()
     {
      document.getElementById('div_loader').style.display = 'block';
      document.getElementById("btnAgregar").disabled = true;
      datos=$('#frmnuevo').serialize();
      $.ajax({
        type:"POST",
        data:datos,
        url:"procesos/agregar.php",
        success:function(r)
        {
          datos=jQuery.parseJSON(r);
          if(datos['consulta'] == 1)
          {
            $('#frmnuevo')[0].reset();
            w_alert({ titulo: 'Gasto Agregado Correctamente', tipo: 'success' });
            $('#div_tabla_gastos').load('tablas/gastos.php', function(){cerrar_loader();});
            $("#Modal_Nuevo_Gasto").modal('toggle');
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

     $('#btnEditar').click(function()
     {
      document.getElementById('div_loader').style.display = 'block';
      document.getElementById("btnEditar").disabled = true;
      datos=$('#frmnuevo_U').serialize();
      $.ajax({
        type:"POST",
        data:datos,
        url:"procesos/actualizar.php",
        success:function(r)
        {
          datos=jQuery.parseJSON(r);
          if(datos['consulta'] == 1)
          {
            $('#frmnuevo_U')[0].reset();
            w_alert({ titulo: 'Gasto Actualizado Correctamente', tipo: 'success' });
            $('#div_tabla_gastos').load('tablas/gastos.php', function(){cerrar_loader();});
            $("#Modal_Editar").modal('toggle');
            document.getElementById("btnEditar").disabled = false;
          }
          else
          {
            w_alert({ titulo: datos['consulta'], tipo: 'danger' });
            if(datos['consulta'] == 'Reload')
            {
              document.getElementById('div_login').style.display = 'block';
cerrar_loader();
              
            }
            document.getElementById("btnEditar").disabled = false;
            cerrar_loader();
          }
        }
      });

    });

     $('#btnEliminar').click(function()
     {
      document.getElementById('div_loader').style.display = 'block';
      cod_gasto = document.getElementById("cod_gasto_delete").value;
      $.ajax({
        type:"POST",
        data:"cod_gasto=" + cod_gasto,
        url:"procesos/eliminar.php",
        success:function(r)
        {
          datos=jQuery.parseJSON(r);
          if(datos['consulta'] == 1)
          {
            w_alert({ titulo: 'Gasto Eliminado Correctamente', tipo: 'success' });
            $('#div_tabla_gastos').load('tablas/gastos.php', function(){cerrar_loader();});
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

     function actualizar_gasto(cod_gasto)
     {
      document.getElementById("btnEditar").disabled = false;
      $.ajax({
        type:"POST",
        data:"cod_gasto=" + cod_gasto,
        url:"procesos/obtener_datos.php",
        success:function(r){
          datos=jQuery.parseJSON(r);
          $('#cod_gasto_U').val(datos['codigo']);
          $('#descripcion_gasto_U').val(datos['descripcion']);
          $('#valor_gasto_U').val(datos['valor']);
          $('#num_factura_gasto_U').val(datos['num_factura']);
        }
      });
    }


  </script>


  <?php 
}
else
  require_once 'error_403.php';
}
else
{
  ?>
  <script type="text/javascript">
    document.getElementById('div_login').style.display = 'block';
    cerrar_loader();
    
  </script>
  <?php 
}
?>