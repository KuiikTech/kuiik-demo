<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
$fecha_40min = date("Y-m-d G:i:s", strtotime($fecha_h . "+ 40 minute"));

require_once "../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();
session_set_cookie_params(7 * 24 * 60 * 60);
session_start();

if (isset($_SESSION['usuario_restaurante'])) {
  $usuario = $_SESSION['usuario_restaurante'];

  $sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto`, `color` FROM `usuarios` WHERE codigo='$usuario'";
  $result_e = mysqli_query($conexion, $sql_e);
  $ver_e = mysqli_fetch_row($result_e);

  $cedula = $ver_e[1];

  $nombre_usuario = $ver_e[2] . ' ' . $ver_e[3];
  $rol = $ver_e[5];

  if ($ver_e[7] == '')
    $url_avatar = 'user.svg';
  else
    $url_avatar = $ver_e[7];

  $tecnico_filtro = '';
  $busqueda = '';
  $estado_get = '';
  $busqueda_get = '';
  $dia = '';

  if (isset($_GET['estado'])) {
    if ($_GET['estado'] != 'GARANTIA' && $_GET['estado'] != 'PEDIDO') {
      if ($_GET['estado'] == 'EN_ESPERA')
        $_GET['estado'] = 'EN ESPERA';
      $estado = "estado = '" . $_GET['estado'] . "' AND informacion NOT LIKE '%" . 'tipo":"Garantía"' . "%' AND informacion NOT LIKE '%" . 'tipo":"Pedido"' . "%'";
    } else {
      if ($_GET['estado'] == 'GARANTIA')
        $estado = "informacion LIKE '%" . 'tipo":"Garantía"' . "%' AND estado != 'ANULADO'";
      else
        $estado = "informacion LIKE '%" . 'tipo":"Pedido"' . "%' AND estado != 'ANULADO'";
    }
  }

  if (isset($_GET['busqueda'])) {
    $busqueda1 = str_replace("***", "%", $_GET['busqueda']);
    $sql_daños = "SELECT GROUP_CONCAT(DISTINCT `codigo` ORDER BY codigo ASC SEPARATOR '" . '"' . "%') FROM `tipo_daños` WHERE nombre LIKE '%" . $busqueda1 . "%' ";
    $result_daños = mysqli_query($conexion, $sql_daños);
    $ver_daños = mysqli_fetch_row($result_daños);

    $busqueda = "WHERE cliente LIKE '%" . $busqueda1 . "%' OR daños LIKE '%" . $busqueda1 . "%' OR daños LIKE '%" . '"' . $ver_daños[0] . '"' . "%' ";

    if (isset($_GET['dia'])) {
      if ($_GET['dia'] == 'hoy')
        $busqueda .= "AND date(fecha_entrega) = '" . date("Y-m-d", strtotime($fecha)) . "'";
      if ($_GET['dia'] == 'mañana')
        $busqueda .= "AND date(fecha_entrega) = '" . date("Y-m-d", strtotime($fecha . ' +1 day')) . "'";
    }

    $busqueda .= " order by estado DESC, fecha_entrega DESC";

    $fecha_sql = date('Y-m-d');
    $sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro`, `local` FROM `servicios` $busqueda";
    $result = mysqli_query($conexion, $sql);
  } else {
    if (isset($_GET['tecnico'])) {
      if (($_GET['tecnico']) == 'Todos')
        $tecnico_filtro = 'order by estado DESC, fecha_entrega ASC';
      else
        $tecnico_filtro = 'AND tecnico = ' . $_GET['tecnico'] . ' order by estado DESC, fecha_entrega ASC';
    } else
      $tecnico_filtro = 'order by estado DESC, fecha_entrega ASC';

    if (isset($_GET['dia'])) {
      if ($_GET['dia'] == 'hoy')
        $dia = "AND date(fecha_entrega) = '" . date("Y-m-d", strtotime($fecha)) . "'";
      if ($_GET['dia'] == 'mañana')
        $dia = "AND date(fecha_entrega) = '" . date("Y-m-d", strtotime($fecha . ' +1 day')) . "'";
    }

    $fecha_sql = date('Y-m-d');
    $sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro`, `local` FROM `servicios` WHERE $estado $dia $tecnico_filtro";
    $result = mysqli_query($conexion, $sql);
  }

  if (isset($_GET['estado'])) {
    $nombre_tabla = 'SERVICIOS [' . $_GET['estado'] . ']';
    $estado_get = '&estado=' . $_GET['estado'];
  } else
    $nombre_tabla = 'SERVICIOS';

  if (isset($_GET['busqueda'])) {
    $busqueda_get = '&busqueda=' . $_GET['busqueda'];
    $nombre_tabla .= '<small>[Busqueda: ' . str_replace("***", " ", $_GET['busqueda']) . ']</small>';
  }

?>
  <div class="card text-xsmall p-0">
    <div class="card-header text-center p-1 bg-100">
      <h4 class="mb-0"><?php echo $nombre_tabla ?></h4>
    </div>
    <div class="card-body p-1 pt-1">
      <div class="row m-0 p-2 text-right">
        <div class="col text-right">
          <button class="btn btn-sm btn-outline-danger ml-auto btn-round" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_tabla_servicios').load('tablas/tabla_servicios.php/?dia=hoy<?php echo $estado_get . $busqueda_get ?>', cerrar_loader());">HOY</button>
          <button class="btn btn-sm btn-outline-warning ml-auto btn-round" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_tabla_servicios').load('tablas/tabla_servicios.php/?dia=mañana<?php echo $estado_get . $busqueda_get ?>', cerrar_loader());">MAÑANA</button>
          <button class="btn btn-sm btn-outline-primary ml-auto btn-round" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_tabla_servicios').load('tablas/tabla_servicios.php/?<?php echo $estado_get . $busqueda_get ?>', cerrar_loader());">TODOS</button>
        </div>
      </div>

      <table width="100%" class="table text-dark table-sm" id="tabla_servicios">
        <thead>
          <tr class="text-center">
            <th class="p-0 no-sort" width="10px">#</th>
            <th class="p-0" width="10px">Cod</th>
            <th class="p-0 no-sort">Cliente</th>
            <th class="p-0 no-sort">Equipo</th>
            <th class="p-0" width="200px">Fecha Entrega</th>
            <th class="p-0" width="150px">Técnico</th>
            <th class="p-0 no-sort">Estado</th>
          </tr>
        </thead>
        <div class="overflow-auto">
          <tbody class="overflow-auto">
            <?php
            $num_item = 1;
            while ($mostrar = mysqli_fetch_row($result)) {
              $codigo = $mostrar[0];
              if ($mostrar[1] != '') {
                $items = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[1]);
                $items = json_decode($items, true);
              }

              if ($mostrar[4] != '') {
                $informacion = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[4]);
                $informacion = json_decode($informacion, true);
              }

              if (isset($informacion['equipo'])) {
                $cod_equipo = $informacion['equipo'];

                $sql_equipo = "SELECT `codigo`, `nombre`, `estado`, `fecha_creacion`, `creador` FROM `tipo_equipos` WHERE codigo = '$cod_equipo'";
                $result_equipo = mysqli_query($conexion, $sql_equipo);
                $mostrar_equipo = mysqli_fetch_row($result_equipo);

                if ($mostrar_equipo != null)
                  $titulo = '<b>' . $mostrar_equipo[1] . ' </b>| ';
                else
                  $titulo = '';
              } else {
                $cod_equipo = '';
                $titulo = '';
              }

              $info_equipo = array();
              if (isset($informacion['equipo'])) {
                if (isset($informacion['lista_info'])) {
                  $info_equipo = $informacion['lista_info'];

                  foreach ($info_equipo as $i => $item)
                    $titulo .= ' ' . $item['nombre'] . ': <b>' . $item['valor'] . '</b>';
                }
              }

              if ($mostrar[10] != null) {
                $fecha_hora_entrega = date("Y-m-d h:i A", strtotime($mostrar[10]));
                $fecha_entrega = date("Y-m-d", strtotime($fecha_hora_entrega));
                $hora_entrega = date("h:i A", strtotime($fecha_hora_entrega));
              } else {
                $fecha_entrega = '<b class="text-info">SIN ASIGNAR</b>';
                $hora_entrega = '<b class="text-info">SIN ASIGNAR</b>';
              }

              $estado = $mostrar[9];
              $local = $mostrar[12];
              $creador = $mostrar[7];

              if (($creador == $usuario) || $rol == 'Administrador' || $rol == 'Técnico' || isset($_GET['tecnico']) || isset($_GET['busqueda'])) {
                $color_creador = '';

                $sql_e = "SELECT nombre, apellido, rol, foto, color FROM `usuarios` WHERE codigo = '$creador'";
                $result_e = mysqli_query($conexion, $sql_e);
                $ver_e = mysqli_fetch_row($result_e);
                if ($ver_e != null) {
                  $nombre_aux = explode(' ', $ver_e[0]);
                  $apellido_aux = explode(' ', $ver_e[1]);
                  $creador = ucfirst(strtolower($nombre_aux[0])); //.' '.$apellido_aux[0];

                  $color_creador = $ver_e[4];
                }

                $cliente['nombre'] = 'Cliente No encontrado';
                if ($mostrar[2] != '') {
                  $mostrar[2] = trim($mostrar[2]);
                  $cliente = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[2]);
                  $cliente = str_replace(' ', ' ', $cliente);
                  $cliente = json_decode($cliente, true);

                  $cod_cliente = $cliente['codigo'];
                } else {
                  $cliente['direccion'] = '';
                  $cliente['correo'] = '';
                  $cliente['id'] = '';
                }

                if (!isset($cliente['id']))
                  $cliente['id'] = '???';



                $nombre_tec = '<b class="text-danger">No asignado</b>';
                $color_tecnico = 'border: none !important';
                if ($mostrar[8] != null && $mostrar[8] != '0') {
                  $cod_tecnico = $mostrar[8];

                  $sql_tec = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto`, `color` FROM `usuarios` WHERE codigo = '$cod_tecnico'";
                  $result_tec = mysqli_query($conexion, $sql_tec);
                  $mostrar_tec = mysqli_fetch_row($result_tec);

                  $nombre_tec = $mostrar_tec[2]; //.' '.$mostrar_tec[3];
                  $color_tecnico = 'background-color: ' . $mostrar_tec[8] . ' !important;';
                }

                $dia_hoy = date("Y-m-d", strtotime($fecha));
                $fecha_serv = date("Y-m-d", strtotime($fecha_entrega));

                $dia_servicio = '';
                if ($mostrar[10] != null) {
                  if ($dia_hoy >= $fecha_serv)
                    $dia_servicio = '<b class="text-danger">HOY</b>';
                }

                if ($estado == 'PENDIENTE') {
                  $bg_estado = 'bg-danger';
                  if ($mostrar[10] != null) {
                    if ($dia_hoy >= $fecha_serv)
                      $dia_servicio = '<b class="text-danger">HOY</b>';
                  }
                } else if ($estado == 'TERMINADO') {
                  $bg_estado = 'bg-success';
                  if ($mostrar[10] != null) {
                    if ($dia_hoy >= $fecha_serv)
                      $dia_servicio = '<b class="text-danger">HOY</b>';
                  }
                } else {
                  $bg_estado = 'bg-info';
                  if (isset($informacion['fecha_entrega'])) {
                    $fecha_entrega = date("Y-m-d", strtotime($informacion['fecha_entrega']));
                    $hora_entrega = date("h:i A", strtotime($informacion['fecha_entrega']));
                  }
                  if ($mostrar[10] != null) {
                    if ($dia_hoy == $fecha_entrega)
                      $dia_servicio = '<b class="text-danger">HOY</b>';
                    else
                      $dia_servicio = '';
                  }
                }

                $titulo = substr($titulo, 0, 80);
            ?>
                <tr onclick="mostrar_servicio('<?php echo $mostrar[0] ?>')" class="text-dark tr_servicio" style="cursor: pointer;">
                  <td class="text-center p-1" style="<?php echo $color_tecnico ?>"><small><?php echo $num_item ?></small></td>
                  <td class="text-center p-1"><b><?php echo str_pad($codigo, 3, "0", STR_PAD_LEFT) ?></b></td>
                  <td class="p-1 fst-italic lh-1">
                    <?php echo ucwords(strtolower($cliente['nombre'])) ?>
                    <br>
                    <small><b><?php echo 'CC:' . $cliente['id'] . ' -- Tel:' . $cliente['telefono'] ?></b></small>
                  </td>
                  <td class="p-1"><?php echo $titulo ?></td>
                  <td class="text-center p-0 px-1 lh-1">
                    <div class="row m-0 p-0 pl-1">
                      <?php
                      if ($informacion['tipo'] == 'Garantía') {
                        $servicio_asociado = $informacion['servicio_asociado'];
                      ?>
                        <div class="col-auto m-0 p-0 my-auto" title="Garantía servicio # <?php echo str_pad($servicio_asociado, 3, "0", STR_PAD_LEFT) ?>">
                          <span class="fas fa-certificate text-info"></span>
                        </div>
                      <?php
                      }
                      ?>
                      <div class="col-auto p-1 border-end border-2 my-auto"><?php echo $dia_servicio ?></div>
                      <div class="col-auto p-1">
                        <small class="text-dark">Fecha: <b><?php echo $fecha_entrega ?></b></small>
                        <br class="p-0 m-0">
                        <small class="text-dark">Hora: <b><?php echo $hora_entrega ?></b></small>
                      </div>
                    </div>
                  </td>
                  <td class="text-center p-1"><?php echo $nombre_tec ?></td>
                  <td class="text-center p-1 text-white lh-1 <?php echo $bg_estado ?>">
                    <b><?php echo $estado ?></b>
                    <br>
                    <small class="fst-italic text-dark"><?php echo $local . '(<b>' . $creador . '</b>)' ?></small>
                  </td>
                </tr>
            <?php
                $num_item += 1;
              }
            }
            ?>
          </tbody>
        </div>
      </table>
    </div>
  </div>
<?php
} else {
?>
  <script type="text/javascript">
    window.location = "login.php";
  </script>
<?php
}
?>


<script type="text/javascript">
  $(document).ready(function() {
    $('#tabla_servicios').DataTable({
      "columnDefs": [{
        "targets": 'no-sort',
        "orderable": false,
      }]
    });
  });
</script>