<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
$fecha_40min = date("Y-m-d G:i:s",strtotime($fecha_h."+ 40 minute"));

require_once "../clases/conexion.php";
$obj= new conectar(); 
$conexion=$obj->conexion();
$conexion=$obj->conexion();
session_set_cookie_params(7*24*60*60);
session_start();

if(isset($_SESSION['usuario_restaurante']))
{
  $usuario = $_SESSION['usuario_restaurante'];

  $sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto`, `color` FROM `usuarios` WHERE codigo='$usuario'";
  $result_e=mysqli_query($conexion,$sql_e);
  $ver_e=mysqli_fetch_row($result_e);

  $cedula = $ver_e[1];

  $nombre_usuario = $ver_e[2].' '.$ver_e[3];
  $rol = $ver_e[5];

  if ($ver_e[7] == '')
    $url_avatar = 'user.svg';
  else
    $url_avatar = $ver_e[7];

  $num_tabla=$_GET['num_tabla'];
  $tecnico_filtro='';
  $busqueda='';

  if(isset($_GET['busqueda']))
  {
    $busqueda = "AND cliente LIKE '%". $_GET['busqueda'] ."%' order by estado ASC, fecha_entrega DESC";
    if ($num_tabla == 1) 
    {
      $nombre_tabla = 'HOY';
      $fecha_sql=date('Y-m-d');
      $sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro`, `local` FROM `servicios` WHERE DATE(`fecha_entrega`)<=DATE('$fecha_sql') $busqueda";
      $result=mysqli_query($conexion,$sql);
    }
    if ($num_tabla == 2) 
    {
      $nombre_tabla = 'MAÑANA';
      $fecha_sql= date("Y-m-d",strtotime($fecha."+ 1 days"));
      $sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro`, `local` FROM `servicios` WHERE DATE(`fecha_entrega`)=DATE('$fecha_sql') $busqueda";
      $result=mysqli_query($conexion,$sql);
    }
    if ($num_tabla == 3) 
    {
      $nombre_tabla = 'PASADO MAÑANA';
      $fecha_sql= date("Y-m-d",strtotime($fecha."+ 2 days"));
      $sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro`, `local` FROM `servicios` WHERE DATE(`fecha_entrega`)=DATE('$fecha_sql') $busqueda";
      $result=mysqli_query($conexion,$sql);
    }
    if ($num_tabla == 4) 
    {
      $nombre_tabla = 'PRÓXIMOS DIAS';
      $fecha_sql= date("Y-m-d",strtotime($fecha."+ 3 days"));
      $sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro`, `local` FROM `servicios` WHERE DATE(`fecha_entrega`)>=DATE('$fecha_sql') $busqueda";
      $result=mysqli_query($conexion,$sql);
    }

  }
  else
  {
    if (isset($_GET['tecnico']))
    {
      if(($_GET['tecnico']) == 'Todos')
        $tecnico_filtro = 'order by estado ASC, fecha_entrega ASC';
      else
        $tecnico_filtro = 'AND tecnico = '. $_GET['tecnico'] .' order by estado ASC, fecha_entrega DESC';
    }
    else
      $tecnico_filtro = 'order by estado ASC, fecha_entrega ASC';

    if ($num_tabla == 1) 
    {
      $nombre_tabla = 'HOY';
      $fecha_sql=date('Y-m-d');
      $sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro`, `local` FROM `servicios` WHERE DATE(`fecha_entrega`)<=DATE('$fecha_sql') AND estado != 'ENTREGADO' $tecnico_filtro";
      $result=mysqli_query($conexion,$sql);
    }
    if ($num_tabla == 2) 
    {
      $nombre_tabla = 'MAÑANA';
      $fecha_sql= date("Y-m-d",strtotime($fecha."+ 1 days"));
      $sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro`, `local` FROM `servicios` WHERE DATE(`fecha_entrega`)=DATE('$fecha_sql') AND estado != 'ENTREGADO' $tecnico_filtro";
      $result=mysqli_query($conexion,$sql);
    }
    if ($num_tabla == 3) 
    {
      $nombre_tabla = 'PASADO MAÑANA';
      $fecha_sql= date("Y-m-d",strtotime($fecha."+ 2 days"));
      $sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro`, `local` FROM `servicios` WHERE DATE(`fecha_entrega`)=DATE('$fecha_sql') AND estado != 'ENTREGADO' $tecnico_filtro";
      $result=mysqli_query($conexion,$sql);
    }
    if ($num_tabla == 4) 
    {
      $nombre_tabla = 'PRÓXIMOS DIAS';
      $fecha_sql= date("Y-m-d",strtotime($fecha."+ 3 days"));
      $sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro`, `local` FROM `servicios` WHERE DATE(`fecha_entrega`)>=DATE('$fecha_sql') AND estado != 'ENTREGADO' $tecnico_filtro";
      $result=mysqli_query($conexion,$sql);
    }
  }
  $tamaño = 'style="height: 350px;"';

  ?>
  <div class="card text-xsmall">
    <div class="card-header text-center p-2">
      <h4 class="mb-0"><?php echo $nombre_tabla ?></h4>
    </div>
    <div class="card-body p-0 overflow-auto" <?php echo $tamaño ?> >
      <ul class="list-group list-group-flush">
        <?php 
        $num_item = 1;
        while ($mostrar=mysqli_fetch_row($result)) 
        { 
          if($mostrar[1] != '')
          {
            $items = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[1]);
            $items = json_decode($items,true);
          }

          if($mostrar[4] != '')
          {
            $informacion = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[4]);
            $informacion = json_decode($informacion,true);
          }

          $cod_equipo = $informacion['equipo'];

          $sql_equipo = "SELECT `codigo`, `nombre`, `estado`, `fecha_creacion`, `creador` FROM `tipo_equipos` WHERE codigo = '$cod_equipo'";
          $result_equipo=mysqli_query($conexion,$sql_equipo);
          $mostrar_equipo=mysqli_fetch_row($result_equipo);

          $titulo = '<b>'.$mostrar_equipo[1].' </b>| ';

          $info_equipo = array();
          if(isset($informacion['equipo']))
          { 
            if(isset($informacion['lista_info']))
            {
              $info_equipo = $informacion['lista_info'];

              foreach ($info_equipo as $i => $item)
                $titulo .= ' '.$item['nombre'].': <b>'.$item['valor'].'</b>';
            }
          }

          $fecha_hora_entrega = date("Y-m-d h:i A",strtotime($mostrar[10]));
          $fecha_entrega = date("Y-m-d",strtotime($fecha_hora_entrega));
          $hora_entrega = date("h:i A",strtotime($fecha_hora_entrega));
          $estado = $mostrar[9];
          $local = $mostrar[12];
          $creador = $mostrar[7];

          if(($creador == $usuario) || $rol == 'Administrador' || $rol == 'Técnico' || isset($_GET['tecnico']) || isset($_GET['busqueda']))
          {
            $color_creador = '';

            $sql_e = "SELECT nombre, apellido, rol, foto, color FROM `usuarios` WHERE codigo = '$creador'";
            $result_e=mysqli_query($conexion,$sql_e);
            $ver_e=mysqli_fetch_row($result_e);
            if($ver_e != null)
            {
              $nombre_aux = explode(' ', $ver_e[0]);
              $apellido_aux = explode(' ', $ver_e[1]);
              $creador = $nombre_aux[0].' '.$apellido_aux[0];

              $color_creador = $ver_e[4];
            }

            $cliente['nombre'] = 'Cliente No encontrado';
            if($mostrar[2] != '')
            {
              $mostrar[2] = trim($mostrar[2]);
              $cliente = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[2]);
              $cliente = str_replace(' ', ' ', $cliente);
              $cliente = json_decode($cliente,true);

              $cod_cliente = $cliente['codigo'];
            }

            $nombre_tec = '<b class="text-danger">No asignado</b>';
            $color_tecnico = 'border: none !important';
            if($mostrar[8] != null && $mostrar[8] != '0')
            {
              $cod_tecnico = $mostrar[8];

              $sql_tec = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto`, `color` FROM `usuarios` WHERE codigo = '$cod_tecnico'";
              $result_tec=mysqli_query($conexion,$sql_tec);
              $mostrar_tec=mysqli_fetch_row($result_tec);

              $nombre_tec = $mostrar_tec[2].' '.$mostrar_tec[3];
              $color_tecnico = 'border-color: '.$mostrar_tec[8].' !important;';
            }

            $sql_cliente = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro` FROM `clientes` WHERE `codigo`='$cod_cliente'";
            $result_cliente=mysqli_query($conexion,$sql_cliente);
            $cliente = $result_cliente->fetch_object();
            $cliente = json_encode($cliente,JSON_UNESCAPED_UNICODE);
            $cliente = json_decode($cliente, true);

            $date1 = new DateTime($fecha_40min);
            $date2 = new DateTime($fecha_hora_entrega);
            $diff = $date1->diff($date2);

            $tiempo = ( ($diff->days * 24 ) * 60 ) + ( $diff->h * 60 ) + $diff->i;

            $proximo = 0;
            if ($diff->invert == 1 && $estado == 'PENDIENTE' && $mostrar[9] == 1 && ($tiempo>=40))
              $proximo = 1;

            if ($estado == 'PENDIENTE') 
              $bg_estado = 'bg-danger';
            else if($estado == 'TERMINADO')
              $bg_estado = 'bg-success';
            else
              $bg_estado = 'bg-info';
            ?>
            <a href="javascript:mostrar_servicio('<?php echo $mostrar[0] ?>')" class="list-group-item-bg list-group-item p-1" style="text-decoration: none;">
              <div class="row justify-content-sm-between align-items-center px-1 mx-0 border-start border-5" style="<?php echo $color_tecnico ?>">
                <div class="row justify-content-sm-between align-items-center px-1 mx-0">
                  <h6 class="d-inline-block m-0 text-truncate"><?php echo $titulo ?></h6>
                </div>
                <div class="row justify-content-sm-between align-items-center px-1 mx-0">
                  <div class="col-sm-6 col-md-6 col-6 text-truncate px-1 mb-sm-0 align-middle">
                    <div class="row lh-1">
                      <small class="text-dark">Código: <?php echo str_pad($mostrar[0],5,"0",STR_PAD_LEFT) ?></small>
                      <br class="p-0 m-0">
                      <small class="text-dark">Tec: <b><?php echo $nombre_tec ?></b></small>
                      <br class="p-0 m-0">
                      <small class="text-dark">Recepción: <b><?php echo $local.'('.$creador.')' ?></b></small>
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-6 col-6 text-truncate px-1">
                    <div class="row">
                      <h6 class="my-0"><?php echo ucwords(strtolower($cliente['nombre'])) ?></h6>
                    </div>
                    <div class="row lh-1">
                      <div class="align-items-center col-sm-7 col-md-7 col-7 text-truncate">
                        <small class="text-dark">Fecha: <b><?php echo $fecha_entrega ?></b></small>
                        <br class="p-0 m-0">
                        <small class="text-dark">Hora: <b><?php echo $hora_entrega ?></b></small>
                      </div>
                      <div class="col-sm-5 col-md-5 col-5 text-truncate d-flex align-items-center text-center <?php echo $bg_estado ?>">
                        <h6 class="mb-0 fw-bold text-white text-center"><?php echo $estado ?></h6>
                      </div>
                    </div>
                  </div>

                </div>
              </div>

            </a>

            <?php 
            if ($proximo === 1) 
            {
              ?>
              <script type="text/javascript">
                notificar('<?php echo $mostrar[1] ?>','<?php echo $mostrar_cliente[2] ?>','<?php echo $mostrar[0] ?>');
              </script>
              <?php 
            }
            $num_item += 1;
          } 
        }
        ?>
      </ul>
    </div>
  </div>
  <?php 
}
else
{
  ?>
  <script type="text/javascript">
    window.location="login.php";
  </script>
  <?php 
}
?>


<script type="text/javascript">
  $(document).ready(function()
  {
    $('#tabla_servicios_<?php echo $num_tabla ?>').DataTable(
    {
      responsive: false,
      searching: false,
      paging: false,
      info: false
    });
  });
  
</script>