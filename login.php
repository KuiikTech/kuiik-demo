<?php 
session_set_cookie_params(7*24*60*60);
session_start();
if(isset($_SESSION['usuario_restaurante']))
  header("Location:index.php");
else
{
  ?>
  <!DOCTYPE html>
  <html lang="es" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login | Kuiik | Software a la medida</title>

    <link rel="icon" href="recursos/favicon.ico" type="image/x-icon">

    <meta name="theme-color" content="#ffffff">
    <script src="assets/js/config.js"></script>
    <script src="vendors/overlayscrollbars/OverlayScrollbars.min.js"></script>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
    <link href="vendors/overlayscrollbars/OverlayScrollbars.min.css" rel="stylesheet">
    <link href="assets/css/theme.min.css" rel="stylesheet" id="style-default">
    <link href="assets/css/user.min.css" rel="stylesheet" id="user-style-default">
    <link href="assets/css/propios.css" rel="stylesheet" id="user-style-default">
  </head>

  <body>
    <main class="main" id="top">
      <div id="w_alert" class="w_alert"></div>
      <div id="div_loader" class="div_loader">
        <div class="loader"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      </div>
      <div class="container" data-layout="container">
        <div class="d-flex justify-content-center align-items-center min-vh-100">
          <div class="card rounded-3 py-4 px-3">
            <div class="card-body p-3 pt-1">
              <h4 class="mb-4 f-w-700 text-center">Acceso</h4>

              <form id="form_login" class="text-center">
                <input type="text" name="input_cedula" id="input_cedula" class="form-control form-control-sm round-form py-2" placeholder="Digita tu cédula" autofocus="">
                <br>
                <input type="password" id="input_contraseña" name="input_contraseña" class="form-control form-control-sm round-form py-2" placeholder="Digita tu contraseña">
                <br>
                <button class="btn btn-primary btn-sm btn-round py-2 px-4 text-uppercase mt-3" type="button" id="btn_login"> Iniciar Sesión</button>
              </form>
              <div class="position-relative mt-4 text-center">
                <strong style="color: #FE6257;">Kuiik</strong> es el software ideal para gestionar tu negocio
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="vendors/jquery-3.6.0.js"></script>
  <script src="vendors/popper/popper.min.js"></script>
  <script src="vendors/bootstrap/bootstrap.min.js"></script>
  <script src="vendors/anchorjs/anchor.min.js"></script>
  <script src="vendors/is/is.min.js"></script>
  <script src="vendors/fontawesome/all.min.js"></script>
  <script src="vendors/lodash/lodash.min.js"></script>
  <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
  <script src="vendors/list.js/list.min.js"></script>
  <script src="assets/js/theme.js"></script>

  <script src="vendors/w_alert.js"></script>

</body>

</html>

<script type="text/javascript">

  $('#input_contraseña').keypress(function(e){
    if(e.keyCode==13)
      $('#btn_login').click();
  });

  $('#input_cedula').keypress(function(e){
    if(e.keyCode==13)
      $('#btn_login').click();
  });

  $('#btn_login').click(function()
  {
    document.getElementById('div_loader').style.display = 'block';
    datos=$('#form_login').serialize();
    $.ajax({
      type:"POST",
      data:datos,
      url:"procesos/login.php",
      success:function(r)
      {
        datos=jQuery.parseJSON(r);
        if (datos['consulta'] == 1)
        {
          w_alert({ titulo: 'Hola '+datos['nombre']+', Bienvenid@ a Kuiik', tipo: 'success' });
          setTimeout('window.location="index.php"',1000);
        }
        else
          w_alert({ titulo: datos['consulta'], tipo: 'danger' });
      }
    });
    document.getElementById('div_loader').style.display = 'none';
  });

</script>

<?php 
}
?>