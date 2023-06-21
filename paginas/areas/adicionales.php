<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');

require_once "../../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();

$num_tabla = 1;

session_start();

if (isset($_SESSION['usuario_restaurante']))
{
  $usuario = $_SESSION['usuario_restaurante'];

  $sql_e = "SELECT nombre, rol, foto, area FROM usuarios WHERE codigo = '$usuario'";
  $result_e=mysqli_query($conexion,$sql_e);
  $ver_e=mysqli_fetch_row($result_e);

  $nombre = ' ' .  $ver_e[0];
  $rol = $ver_e[1];
  $url_avatar = $ver_e[2];
  $area = $ver_e[3];

  if ($url_avatar == '')
    $url_avatar = 'user.svg';
  else
    $url_avatar = $ver_e[2];

  if($area == 'Adicionales')
    header("Location:adicionales.php");
  if($area == 'Bar')
    header("Location:bar.php");
  if($area == 'Horno')
    header("Location:horno.php");
  if($area == 'Caja' || $area == 'Meseros')
    header("Location:../../index.php");

  if($area == 'Bloqueado')
    header("Location:../../procesos/cerrar_sesion.php");

  ?>
  <!DOCTYPE html>
  <html lang="es">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Adicionales | EL RANCHO DEL TIO | WitSoft</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="../../vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="../../vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="../../vendors/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="../../vendors/chartist/chartist.min.css">
    <!-- End plugin css for this page -->
    <link rel="stylesheet" href="../../vendors/datatable/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../../vendors/sweetalert/sweetalert.css">
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/propios.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="../../recursos/WitSoft.ico" />
  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:partials/_navbar.html -->
      <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="navbar-brand-wrapper d-flex align-items-center">
          <a class="navbar-brand brand-logo" href="../../index.html">
            <img src="../../recursos/logo_witsoft.png" alt="logo" class="logo-dark" />
          </a>
          <a class="navbar-brand brand-logo-mini" href="../../index.php"><img src="../../recursos/logo_witsoft_mini.png" alt="logo" /></a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-center flex-grow-1">
          <div class="navbar-nav-right ml-auto">
            <div class="logo_rectangular align-items-center" href="../../perfil.php">
              <img src="../../recursos/empresa/logo_centro.png" alt="logo" />
            </div>
          </div>
          <ul class="navbar-nav navbar-nav-right ml-auto">
            <li class="nav-item"><a href="../../procesos/cerrar_sesion.php" class="nav-link"><i class="icon-logout mr-2"></i>Cerrar Sesión</a></li>
          </ul>
        </div>
      </nav>

      <div class="container-fluid page-body-wrapper">

        <div class="main-panel" style="width: 100%">
          <div class="content_wrapper">

            <div class="alto_total" id="div_contenido"></div>

          </div>

          <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              <span class="text-muted text-center text-sm-left d-block d-sm-inline-block"></span>
              <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center"><a href="../../https://www.facebook.com/WitSoft2016/" target="_blank">WitSoft</a> - Desarrollo de Software</span>
            </div>
          </footer>
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <?php require_once "js.php";?>
  </body>
  </html>

  <script type="text/javascript">

    $('#div_contenido').load('tabla_pedidos.php/?area=Adicionales');

    $('input.moneda').keyup(function(event)
    {
      if(event.which >= 37 && event.which <= 40)
      {
        event.preventDefault();
      }

      $(this).val(function(index, value) {
        return value
        .replace(/\D/g, "")
        .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".")
        ;
      });
    });

    setInterval("cambios('Adicionales')",10000);

    function cambios(area)
    {
      $.ajax({
        type:"POST",
        data:"area="+area,
        url:"../../procesos/cambios.php",
        success:function(r)
        {
          datos=jQuery.parseJSON(r);
          if (datos['consulta'] == 1)
            $('#div_contenido').load('tabla_pedidos.php/?area='+area);
          if (datos['consulta'] == 2)
            location.reload(true);
        }
      });
    }

  </script>

  <?php 
}
else
  header("Location:../login.php");
?>