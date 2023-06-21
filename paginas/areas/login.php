<?php 
session_set_cookie_params(7*24*60*60);
session_start();
if(isset($_SESSION['usuario_restaurante']))
  header("Location:cocina.php");
else
{
  ?>
  <!DOCTYPE html>
  <html lang="es" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login | Restaurante | W-POS | WitSoft</title>

    <link rel="icon" href="../../recursos/WitSoft.ico" type="image/x-icon">

    <link rel="manifest" href="../../assets/img/favicons/manifest.json">
    <meta name="msapplication-TileImage" content="assets/img/favicons/mstile-150x150.png">
    <meta name="theme-color" content="#ffffff">
    <script src="../../assets/js/config.js"></script>
    <script src="../../vendors/overlayscrollbars/OverlayScrollbars.min.js"></script>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
    <link href="../../vendors/overlayscrollbars/OverlayScrollbars.min.css" rel="stylesheet">
    <link href="../../assets/css/theme.min.css" rel="stylesheet" id="style-default">
    <link href="../../assets/css/user.min.css" rel="stylesheet" id="user-style-default">
    <link href="../../assets/css/propios.css" rel="stylesheet" id="user-style-default">
  </head>

  <body>
    <main class="main" id="top">
      <div id="w_alert" class="w_alert"></div>
      <div id="div_loader" class="div_loader">
        <div class="loader"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      </div>
      <div class="container login-page" data-layout="container">
        <div class="row flex-center min-vh-100 py-6">
          <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 col-xxl-8">
            <div class="card rounded-5">
              <div class="card-header p-4 pb-0 pt-3">
                <div class="row">
                  <div class="col-6 d-flex align-items-center mb-2">
                    <img class="d-table mx-auto mb-3 w-100" src="../../recursos/w-pos-logo.svg" alt="W-POS - WitSoft">
                  </div>
                  <div class="col-6 d-flex align-items-center mb-2 text-center">
                    <img class="me-2" src="../../recursos/logo_restaurante.png" alt=""  style="width: 120px;"/>
                  </div>
                </div>
              </div>
              <div class="card-body p-3 pt-1">
                <hr>
                <h5 class="mb-3 f-w-400 text-center">Accede a tu cuenta</h5>

                <form id="form_login" class="text-center">
                  <input type="text" name="input_cedula" id="input_cedula" class="form-control form-control-sm round-form" placeholder="Cédula" autofocus="">
                  <br>
                  <input type="password" id="input_contraseña" name="input_contraseña" class="form-control form-control-sm round-form" placeholder="Contraseña">
                  <br>
                  <button class="btn btn-primary btn-sm btn-round" type="button" id="btn_login"> Iniciar Sesión</button>
                </form>
                <div class="position-relative mt-4">
                  <hr class="bg-300" />
                  <div class="divider-content-center">
                    <a href="https://witsoft.co" target="_blank"><strong>WIT</strong>SOFT</a> - Desarrollo de Software
                  </div>
                </div>

                
              </div>
              <div class="position-relative m-4 mt-0">
                <hr class="bg-200" />
                <div class="divider-content-center">
                 <div class="theme-control-toggle fa-icon-wait px-5">
                  <input class="form-check-input ms-0 theme-control-toggle-input" id="themeControlToggle" type="checkbox" data-theme-control="theme" value="dark" />
                  <label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Cambiar a tema claro"><span class="fas fa-sun fs-0"></span></label>
                  <label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Cambiar a tema oscuro"><span class="fas fa-moon fs-0"></span></label>
                </div>
              </div>
            </div>
            <div class="position-relative">
              <div class="text-center">

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="../../vendors/jquery-3.6.0.js"></script>
  <script src="../../vendors/popper/popper.min.js"></script>
  <script src="../../vendors/bootstrap/bootstrap.min.js"></script>
  <script src="../../vendors/anchorjs/anchor.min.js"></script>
  <script src="../../vendors/is/is.min.js"></script>
  <script src="../../vendors/fontawesome/all.min.js"></script>
  <script src="../../vendors/lodash/lodash.min.js"></script>
  <script src="../../https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
  <script src="../../vendors/list.js/list.min.js"></script>
  <script src="../../assets/js/theme.js"></script>

  <script src="../../vendors/w_alert.js"></script>

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
      url:"../../procesos/login.php",
      success:function(r)
      {
        datos=jQuery.parseJSON(r);
        if (datos['consulta'] == 1)
        {
          w_alert({ titulo: 'Sesión Iniciada - BIENVENIDO: '+datos['nombre'], tipo: 'success' });
          setTimeout('window.location="cocina.php"',1000);
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