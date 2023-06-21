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
  $acceso = $obj_permisos->buscar_permiso($usuario,'Por Cobrar','VER');

  if($acceso == 'SI')
  {
    ?>

    <div id="div_tabla_cuentas"></div>

    <!-- Modal cobrar cuenta-->
    <div class="modal fade" id="Modal_Cobro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header text-center">
            <h5 class="modal-title">Seguro desea cobrar esta cuenta?</h5>
          </div>
          <div class="modal-body">
            <input type="number" name="cod_cuenta_cobro" id="cod_cuenta_cobro" hidden="">
            <div class="row">
              <button type="button" class="btn btn-sm btn-secondary btn-round btn-block col" data-bs-dismiss="modal">NO</button>
              <button type="button" class="btn btn-sm btn-outline-primary btn-round btn-block col" id="btnCobrar">SI, Cobrar</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Ingresar a caja-->
    <div class="modal fade" id="Modal_Ingreso" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header text-center">
            <h5 class="modal-title">Seguro desea ingresar esta cuenta?</h5>
          </div>
          <div class="modal-body">
            <input type="number" name="cod_cuenta_ingreso" id="cod_cuenta_ingreso" hidden="">
            <div class="row">
              <label>Método de pago</label>
              <select class="form-control form-control-sm" id="metodo_cuenta_ingreso" name="metodo_cuenta_ingreso">
                <option value="">Seleccione uno...</option>
                <option value="Efectivo">Efectivo</option>
                <option value="Tarjeta">Tarjeta</option>
                <option value="Nequi">Nequi</option>
                <option value="Bancolombia">Bancolombia</option>
                <option value="Daviplata">Daviplata</option>
              </select>
            </div>
            <div class="row mt-2">
              <button type="button" class="btn btn-sm btn-secondary btn-round btn-block col" data-bs-dismiss="modal">NO</button>
              <button type="button" class="btn btn-sm btn-outline-primary btn-round btn-block col" id="btnIngresar">SI, Ingresar</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Ingresar a caja-->
    <div class="modal fade" id="Modal_Dividir" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header text-center">
            <h5 class="modal-title">Seguro desea dividir esta cuenta?</h5>
          </div>
          <div class="modal-body">
            <input type="number" name="cod_cuenta_dividir" id="cod_cuenta_dividir" hidden="">
            <div class="row">
              <label>Nuevo Valor</label>
              <input type="text" class="form-control form-control-sm moneda" name="valor_nuevo" id="valor_nuevo" placeholder="Valor nuevo">
            </div>
            <div class="row mt-2">
              <button type="button" class="btn btn-sm btn-secondary btn-round btn-block col" data-bs-dismiss="modal">NO</button>
              <button type="button" class="btn btn-sm btn-outline-primary btn-round btn-block col" id="btnDividir">SI, Dividir</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script type="text/javascript">
     $(document).ready(function()
     {
      document.title = 'Cuentas por cobrar | Restaurante | W-POS | WitSoft';
      $('.active').removeClass("active")
      document.getElementById('a_por_cobrar').classList.add("active");

      document.getElementById('div_loader').style.display = 'block';
      $('#div_tabla_cuentas').load('tablas/por_cobrar.php', function(){cerrar_loader();});
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

     $('#btnCobrar').click(function()
     {
      document.getElementById('div_loader').style.display = 'block';
      cod_cuenta = document.getElementById("cod_cuenta_cobro").value;
      $.ajax({
        type:"POST",
        data:"cod_cuenta=" + cod_cuenta,
        url:"procesos/cobrar_cuenta.php",
        success:function(r)
        {
          datos=jQuery.parseJSON(r);
          if(datos['consulta'] == 1)
          {
            w_alert({ titulo: 'Cuenta Cobrada Correctamente', tipo: 'success' });
            $('#div_tabla_cuentas').load('tablas/por_cobrar.php', function(){cerrar_loader();});
            $("#Modal_Cobro").modal('toggle');
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

     $('#btnIngresar').click(function()
     {
      document.getElementById('div_loader').style.display = 'block';
      cod_cuenta = document.getElementById("cod_cuenta_ingreso").value;
      metodo = document.getElementById("metodo_cuenta_ingreso").value;
      if(metodo != '')
      {
        $.ajax({
          type:"POST",
          data:"cod_cuenta=" + cod_cuenta+"&metodo=" + metodo,
          url:"procesos/ingresar_cuenta.php",
          success:function(r)
          {
            datos=jQuery.parseJSON(r);
            if(datos['consulta'] == 1)
            {
              w_alert({ titulo: 'Cuenta Ingresada Correctamente', tipo: 'success' });
              $('#div_tabla_cuentas').load('tablas/por_cobrar.php', function(){cerrar_loader();});
              $("#Modal_Ingreso").modal('toggle');
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
      }
      else
      {
        w_alert({ titulo: 'Seleccione un método de pago', tipo: 'danger' });
        document.getElementById("metodo_cuenta_ingreso").focus();
        cerrar_loader();
      }

    });

     $('#btnDividir').click(function()
     {
      document.getElementById('div_loader').style.display = 'block';
      cod_cuenta = document.getElementById("cod_cuenta_dividir").value;
      valor_nuevo = document.getElementById("valor_nuevo").value;
      if(valor_nuevo != '')
      {
        $.ajax({
          type:"POST",
          data:"cod_cuenta=" + cod_cuenta+"&valor_nuevo=" + valor_nuevo,
          url:"procesos/dividir_credito.php",
          success:function(r)
          {
            datos=jQuery.parseJSON(r);
            if(datos['consulta'] == 1)
            {
              w_alert({ titulo: 'Cuenta Dividida Correctamente', tipo: 'success' });
              $('#div_tabla_cuentas').load('tablas/por_cobrar.php', function(){cerrar_loader();});
              $("#Modal_Dividir").modal('toggle');
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
      }
      else
      {
        w_alert({ titulo: 'Ingrese un valor', tipo: 'danger' });
        document.getElementById("valor_nuevo").focus();
        cerrar_loader();
      }

    });

     function generar_factura(cod_venta)
     {
      document.getElementById('div_loader').style.display = 'block';
      $.ajax({
        type:"POST",
        data:"cod_venta=" + cod_venta,
        url:"procesos/generar_factura.php",
        success:function(r)
        {
          datos=jQuery.parseJSON(r);
          if(datos['consulta'] == 1)
          {
            w_alert({ titulo: 'Factura Generada Correctamente', tipo: 'success' });
            $("#Modal_venta").modal('toggle');
            $('.modal-backdrop').remove();
            document.querySelector("body").style.overflow = "auto";
            click_item('facturas');
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