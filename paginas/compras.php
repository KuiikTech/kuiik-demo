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
  $acceso = $obj_permisos->buscar_permiso($usuario,'Compras','VER');

  if($acceso == 'SI')
  {

    ?>

    <div id="div_tabla_compras"></div>

    <!-- Modal Ver Compra-->
    <div class="modal fade" id="Modal_Ver" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="overflow-y: scroll;">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="div_ver_compra">
        </div>
      </div>
    </div>

    <script type="text/javascript">
     $(document).ready(function()
     {
       document.title = 'Compras | Restaurante | W-POS | Kuiik';
       $('.active').removeClass("active")
       document.getElementById('a_compras').classList.add("active");

       document.getElementById('div_loader').style.display = 'block';
       $('#div_tabla_compras').load('tablas/compras.php', function(){cerrar_loader();});
     });

     //$('.select2').select2();

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

     function cambiar_estado_compra(estado,cod_compra)
     {
       $.ajax({
        type:"POST",
        data:"cod_compra=" + cod_compra + "&estado=" + estado,
        url:"procesos/cambiar_estado_compra.php",
        success:function(r)
        {
          datos=jQuery.parseJSON(r);
          if(datos['consulta'] == 1)
          {
            w_alert({ titulo: 'Estado cambiado', tipo: 'success' });
            click_item('compras');
          }
          else
          {
            w_alert({ titulo: datos['consulta'], tipo: 'danger' });
            if(datos['consulta'] == 'Reload')
            {
              document.getElementById('div_login').style.display = 'block';
              cerrar_loader();
              
            }
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