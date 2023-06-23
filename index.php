<?php
session_set_cookie_params(7 * 24 * 60 * 60);
session_start();

date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');

require_once "clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$num_tabla = 1;
header('Access-Control-Allow-Origin: *');

if (isset($_SESSION['usuario_restaurante'])) {
  $usuario = $_SESSION['usuario_restaurante'];

  $sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
  $result_e = mysqli_query($conexion, $sql_e);
  $ver_e = mysqli_fetch_row($result_e);

  $cedula = $ver_e[1];

  $nombre_usuario = $ver_e[2] . ' ' . $ver_e[3];
  $rol = $ver_e[5];

  if ($ver_e[7] == '')
    $url_avatar = 'user.svg';
  else
    $url_avatar = $ver_e[7];

  $caja = 1;
?>
  <!DOCTYPE html>
  <html lang="es">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Kuiik</title>

    <link rel="icon" href="recursos/favicon.ico" type="image/x-icon">

    <script src="assets/js/config.js"></script>
    <script src="vendors/overlayscrollbars/OverlayScrollbars.min.js"></script>

    <link rel="stylesheet" type="text/css" href="vendors/datatables/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="vendors/datatables/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="vendors/DataTables-1.10.25/datatables.css">

    <link rel="stylesheet" type="text/css" href="vendors/select2/select2.min.css">
    <link rel="stylesheet" type="text/css" href="vendors/pattern-lock/patternlock.css">
    <link rel="stylesheet" type="text/css" href="vendors/swiper/swiper-bundle.min.css">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
    <link href="vendors/overlayscrollbars/OverlayScrollbars.min.css" rel="stylesheet">
    <link href="assets/css/theme.css" rel="stylesheet" id="style-default">
    <link href="assets/css/user.css" rel="stylesheet" id="user-style-default">
    <link href="assets/css/propios.css" rel="stylesheet" id="user-style-default">
  </head>

  <body>
    <div id="w_alert" class="w_alert"></div>
    <div id="div_loader" class="div_loader">
      <div class="loader">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
      </div>
    </div>
    <main class="main" id="top">
      <div class="container-fluid" data-layout="container">
        <nav class="navbar navbar-light navbar-vertical navbar-expand-xl navbar-inverted">
          <div class="d-flex align-items-center">
            <div class="toggle-icon-wrapper">

              <button class="btn navbar-toggler-humburger-icon navbar-vertical-toggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Ocultar/Mostrar Menú"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>

            </div>
            <a class="navbar-brand" href="index.php">
              <div class="d-flex align-items-center py-3">
                <img class="me-2" src="recursos/kuiik.svg" alt="" height="40" />
              </div>
            </a>
          </div>
          <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
            <div class="navbar-vertical-content scrollbar">
              <ul class="navbar-nav flex-column mb-3" id="navbarVerticalNav">
                <li class="nav-item">
                  <!-- label-->
                  <div class="row navbar-vertical-label-wrapper my-1">
                    <div class="col-auto navbar-vertical-label">Menú
                    </div>
                    <div class="col ps-0">
                      <hr class="mb-0 navbar-vertical-divider" />
                    </div>
                  </div>


                  <a class="nav-link" href="javascript:click_item('pdv')" role="button" aria-expanded="false" id="a_pdv">
                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-shopping-cart"></span></span><span class="nav-link-text ps-1">Punto de venta</span>
                    </div>
                  </a>

                  <a class="nav-link" href="javascript:click_item('reservas')" role="button" aria-expanded="false" id="a_reservas">
                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-bell"></span></span><span class="nav-link-text ps-1">Reservas</span>
                      <span class="badge rounded-pill bg-danger text-white mx-1" id="badge_PENDIENTE"></span>
                    </div>
                  </a>

                  <a class="nav-link" href="javascript:click_item('clientes')" role="button" aria-expanded="false" id="a_clientes">
                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-users"></span></span><span class="nav-link-text ps-1">Clientes</span>
                    </div>
                  </a>

                  <a class="nav-link" href="javascript:click_item('productos')" role="button" aria-expanded="false" id="a_productos">
                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-boxes"></span></span><span class="nav-link-text ps-1">Productos</span>
                    </div>
                  </a>

                  <a class="nav-link dropdown-indicator" href="#transacciones" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="transacciones" id="a_transacciones">
                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-hand-holding-usd"></span></span><span class="nav-link-text ps-1">Transacciones</span>
                    </div>
                  </a>
                  <ul class="nav collapse false" id="transacciones">

                    <li class="nav-item">
                      <a class="nav-link" href="javascript:click_item('caja')" aria-expanded="false" id="a_caja">
                        <div class="d-flex align-items-center">
                          <span class="nav-link-text ps-1">Caja</span>
                        </div>
                      </a>
                    </li>

                    <li class="nav-item">
                      <a class="nav-link" href="javascript:click_item('ventas')" aria-expanded="false" id="a_ventas">
                        <div class="d-flex align-items-center">
                          <span class="nav-link-text ps-1">Ventas</span>
                        </div>
                      </a>
                    </li>

                    <li class="nav-item">
                      <a class="nav-link" href="javascript:click_item('facturas')" aria-expanded="false" id="a_facturas">
                        <div class="d-flex align-items-center">
                          <span class="nav-link-text ps-1">Facturas</span>
                        </div>
                      </a>
                    </li>

                    <li class="nav-item">
                      <a class="nav-link" href="javascript:click_item('compras')" aria-expanded="false" id="a_compras">
                        <div class="d-flex align-items-center">
                          <span class="nav-link-text ps-1">Compras</span>
                        </div>
                      </a>
                    </li>

                    <li class="nav-item">
                      <a class="nav-link" href="javascript:click_item('por_cobrar')" aria-expanded="false" id="a_por_cobrar">
                        <div class="d-flex align-items-center">
                          <span class="nav-link-text ps-1">Cuentas por cobrar</span>
                        </div>
                      </a>
                    </li>

                    <li class="nav-item">
                      <a class="nav-link" href="javascript:click_item('caja_mayor')" aria-expanded="false" id="a_caja_mayor">
                        <div class="d-flex align-items-center">
                          <span class="nav-link-text ps-1">Caja Mayor</span>
                        </div>
                      </a>
                    </li>

                  </ul>

                  <a class="nav-link dropdown-indicator" href="#configuraciones" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="configuraciones" id="a_configuraciones">
                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-cogs"></span></span><span class="nav-link-text ps-1">Configuraciones</span>
                    </div>
                  </a>
                  <ul class="nav collapse false" id="configuraciones">

                    <li class="nav-item">
                      <a class="nav-link" href="javascript:click_item('usuarios')" aria-expanded="false" id="a_usuarios">
                        <div class="d-flex align-items-center">
                          <span class="nav-link-text ps-1">Usuarios</span>
                        </div>
                      </a>
                    </li>

                    <li class="nav-item">
                      <a class="nav-link" href="javascript:click_item('config_pdv')" aria-expanded="false" id="a_config_pdv">
                        <div class="d-flex align-items-center">
                          <span class="nav-link-text ps-1">Config PDV</span>
                        </div>
                      </a>
                    </li>

                    <li class="nav-item">
                      <a class="nav-link" href="javascript:click_item('clientes_especiales')" aria-expanded="false" id="a_clientes_especiales">
                        <div class="d-flex align-items-center">
                          <span class="nav-link-text ps-1">Clientes Especiales</span>
                        </div>
                      </a>
                    </li>

                    <li class="nav-item">
                      <a class="nav-link" href="javascript:click_item('proveedores')" aria-expanded="false" id="a_proveedores">
                        <div class="d-flex align-items-center">
                          <span class="nav-link-text ps-1">Proveedores</span>
                        </div>
                      </a>
                    </li>

                  </ul>

                  <a class="nav-link" href="javascript:click_item('perfil')" role="button" aria-expanded="false" id="a_perfil">
                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-boxes"></span></span><span class="nav-link-text ps-1">Perfil</span>
                    </div>
                  </a>
                  <a hidden class="nav-link" href="javascript:click_item('bodega')" role="button" aria-expanded="false" id="a_bodega">
                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-warehouse"></span></span><span class="nav-link-text ps-1">Bodega</span>
                    </div>
                  </a>
                  <a hidden class="nav-link" href="javascript:click_item('informes')" role="button" aria-expanded="false" id="a_informes">
                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-clipboard"></span></span><span class="nav-link-text ps-1">Informes</span>
                    </div>
                  </a>

                </li>
              </ul>
              <hr class="mb-0 navbar-vertical-divider bg-white" hidden />
              <div class="row navbar-vertical-label-wrapper my-1 mx-0" hidden>
                <button class="btn btn-sm btn-outline-info btn-round" onclick="abrir_cajon()"><span class="fas fa-dollar-sign"></span> Abrir cajón</button>
              </div>

              <div class="list-user-sales" id="ranking_sales"></div>
            </div>

          </div>
        </nav>
        <div class="content">
          <nav class="navbar navbar-light navbar-glass navbar-top navbar-expand">

            <button class="btn navbar-toggler-humburger-icon navbar-toggler me-1 me-sm-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation" id="btn_close_nav"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
            <a class="navbar-brand me-1 me-sm-3" href="index.php">
              <div class="d-flex align-items-center">
                <img class="me-2" src="recursos/kuiik.svg" alt="Restaurante" height="30" />
              </div>
            </a>
            <ul class="navbar-nav navbar-nav-icons ms-auto flex-row align-items-center">
              <li class="dropdown pc-h-item d-none d-lg-none">
                <div class="px-3" id="div_busqueda_nombre">
                  <div class="form-group mb-0 d-flex align-items-center">
                    <i data-feather="search"></i>
                    <input type="text" class="form-control border-0 shadow-none" placeholder="Cédula/NIT - Nombre/Daños" onkeydown="if(event.key=== 'Enter'){cargar_servicios_cc_2(this.value)}" id="input_cc_cliente" autocomplete="off">
                  </div>
                </div>
              </li>
              <li class="dropdown pc-h-item d-none d-lg-none">
                <div class="px-3">
                  <div class="form-group mb-0 d-flex align-items-center">
                    <i data-feather="search"></i>
                    <input type="number" class="form-control border-0 shadow-none" placeholder="Código de servicio" onkeydown="if(event.key=== 'Enter'){buscar_servicio(this.value)}" id="input_cod_servicio_b">
                  </div>
                </div>
              </li>

              <li class="dropdown pc-h-item d-none d-lg-block">
                <div class="px-3">
                  <a href="javascript:click_item('pedidos_cocina')" class="btn btn-sm btn-outline-dark btn-round" title="Mostrar pedidos Cocina">
                    <span class="fas fa-utensils fs-0"></span>
                  </a>
                </div>
              </li>

              <li class="nav-item dropdown">
                <div id="div_notificaciones"></div>
              </li>

              <li class="nav-item">
                <div class="theme-control-toggle fa-icon-wait px-2">
                  <input class="form-check-input ms-0 theme-control-toggle-input" id="themeControlToggle" type="checkbox" data-theme-control="theme" value="dark" />
                  <label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Cambiar a tema claro"><span class="fas fa-sun fs-0"></span></label>
                  <label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Cambiar a tema oscuro"><span class="fas fa-moon fs-0"></span></label>
                </div>
              </li>
              <li class="nav-item dropdown"><a class="nav-link pe-0" id="navbarDropdownUser" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <div class="d-flex align-items-center">
                    <div class="flex-1 me-2 d-none d-md-block">
                      <h6 class="mb-0 title"><?php echo $nombre_usuario ?></h6>
                      <p class="fs--2 mb-0 d-flex"><?php echo $rol ?></p>
                    </div>
                    <div class="avatar avatar-xl status-online">
                      <img class="rounded-circle" src="recursos/user/<?php echo $url_avatar ?>" alt="" />
                    </div>
                  </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end py-0" aria-labelledby="navbarDropdownUser">
                  <div class="bg-white dark__bg-1000 rounded-2 py-2">
                    <a class="dropdown-item" href="javascript:click_item('perfil')">Perfil</a>
                    <a class="dropdown-item" href="javascript:click_item('connfiguracion')">Configuración</a>
                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item" href="procesos/cerrar_sesion.php">Salir</a>
                  </div>
                </div>
              </li>
            </ul>
          </nav>

          <div id="div_login" class="login_reload" style="z-index: 2000 !important;">
            <div class="row flex-center min-vh-100 py-6" style="opacity: 100% !important;">
              <div class="col-sm-3 col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                <div class="card rounded-5">
                  <div class="card-header p-4 pb-0 pt-3">
                    <div class="row">
                      <div class="col d-flex align-items-center mb-2">
                        <img class="d-table mx-auto mb-3 w-100" src="recursos/w-pos-logo.svg" alt="W-POS - Kuiik">
                      </div>
                      <div class="col d-flex align-items-center mb-2">
                        <img class="me-2" src="recursos/logo_restaurante.png" alt="" width="100%" />
                      </div>
                    </div>
                  </div>
                  <div class="card-body p-3 pt-1">
                    <hr>
                    <h6 class="mb-3 f-w-400 text-center">Accede a tu cuenta</h6>
                    <h5 class="mb-1 f-w-400 text-center"><?php echo $nombre_usuario ?></h5>
                    <form id="form_login" class="text-center">
                      <input type="text" name="input_cedula" id="input_cedula" class="form-control form-control-sm round-form" placeholder="Cédula" hidden value="<?php echo $cedula ?>">
                      <input type="password" id="input_contraseña" name="input_contraseña" class="form-control form-control-sm round-form" placeholder="Contraseña">
                      <br>
                      <button class="btn btn-primary btn-sm btn-round" type="button" id="btn_login"> Iniciar Sesión</button>
                    </form>
                    <div class="position-relative mt-4">
                      <hr class="bg-300" />
                      <div class="divider-content-center">
                        <a href="https://Kuiik.co" target="_blank"><strong>WIT</strong>SOFT</a> - Desarrollo de Software
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

          <div class="row g-3 mb-3">
            <div class="text-dark p-0" id="div_contenido"></div>

            <!-- Modal Carrito-->
            <div class="modal fade" id="Modal_Carrito" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content p-2" id="div_modal_carrito">
                </div>
              </div>
            </div>

            <!-- Modal detalles de factura-->
            <div class="modal fade" id="Modal_ticket" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content sombra_modal" id="contenedor_pdf"></div>
              </div>
            </div>

            <!-- Modal detalles de factura-->
            <div class="modal fade" id="Modal_ticket_s" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content sombra_modal" id="contenedor_pdf_s"></div>
              </div>
            </div>

            <!-- Modal detalles de servicio-->
            <div class="modal fade" id="Modal_ver_servicio" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content p-2" id="div_modal_servicio"></div>
              </div>
            </div>

            <!-- Modal ver PDF-->
            <div class="modal fade" id="Modal_PDF" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content sombra_modal" id="contenedor_pdf_2"></div>
              </div>
            </div>

            <!-- Modal Ver Producto-->
            <div class="modal fade" id="Modal_Ver_Producto" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-md" role="document">
                <div class="modal-content" id="div_modal_producto_2">
                </div>
              </div>
            </div>

            <div id="contenedor_pdf_cajon" hidden></div>

            <!-- Modal confirmacion recarga-->
            <div class="modal fade" id="Modal_confirmacion_recarga" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow-y: scroll;">
              <div class="row">
                <div class="modal-dialog modal-sm" role="document">
                  <div class="modal-content shadow-lg">
                    <div class="modal-header text-center p-2 bg-danger">
                      <h5 class="modal-title text-white">Está seguro de procesar la recarga?</h5>
                    </div>
                    <div class="modal-body p-2">
                      <div class="row m-0">
                        <div id="valor_recarga" class="text-center"></div>
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-round col m-1" onclick="$('#Modal_confirmacion_recarga').modal('toggle');" id="btn_close_confirm_recarga">NO</button>
                        <button type="button" class="btn btn-sm btn-outline-primary btn-round col m-1" id="btn_procesar_recarga_confirm">SI</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- <footer class="footer">
              <div class="row g-0 justify-content-between fs--1 mt-4 mb-3">
                <div class="col-12 col-sm-auto text-center">
                  <p class="mb-0 text-600">W-pos <span class="d-none d-sm-inline-block">| </span><br class="d-sm-none" /> 2022 &copy; Wit<b>Soft</b></p>
                </div>
                <div class="col-12 col-sm-auto text-center">
                  <p class="mb-0 text-600">v2.0.1</p>
                </div>
              </div>
            </footer> -->
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

    <script src="vendors/datatables/js/jquery.dataTables.min.js"></script>
    <script src="vendors/datatables/js/dataTables.bootstrap4.min.js"></script>
    <script src="vendors/datatables/js/dataTables.responsive.min.js"></script>
    <script src="vendors/datatables/js/responsive.bootstrap4.min.js"></script>
    <script src="vendors/select2/select2.min.js"></script>

    <script src="vendors/swiper/swiper-bundle.min.js"></script>

    <script src="vendors/w_alert.js"></script>

    <script src="vendors/pattern-lock/patternlock.js" charset="utf-8"></script>
    <script src="vendors/qrcodejs/qrcode.js"></script>

    <script src="vendors/ConectorPlugin.js"></script>

    <script src="vendors/reloj/time_calculation.js"></script>

  </body>

  </html>

  <script type="text/javascript">
    $('input.moneda').keyup(function(event) {
      if (event.which >= 37 && event.which <= 40) {
        event.preventDefault();
      }
      $(this).val(function(index, value) {
        return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
      });
    });

    <?php
    $sql_especiales = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Ranking Productos'";
    $result_especiales = mysqli_query($conexion, $sql_especiales);
    $mostrar_especiales = mysqli_fetch_row($result_especiales);

    if ($mostrar_especiales[2] == 'SI') {
    ?>
      document.getElementById('div_loader').style.display = 'block';
      $('#ranking_sales').load('paginas/detalles/ranking_ventas.php', cerrar_loader());
    <?php
    }
    ?>

    cargar_notificaciones();
    notificacion_reservas('PENDIENTE');

    function notificacion_reservas(estado) {
      $.ajax({
        type: "POST",
        data: "reservas=" + estado,
        url: "procesos/obtener_datos.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos[estado] > 0) {
            document.getElementById("badge_" + estado).hidden = false;
            $('#badge_' + estado).html(datos[estado]);
            if (document.body.contains(document.getElementById("badge_" + estado + "_s"))) {
              document.getElementById("badge_" + estado + "_s").hidden = false;
              $('#badge_' + estado + '_s').html(datos[estado]);
            }
          } else {
            if (!!document.getElementById("#badge_" + estado + "_s"))
              document.getElementById("badge_" + estado + "_s").hidden = true;
            document.getElementById("badge_" + estado).hidden = true;
          }
        }
      });
    }

    $('#input_recarga').keypress(function(e) {
      if (e.keyCode == 13)
        $('#btn_procesar_recarga').click();
    });

    $('#btn_procesar_recarga_confirm').click(function() {
      document.getElementById('div_loader').style.display = 'block';
      input_recarga = document.getElementById("input_recarga").value;
      input_metodo_pago = document.getElementById("input_metodo_pago_r").value;
      if (input_recarga != '' && input_metodo_pago != '') {
        $.ajax({
          type: "POST",
          data: 'input_recarga=' + input_recarga + '&input_metodo_pago=' + input_metodo_pago,
          url: "procesos/agregar_recarga.php",
          success: function(r) {
            datos = jQuery.parseJSON(r);
            if (datos['consulta'] == 1) {
              w_alert({
                titulo: 'Recarga agregada',
                tipo: 'success'
              });
              document.getElementById("input_recarga").value = '';
              document.getElementById("input_metodo_pago_r").value = '';
              $('#btn_close_confirm_recarga').click();
              abrir_cajon();
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
      } else {
        if (input_recarga == '') {
          w_alert({
            titulo: 'Ingrese el valor de la recarga',
            tipo: 'danger'
          });
          document.getElementById('input_recarga').focus();
        } else if (input_metodo_pago == '') {
          w_alert({
            titulo: 'Seleccione el metodo de pago',
            tipo: 'danger'
          });
          document.getElementById('input_metodo_pago_r').focus();
        }
      }
      cerrar_loader();
    });

    function cerrar_loader() {
      document.getElementById('div_loader').style.display = 'none';
    }

    function cargar_notificaciones() {
      document.getElementById('div_loader').style.display = 'block';
      $('#div_notificaciones').load('paginas/detalles/notificaciones.php', function() {
        cerrar_loader();
      });
    }

    click_item('pdv');

    function click_item(id) {
      document.getElementById('div_busqueda_nombre').hidden = false;
      document.getElementById('div_loader').style.display = 'block';
      $('#div_contenido').load('paginas/' + id + '.php', function() {
        cerrar_loader();
      });
      $('#navbarVerticalCollapse').removeClass("show");
      cargar_notificaciones();
    }

    window.onload = function() {
      document.onkeyup = mostrarInformacionTecla;
    }

    function mostrarInformacionTecla(evObject) {
      var msg = '';
      var teclaPulsada = evObject.keyCode;

      if (teclaPulsada == 69) {
        if (!!document.getElementById("btn_abrir_camara") && !$('body').hasClass('modal-open'))
          $("#btn_abrir_camara").click();
      }
    }

    function buscar_servicio(cod_servicio) {
      document.getElementById("input_cod_servicio_b").value = '';
      document.getElementById('div_loader').style.display = 'block';
      $.ajax({
        type: "POST",
        data: "cod_servicio=" + cod_servicio,
        url: "procesos/buscar_servicio.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            $('#Modal_ver_servicio').modal('show');
            document.getElementById('div_loader').style.display = 'block';
            $('#div_modal_servicio').load('paginas/detalles/detalles_servicio.php/?cod_servicio=' + cod_servicio, function() {
              cerrar_loader();
            });
          } else
            w_alert({
              titulo: datos['consulta'],
              tipo: 'danger'
            });

          cerrar_loader();
        }
      });
    }

    $('#input_contraseña').keypress(function(e) {
      if (e.keyCode == 13)
        $('#btn_login').click();
    });

    $('#input_cedula').keypress(function(e) {
      if (e.keyCode == 13)
        $('#btn_login').click();
    });

    $('#btn_login').click(function() {
      document.getElementById('div_loader').style.display = 'block';
      datos = $('#form_login').serialize();
      $.ajax({
        type: "POST",
        data: datos,
        url: "procesos/login.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            w_alert({
              titulo: 'Sesión Iniciada - BIENVENIDO: ' + datos['nombre'],
              tipo: 'success'
            });
            document.getElementById('input_contraseña').value = '';
            document.getElementById('div_login').style.display = 'none';
            document.getElementById('pc_container').style.display = 'block';
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
      document.getElementById('div_loader').style.display = 'none';
    });

    function imprimir_ticket_venta(cod_venta) {
      document.getElementById('div_loader').style.display = 'block';
      $.ajax({
        type: "POST",
        data: "cod_venta=" + cod_venta,
        url: "procesos/generar_venta_pdf.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            ruta_pdf = datos['ruta_pdf'];
            //$("#Modal_ticket").modal('show');
            $('#contenedor_pdf').load('paginas/detalles/ver_ticket_pdf.php/?ruta=' + ruta_pdf + '&imprimir=1', function() {
              cerrar_loader();
            });
          } else {
            w_alert({
              titulo: datos['consulta'],
              tipo: 'danger'
            });
            if (datos['consulta'] == 'Reload') {
              document.getElementById('div_login').style.display = 'block';
              cerrar_loader();

            }

          }
          cerrar_loader();
        }
      });
    }

    function generar_PDF_factura(cod_factura) {
      //document.getElementById('div_loader').style.display = 'block';
      $.ajax({
        type: "POST",
        data: "cod_factura=" + cod_factura,
        url: "procesos/generar_PDF_factura.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            ruta_pdf = datos['ruta_pdf'];
            w_alert({
              titulo: 'PDF Generado Correctamente',
              tipo: 'success'
            });
            imprimir_PDF(ruta_pdf);
          } else {
            w_alert({
              titulo: datos['consulta'],
              tipo: 'danger'
            });
            cerrar_loader()
          }
        }
      });
    }

    function imprimir_PDF(ruta) {
      document.getElementById('div_loader').style.display = 'block';

      ruta_pdf = datos['ruta_pdf'];
      $("#Modal_PDF").modal('show');
      $('#contenedor_pdf_2').load('paginas/detalles/ver_pdf.php/?ruta=' + ruta_pdf, function() {
        cerrar_loader();
      });
    }

    function imprimir_comanda(datos) {
      var datos = JSON.stringify(datos);
      $.ajax({
        type: "POST",
        datatype: 'application/json',
        data: "datos=" + datos,
        url: "http://localhost/printer_thermal/print.php",
        success: function(r) {

        }
      });
    }

    function abrir_cajon() {
      $.ajax({
        type: "POST",
        datatype: 'application/json',
        url: "http://localhost/printer_thermal/open_cashdrawer.php",
        success: function(r) {

        }
      });
    }

    function enviar_whatsapp(cod_cliente, tipo, mensaje = '') {
      document.getElementById('div_loader').style.display = 'block';
      $.ajax({
        type: "POST",
        data: "cod_cliente=" + cod_cliente + "&tipo=" + tipo + "&mensaje=" + mensaje,
        url: "procesos/enviar_whatsapp.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          console.log(datos);
          if (datos['consulta'] == 1) {
            w_alert({
              titulo: 'Mensaje enviado',
              tipo: 'success'
            });
          } else {
            w_alert({
              titulo: datos['consulta'],
              tipo: 'danger'
            });
          }
          cerrar_loader();
        }
      });
    }

    function loadTooltip() {
      const tooltipTriggerList = document.querySelectorAll(
        '[data-bs-toggle="tooltip"]',
      );
      const tooltipList = [...tooltipTriggerList].map(
        (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl),
      );
    }

    function mostrar_producto(cod_producto) {
      $("#Modal_Ver_Producto").modal("show");
      document.getElementById('div_loader').style.display = 'block';
      $('#div_modal_producto_2').load('paginas/detalles/detalles_producto.php/?cod_producto=' + cod_producto +'&bodega=0', function() {
        cerrar_loader();
      });
    }
  </script>

<?php
} else {
  header("Location:login.php");
}
?>