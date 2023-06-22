<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');

require_once "../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();

$num_tabla = 1;
session_set_cookie_params(7*24*60*60);
session_start();

if(isset($_SESSION['usuario_restaurante']))
{
  $usuario = $_SESSION['usuario_restaurante'];

  $sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `foto`, `telefono`, `rol`, `fecha_registro`, `estado`, `color` FROM `usuarios` WHERE codigo = '$usuario'";
  $result_e=mysqli_query($conexion,$sql_e);
  $ver_e=mysqli_fetch_row($result_e);

  $cedula = $ver_e[1];
  $nombre = $ver_e[2];
  $apellido = $ver_e[3];
  $telefono = $ver_e[6];
  $rol = $ver_e[7];
  $imagen_usuario = $ver_e[5];
  $estado = $ver_e[9];
  $color = $ver_e[10];

  if ($imagen_usuario == '')
    $imagen_usuario = 'user.svg';
  ?>

  <div class="row">
    <div class="col-lg-4 pr-0">
      <div class="card card-small mb-4 pt-3">
        <div class="card-header border-bottom text-center">
          <div class="mb-3 mx-auto">
            <img class="rounded-circle" id="img_usuario" src="recursos/user/<?php echo $imagen_usuario ?>?nocache=<?php echo time(); ?>" alt="User photo" width="110">
          </div>
          <div class="float-end">
            <button class="btn btn-outline-warning btn-round p-1" onclick="document.getElementById('imagen_usuario').hidden = false;this.hidden = true;">
              <span class="fa fa-image"></span>
            </button>
          </div>
          <div class="row">
            <form id="form_foto">
              <input type="file" class="form-control form-control-sm" hidden="" name="imagen_usuario" id="imagen_usuario" accept="image/png, .jpeg, .jpg" onchange="guardar_foto();">
            </form>

          </div>
          
          <div class="mb-3 mx-auto">
            <h4 class="mb-0 mt-2"><?php echo $nombre.' '.$apellido ?></h4>
            <span class="badge bg-dark text-white">Identificación: <b><?php echo $cedula ?></b></span>
            <span class="text-dark d-block mb-2"><?php echo $rol ?></span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="card card-small mb-4">
        <div class="card-header border-bottom">
          <h6 class="m-0 p-0">Detalles de la cuenta</h6>
        </div>
        <ul class="list-group list-group-flush">
          <li class="list-group-item p-3">
            <div class="row">
              <div class="col">
                <form id="form_info">
                  <input type="number" id="cod_usuario" name="cod_usuario" value="<?php echo $usuario ?>" hidden>
                  <div class="row">
                    <div class="form-group col-md-6 col-6 col-sm-6 col-lg-6">
                      <label for="input_nombre">Nombre</label>
                      <input type="text" class="form-control form-control-sm" id="input_nombre" name="input_nombre" value="<?php echo $nombre ?>" autocomplete="off">
                    </div>
                    <div class="form-group col-md-6 col-6 col-sm-6 col-lg-6">
                      <label for="input_apellido">Apellido</label>
                      <input type="text" class="form-control form-control-sm" id="input_apellido" name="input_apellido" value="<?php echo $apellido ?>" autocomplete="off">
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-md-6 col-6 col-sm-6 col-lg-6">
                      <label for="input_telefono">Telefono</label>
                      <input type="text" class="form-control form-control-sm" id="input_telefono" name="input_telefono" value="<?php echo $telefono ?>" autocomplete="off">
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-md-6 col-6 col-sm-6 col-lg-6">
                      <label for="input_telefono">Color</label>
                      <input type="color" class="form-control form-control-sm col-1" id="input_color" name="input_color" value="<?php echo $color ?>" autocomplete="off" style="width: 50px;">
                    </div>
                  </div>
                </form>
                <div class="text-center">
                  <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_guargar_cambios">Guardar Cambios</button>
                </div>
                <hr>
                <form id="form_pass">
                  <div class="form-group col-md-6">
                    <label for="input_pass_old">Contraseña Actual</label>
                    <input type="password" class="form-control form-control-sm" id="input_pass_old" name="input_pass_old" autocomplete="off">
                  </div>
                  <div class="row">
                   <div class="form-group col-md-6">
                    <label for="input_pass_new">Contraseña Nueva</label>
                    <input type="password" class="form-control form-control-sm" id="input_pass_new" name="input_pass_new" autocomplete="off">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="input_pass_new_2">Confirmar Contraseña Nueva</label>
                    <input type="password" class="form-control form-control-sm" id="input_pass_new_2" name="input_pass_new_2" autocomplete="off">
                  </div>
                </div>
                <div class="text-center">
                  <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_cambiar_pass">Cambiar Contraseña</button>
                </div>
              </form>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>

<script type="text/javascript">
 document.getElementById('img_usuario').onerror = imagen_defecto;

 function imagen_defecto(e)
 {
  e.target.src = 'recursos/user/user.svg';
}

$(document).ready(function()
{
  document.title = 'Perfil | Restaurante | Kuiik';
  $('.active').removeClass("active")
  document.getElementById('a_perfil').classList.add("active");
});

$('#btn_cambiar_pass').click(function()
{
  document.getElementById("btn_cambiar_pass").disabled = true;
  datos=$('#form_pass').serialize();
  $.ajax({
    type:"POST",
    data:datos,
    url:"procesos/cambiar_pass.php",
    success:function(r)
    {
      datos=jQuery.parseJSON(r);
      if (datos['consulta'] == 1)
      {
        w_alert({ titulo: 'Contraseña Cambiada', tipo: 'success' });
        click_item('perfil');
      }
      else
      {
        if(datos['consulta'] == 'Reload')
          location.reload();
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
      document.getElementById("btn_cambiar_pass").disabled = false;
    }
  });

});

$('#btn_guargar_cambios').click(function()
{
  document.getElementById("btn_guargar_cambios").disabled = true;
  datos=$('#form_info').serialize();
  $.ajax({
    type:"POST",
    data:datos,
    url:"procesos/cambiar_info.php",
    success:function(r)
    {
      datos=jQuery.parseJSON(r);
      if (datos['consulta'] == 1)
      {
        w_alert({ titulo: 'Cambios guardados correctamente', tipo: 'success' });
        click_item('perfil');
      }
      else
      {
        if(datos['consulta'] == 'Reload')
          location.reload();
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
      document.getElementById("btn_guargar_cambios").disabled = false;
    }
  });

});

function guardar_foto()
{
  document.getElementById('div_loader').style.display = 'block';

  var datos = new FormData($("#form_foto")[0]);

  var peticion = new XMLHttpRequest();

  peticion.addEventListener("load",proceso_completo,false);
  peticion.addEventListener("error",error_carga,false);
  peticion.addEventListener("abort",carga_abortada,false);

  peticion.open("POST","procesos/cargar_foto.php/?cod_usuario=<?php echo $usuario ?>");
  peticion.send(datos);
}

function proceso_completo(event)
{
  datos=jQuery.parseJSON(event.target.responseText);
  if(datos['consulta'] == 1)
  {
    w_alert({ titulo: 'Foto Cargada', tipo: 'success' });
    click_item('perfil');
  }
  else
   w_alert({ titulo: datos['consulta'], tipo: 'danger' });

 cerrar_loader();
}

function error_carga(event)
{
 w_alert({ titulo: 'Error al cargar la foto', tipo: 'danger' });
 cerrar_loader();
}

function carga_abortada(event)
{
 w_alert({ titulo: 'La carga fue abortada', tipo: 'danger' });
 cerrar_loader();
}


</script>

<?php 
}
?>