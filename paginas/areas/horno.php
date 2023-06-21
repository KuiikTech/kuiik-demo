<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');

require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$num_tabla = 1;

session_start();

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

  if ($rol != 'Administrador') {
    if ($rol == 'Bar')
      header("Location:bar.php");
    if ($rol == 'Cocina')
      header("Location:cocina.php");
    if ($rol == 'Caja' || $rol == 'Meseros')
      header("Location:../../index.php");
  }
  if ($rol == 'Bloqueado')
    header("Location:../../procesos/cerrar_sesion.php");


?>
  <!DOCTYPE html>
  <html lang="es">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Restaurante | W-POS | WitSoft</title>

    <link rel="icon" href="../../recursos/WitSoft.ico" type="image/x-icon">

    <script src="../../assets/js/config.js"></script>
    <script src="../../vendors/overlayscrollbars/OverlayScrollbars.min.js"></script>

    <link rel="stylesheet" type="text/css" href="../../vendors/datatables/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="../../vendors/datatables/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="../../vendors/DataTables-1.10.25/datatables.css">

    <link rel="stylesheet" type="text/css" href="../../vendors/select2/select2.min.css">
    <link rel="stylesheet" type="text/css" href="../../vendors/pattern-lock/patternlock.css">
    <link rel="stylesheet" type="text/css" href="../../vendors/swiper/swiper-bundle.min.css">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
    <link href="../../vendors/overlayscrollbars/OverlayScrollbars.min.css" rel="stylesheet">
    <link href="../../assets/css/theme.css" rel="stylesheet" id="style-default">
    <link href="../../assets/css/user.css" rel="stylesheet" id="user-style-default">
    <link href="../../assets/css/propios.css" rel="stylesheet" id="user-style-default">
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
        <nav class="navbar navbar-dark navbar-vertical navbar-inverted">
          <div class="d-flex align-items-center">
            <a class="navbar-brand" href="javascript:$('#div_contenido').load('cuadros_pedidos.php/?area=Horno');">
              <div class="d-flex align-items-center py-3">
                <img class="me-2" src="../../recursos/logo_restaurante_horiz.png" alt="" height="40" />
              </div>
            </a>
          </div>
        </nav>
        <div class="content" style="margin-left: 1rem !important">
          <nav class="navbar navbar-light navbar-glass navbar-top navbar-expand">
            <a class="navbar-brand me-1 me-sm-3" href="javascript:$('#div_contenido').load('cuadros_pedidos.php/?area=Horno');">
              <div class="d-flex align-items-center">
                <img class="me-2" src="../../recursos/logo_restaurante_horiz.png" alt="Restaurante" height="30" />
              </div>
            </a>
            <ul class="navbar-nav navbar-nav-icons ms-auto flex-row align-items-center">
              <li class="nav-item dropdown">
                <div id="div_notificaciones"></div>
              </li>

              <li class="nav-item dropdown px-0 fa-icon-wait">
                <a class="btn btn-outline-dark mt-2 btn-round" data-bs-toggle="collapse" href="#collapseView" role="button" aria-expanded="false" aria-controls="collapseView">
                  <span class="fas fa-eye fs-0"></span> Visualizar
                </a>
                <div class="collapse bg-white" id="collapseView">
                  <div class="border p-card rounded">
                    <div class="form-check form-switch">
                      <input class="form-check-input" id="inputCheckBar" type="checkbox" onchange="cambiarVisualizacion('Bar', this.checked)" />
                      <label class="form-check-label" for="inputCheckBar">Bar</label>
                    </div>
                    <div class="form-check form-switch">
                      <input class="form-check-input" id="inputCheckCocina" type="checkbox" onchange="cambiarVisualizacion('Cocina', this.checked)" />
                      <label class="form-check-label" for="inputCheckCocina">Cocina</label>
                    </div>
                    <div class="form-check form-switch">
                      <input class="form-check-input" id="inputCheckHorno" type="checkbox" onchange="cambiarVisualizacion('Horno', this.checked)" />
                      <label class="form-check-label" for="inputCheckHorno">Horno</label>
                    </div>
                    <div class="form-check form-switch">
                      <input class="form-check-input" id="inputCheckHorno2" type="checkbox" onchange="cambiarVisualizacion('Horno2', this.checked)" />
                      <label class="form-check-label" for="inputCheckHorno2">Horno2</label>
                    </div>
                  </div>
                </div>
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
                      <img class="rounded-circle" src="../../recursos/user/<?php echo $url_avatar ?>" alt="" />
                    </div>
                  </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end py-0" aria-labelledby="navbarDropdownUser">
                  <div class="bg-white dark__bg-1000 rounded-2 py-2">
                    <a class="dropdown-item" href="javascript:click_item('perfil')">Perfil</a>
                    <a class="dropdown-item" href="javascript:click_item('connfiguracion')">Configuración</a>
                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item" href="../../procesos/cerrar_sesion.php">Salir</a>
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
                        <img class="d-table mx-auto mb-3 w-100" src="../../recursos/w-pos-logo.svg" alt="W-POS - WitSoft">
                      </div>
                      <div class="col d-flex align-items-center mb-2">
                        <img class="me-2" src="../../recursos/logo_restaurante.png" alt="" width="100%" />
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

          <div class="row g-3 mb-3">
            <div class="row m-0 p-1">
              <div class="text-dark col-10 mt-3 p-1" id="div_contenido">
                <div class="contenedor_pedidos" id="pedidos_pendientes"></div>
                <hr>
                <div class="row text-center">
                  <div class="col">PEDIDOS ENTREGADOS</div>
                </div>
                <div class="contenedor_pedidos" id="pedidos_terminados"></div>
              </div>
              <div class="text-dark border border-danger border-3 col-2 mt-3 p-1" id="div_lateral" width="100%" height="100%">
                <div id="reservas_pendientes"></div>
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

    <script src="../../vendors/jquery-3.6.0.js"></script>
    <script src="../../vendors/popper/popper.min.js"></script>
    <script src="../../vendors/bootstrap/bootstrap.min.js"></script>
    <script src="../../vendors/anchorjs/anchor.min.js"></script>
    <script src="../../vendors/is/is.min.js"></script>
    <script src="../../vendors/echarts/echarts.min.js"></script>
    <script src="../../vendors/fontawesome/all.min.js"></script>
    <script src="../../vendors/lodash/lodash.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
    <script src="../../vendors/list.js/list.min.js"></script>
    <script src="../../assets/js/theme.js"></script>

    <script src="../../vendors/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../../vendors/datatables/js/dataTables.bootstrap4.min.js"></script>
    <script src="../../vendors/datatables/js/dataTables.responsive.min.js"></script>
    <script src="../../vendors/datatables/js/responsive.bootstrap4.min.js"></script>
    <script src="../../vendors/select2/select2.min.js"></script>

    <script src="../../vendors/swiper/swiper-bundle.min.js"></script>

    <script src="../../vendors/w_alert.js"></script>

    <script src="../../vendors/pattern-lock/patternlock.js" charset="utf-8"></script>
    <script src="../../vendors/qrcodejs/qrcode.js"></script>

    <script src="../../vendors/ConectorPlugin.js"></script>

    <script src="../../vendors/reloj/time_calculation.js"></script>

  </body>

  </html>

  <script type="text/javascript">
    function cerrar_loader() {
      document.getElementById('div_loader').style.display = 'none';
    }
    //$('#div_lateral').load('reservas.php/?area=Horno');

    $('input.moneda').keyup(function(event) {
      if (event.which >= 37 && event.which <= 40) {
        event.preventDefault();
      }

      $(this).val(function(index, value) {
        return value
          .replace(/\D/g, "")
          .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
      });
    });
    lista_pedidos('PENDIENTE');
    lista_pedidos('TERMINADO');
    lista_reservas('PENDIENTE');
    var intervalo_pedidos = setInterval("cambios('Horno')", 30000);

    function cambios(area) {
      $.ajax({
        type: "POST",
        data: "area=" + area,
        url: "../../procesos/cambios.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            lista_pedidos('PENDIENTE');
            lista_reservas('PENDIENTE');
            clearInterval(intervalo_pedidos);
            intervalo_pedidos = setInterval("cambios('Horno')", 30000);
          } else {
            if (datos['consulta'] == 'Reload') {
              document.getElementById('div_login').style.display = 'block';
              cerrar_loader();

            }
          }
        }
      });
    }


    async function crear_div(codigo, orden, padre) {
      try {
        if (document.getElementById("div_card_" + codigo)) {
          if (document.getElementById("orden_pedido_" + codigo).innerHTML != orden) {
            document.getElementById("div_card_" + codigo).remove();

            const div = document.createElement("div");
            div.id = "div_card_" + codigo;
            div.className = "card item m-1";
            div.style = "order: " + orden + ";";
            padre.appendChild(div);

            await cargar_div(codigo, orden);
          }
        } else {
          const div = document.createElement("div");
          div.id = "div_card_" + codigo;
          div.className = "card item m-1";
          div.style = "order: " + orden + ";";
          padre.appendChild(div);
          await cargar_div(codigo, orden);
        }
      } catch (error) {
        console.log(error);
      }
    }

    function cargar_div(codigo, orden) {
      $('#div_card_' + codigo).load('cuadro_pedido.php/?cod_pedido=' + codigo + '&orden=' + orden + '&area=Horno');
    }

    async function crear_div_reserva(codigo, orden, padre) {
      try {
        if (document.getElementById("div_card_reserva_" + codigo)) {
          if (document.getElementById("orden_reserva_" + codigo).innerHTML != orden) {
            document.getElementById("div_card_reserva_" + codigo).remove();

            const div = document.createElement("div");
            div.id = "div_card_reserva_" + codigo;
            div.className = "card item m-1";
            div.style = "order: " + orden + ";";
            padre.appendChild(div);

            await cargar_div_reserva(codigo, orden);
          }
        } else {
          const div = document.createElement("div");
          div.id = "div_card_reserva_" + codigo;
          div.className = "card item m-1";
          div.style = "order: " + orden + ";";
          padre.appendChild(div);
          await cargar_div_reserva(codigo, orden);
        }
      } catch (error) {
        console.log(error);
      }
    }

    function cargar_div_reserva(codigo, orden) {
      $('#div_card_reserva_' + codigo).load('cuadro_reserva.php/?cod_reserva=' + codigo + '&orden=' + orden + '&area=Horno');
    }

    function lista_pedidos(tipo) {
      $.ajax({
        type: "POST",
        data: "tipo=" + tipo + "&area=Horno",
        url: "../../procesos/obtener_pedidos.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {

            pedidos = datos['pedidos'];
            cant = datos['cant'];

            if (tipo == 'PENDIENTE')
              padre_1 = document.getElementById('pedidos_pendientes');
            else
              padre_1 = document.getElementById('pedidos_terminados');

            for (var i = 1; i < cant; i++) {
              crear_div(pedidos[i], i, padre_1);
            }
          } else {
            if (datos['consulta'] == 'Reload') {
              document.getElementById('div_login').style.display = 'block';
              cerrar_loader();

            } else
              location.reload(true);
          }
        }
      });
    }

    function lista_reservas(tipo) {
      $.ajax({
        type: "POST",
        data: "tipo=" + tipo,
        url: "../../procesos/obtener_reservas.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {

            reservas = datos['reservas'];
            cant = datos['cant'];

            if (tipo == 'PENDIENTE')
              padre_1 = document.getElementById('reservas_pendientes');
            else
              padre_1 = document.getElementById('reservas_terminadas');

            for (var i = 1; i < cant; i++) {
              crear_div_reserva(reservas[i], i, padre_1);
            }

            if (cant == 1) {
              document.getElementById('div_lateral').style.display = 'none';
              document.getElementById('div_contenido').classList.add("col-12");
              document.getElementById('div_contenido').classList.remove("col-10");
            } else {
              document.getElementById('div_lateral').style.display = 'block';
              document.getElementById('div_contenido').classList.add("col-10");
              document.getElementById('div_contenido').classList.remove("col-12");
            }
          } else {
            if (datos['consulta'] == 'Reload') {
              document.getElementById('div_login').style.display = 'block';
              cerrar_loader();

            } else
              location.reload(true);
          }
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
        url: "../../procesos/login.php",
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

    function preparando_pedido(cod_mesa, cod_pedido, num_item, area, orden) {
      document.getElementById('div_loader').style.display = 'block';
      document.getElementById("pre_" + cod_pedido + "_" + num_item).disabled = true;
      $.ajax({
        type: "POST",
        data: "cod_pedido=" + cod_pedido + "&num_item=" + num_item + "&cod_mesa=" + cod_mesa,
        url: "../../procesos/preparar_pedido.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            w_alert({
              titulo: 'Preparando pedido',
              tipo: 'success'
            });
            cargar_div(cod_pedido, orden);
            document.getElementById('div_loader').style.display = 'none';
          } else {
            w_alert({
              titulo: datos['consulta'],
              tipo: 'danger'
            });
            if (datos['consulta'] == 'Reload') {
              document.getElementById('div_login').style.display = 'block';
              cerrar_loader();

            }

            document.getElementById("pre_" + cod_pedido + "_" + num_item).disabled = false;
            document.getElementById('div_loader').style.display = 'none';
          }
        }
      });
    }

    function pedido_despachado(cod_mesa, cod_pedido, num_item, area, orden) {
      document.getElementById('div_loader').style.display = 'block';
      document.getElementById("des_" + cod_pedido + "_" + num_item).disabled = true;
      $.ajax({
        type: "POST",
        data: "cod_pedido=" + cod_pedido + "&num_item=" + num_item + "&cod_mesa=" + cod_mesa,
        url: "../../procesos/despachar_pedido.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            w_alert({
              titulo: 'Pedido despachado',
              tipo: 'success'
            });
            document.getElementById('div_loader').style.display = 'none';
            cargar_div(cod_pedido, orden);
          } else {
            w_alert({
              titulo: datos['consulta'],
              tipo: 'danger'
            });
            if (datos['consulta'] == 'Reload') {
              document.getElementById('div_login').style.display = 'block';
              cerrar_loader();

            }
            document.getElementById("des_" + cod_pedido + "_" + num_item).disabled = false;
            document.getElementById('div_loader').style.display = 'none';
          }

        }
      });
    }

    function pedido_terminado(cod_pedido, area) {
      document.getElementById('div_loader').style.display = 'block';
      document.getElementById("ter_" + cod_pedido).disabled = true;
      $.ajax({
        type: "POST",
        data: "cod_pedido=" + cod_pedido,
        url: "../../procesos/terminar_pedido.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            w_alert({
              titulo: 'Pedido terminado',
              tipo: 'success'
            });
            lista_pedidos('PENDIENTE');
            lista_pedidos('TERMINADO');
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
          document.getElementById("ter_" + cod_pedido).disabled = false;
          document.getElementById('div_loader').style.display = 'none';
        }
      });
    }

    function cambiarVisualizacion(type, value) {
      localStorage.setItem(type, value);
      $.ajax({
        type: "POST",
        data: "tipo=" + type + "&valor=" + value,
        url: "../../procesos/vistaProductosAreas.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            w_alert({
              titulo: 'Vista actualizada',
              tipo: 'success'
            });
            window.location.reload();
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
          document.getElementById('div_loader').style.display = 'none';
        }
      });
    }

    if (localStorage.getItem('Bar') == 'true')
      document.getElementById('inputCheckBar').checked = true;
    if (localStorage.getItem('Cocina') == 'true')
      document.getElementById('inputCheckCocina').checked = true;
    if (localStorage.getItem('Horno') == 'true')
      document.getElementById('inputCheckHorno').checked = true;
    if (localStorage.getItem('Horno2') == 'true')
      document.getElementById('inputCheckHorno2').checked = true;
  </script>

<?php
} else
  header("Location:login.php");
?>