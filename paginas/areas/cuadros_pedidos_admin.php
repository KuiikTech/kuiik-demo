<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();

$sql_caja = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `gastos`, `creador`, `cajero`, `estado` FROM `caja` WHERE estado = 'ABIERTA' OR estado = 'CREADA'";
$result_caja=mysqli_query($conexion,$sql_caja);
$mostrar_caja=mysqli_fetch_row($result_caja);

$fecha_inicio = $mostrar_caja[1];

$area = $_GET['area'];

$sql = "SELECT `codigo`, `productos`, `mesa`, `solicitante`, `fecha_registro`, `fecha_envio`, `fecha_entrega`, `estado`, `area` FROM `pedidos_mesas_2` WHERE fecha_registro> '$fecha_inicio' AND area = '$area' AND (estado = 'PENDIENTE' OR estado = 'DESPACHADO' OR estado = 'PREPARANDO') ORDER BY FIELD(estado,'PENDIENTE','DESPACHADO','CANCELADO'), fecha_envio ASC";
$result=mysqli_query($conexion,$sql);

$orden = 1;
?>
<div class="card-columns">
  <?php  
  while ($mostrar=mysqli_fetch_row($result)) 
  { 
    $btn_terminado = 'disabled=""';
    $cod_pedido = $mostrar[0];
    $cod_mesa = $mostrar[2];
    $sql_mesa="SELECT `cod_mesa`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_apertura` FROM `mesas` WHERE cod_mesa='$cod_mesa'";
    $result_mesa=mysqli_query($conexion,$sql_mesa);
    $ver_mesa=mysqli_fetch_row($result_mesa);
    $nombre_mesa = $ver_mesa[1];

    $solicitante = $mostrar[3];

    $sql_e = "SELECT nombre, rol, foto FROM usuarios WHERE codigo = '$solicitante'";
    $result_e=mysqli_query($conexion,$sql_e);
    $ver_e=mysqli_fetch_row($result_e);

    $solicitante = $ver_e[0];

    $estado = $mostrar[7];
    $productos_pedido = json_decode($mostrar[1],true);

    $color_text = '';

    ?>
    <div class="card card-small go-stats mb-3">
      <div class="card-header border-bottom p-1 d-flex bg-info">
        <div class="col"><h6 class="m-0 text-dark"><?php echo $nombre_mesa ?></h6></div>
        <div class="col text-right"><h6 class="m-0"><?php echo $solicitante ?></h6></div>
      </div>
      <div class="card-body p-0">
        <ul class="list-group list-group-small list-group-flush">
          <?php 

          foreach ($productos_pedido as $i => $producto)
          {
            $notas = array();
            $cant = $producto['cant'];
            $total_producto = $producto['valor_unitario']*$producto['cant'];
            $nombre_producto = $producto['descripcion'];

            $estado = $producto['estado'];

            if($producto['notas'] != NULL)
              $notas = $producto['notas'];

            if(isset($producto['cod_pedido']))
            {
              $cod_pedido_2 = $producto['cod_pedido'];
              $sql_pedido = "SELECT `codigo`, `producto`, `cantidad`, `valor`, `mesa`, `solicitante`, `fecha_registro`, `fecha_entrega`, `estado`, `area` FROM `pedidos_mesas` WHERE codigo = '$cod_pedido_2'";
              $result_pedido=mysqli_query($conexion,$sql_pedido);
              $mostrar_pedido=mysqli_fetch_row($result_pedido);

              $mesero = $mostrar_pedido[5];

              $sql_e = "SELECT nombre, apellido, rol, foto FROM usuarios WHERE codigo = '$mesero'";
              $result_e=mysqli_query($conexion,$sql_e);
              $ver_e=mysqli_fetch_row($result_e);

              $mesero = $ver_e[0].' '.$ver_e[1];
            }
            else
              $mesero = '';

            if($estado == 'PENDIENTE')
            {
              $bg_tr = 'bg_pendiente';
              $color_text = 'text-danger';
              $terminar = 1;
            }
            else if($estado == 'PREPARANDO')
            {
              $bg_tr = 'bg_preparando';
              $btn_terminado = 'disabled=""';
              $terminar = 1;
            }
            else if($estado == 'DESPACHADO')
            {
              $bg_tr = 'bg_despachado';
              $btn_terminado = '';
              $terminar = 1;
            }
            else if($estado == 'CANCELADO')
            {
              $nombre_producto = '<s>'.$nombre_producto.'</s>';
              $bg_tr = 'bg_cancelado';
              $btn_terminado = '';
            }

            ?>
            <li class="list-group-item d-flex row px-0 m-1 p-0 <?php echo $bg_tr ?>">
              <div class="col-lg-8 col-md-8 col-sm-8 col-8">
                <h6 class="go-stats__label mb-1 text-dark"> <b><?php echo $cant ?></b> - <b><?php echo $nombre_producto ?></b></h6>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4 col-4 d-flex">
                <div class="go-stats__chart d-flex ml-auto mx-auto">
                  <?php 
                  if($estado == 'PENDIENTE' || $estado == 'PREPARANDO')
                  {
                    if($estado == 'PENDIENTE')
                    {
                      ?>
                      <button class="btn btn-info btn-pill ml-1 p-1" style="font-size: 1.2rem !important;" id="pre_<?php echo $cod_pedido.'_'.$i ?>" onclick="preparando_pedido('<?php echo $cod_mesa ?>','<?php echo $cod_pedido ?>','<?php echo $i ?>','<?php echo $area ?>')">
                        PREPARAR
                      </button>
                      <?php 
                    }
                    if($estado == 'PREPARANDO')
                    {
                      ?>
                      <button class="btn btn-success btn-pill ml-1 p-1" style="font-size: 1.2rem !important;" id="des_<?php echo $cod_pedido.'_'.$i ?>" onclick="pedido_despachado('<?php echo $cod_mesa ?>','<?php echo $cod_pedido ?>','<?php echo $i ?>','<?php echo $area ?>')">
                        DESPACHAR
                      </button>
                      <?php 
                    }
                  }
                  else if($estado == 'CANCELADO')
                    echo '<b>CANCELADO</b>';

                  ?>
                </div>
              </div>
              <?php 
              if($producto['notas'] != NULL)
              {
               ?>
               <div class="go-stats__meta pl-3">
                <span class="mr-2">
                  <?php 
                  foreach ($notas as $i => $nota)
                  {
                    echo '* '.$nota.'<br>';
                  }
                  ?>
                </span>
              </div>
              <?php 
            }
            ?>
          </li>
          <?php 
        } 
        ?>

      </ul>
    </div>
    <div class="card-footer border-top py-1">
      <div class="row">
        <div class="col">
          <button class="btn btn-info btn-pill btn-icon ml-1" <?php echo $btn_terminado ?> style="font-size: 1.2rem !important;" id="ter_<?php echo $cod_pedido ?>" onclick="pedido_terminado('<?php echo $cod_pedido ?>','<?php echo $area ?>')">
            TERMINADO
          </button>
          <?php 
          if(!isset($terminar))
          {
            ?>
            <script type="text/javascript">
              document.getElementById("ter_<?php echo $cod_pedido ?>").click();
            </script>
            <?php 
          }
          ?>
        </div>
        <div class="col-2">
          <span class="badge badge-pill badge-warning px-3 pt-2 pb-0"><h4 class="text-dark"><b><?php echo $orden ?></b></h4></span>
        </div>
      </div>
    </div>
  </div>
  <?php 
  $orden ++;
} 
?>
</div>



<?php 
$sql = "SELECT `codigo`, `productos`, `mesa`, `solicitante`, `fecha_registro`, `fecha_envio`, `fecha_entrega`, `estado`, `area` FROM `pedidos_mesas_2` WHERE fecha_registro> '$fecha_inicio' AND area = '$area' AND (estado = 'TERMINADO') ORDER BY FIELD(estado,'PENDIENTE','DESPACHADO','CANCELADO'), fecha_envio ASC";
$result=mysqli_query($conexion,$sql);
?>
<hr>
<div class="row text-center">
  <div class="col">PEDIDOS ENTREGADOS</div>
</div>
<div class="card-columns">
  <?php  
  while ($mostrar=mysqli_fetch_row($result)) 
  { 
    $btn_terminado = 'disabled=""';
    $cod_pedido = $mostrar[0];
    $cod_mesa = $mostrar[2];
    $sql_mesa="SELECT `cod_mesa`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_apertura` FROM `mesas` WHERE cod_mesa='$cod_mesa'";
    $result_mesa=mysqli_query($conexion,$sql_mesa);
    $ver_mesa=mysqli_fetch_row($result_mesa);
    $nombre_mesa = $ver_mesa[1];

    $solicitante = $mostrar[3];

    $sql_e = "SELECT nombre, rol, foto FROM usuarios WHERE codigo = '$solicitante'";
    $result_e=mysqli_query($conexion,$sql_e);
    $ver_e=mysqli_fetch_row($result_e);

    $solicitante = $ver_e[0];

    $estado = $mostrar[7];
    $productos_pedido = json_decode($mostrar[1],true);

    $color_text = '';

    ?>
    <div class="card card-small go-stats mb-3">
      <div class="card-header border-bottom p-1 d-flex bg-gray">
        <div class="col"><h6 class="m-0 text-dark"><?php echo $nombre_mesa ?></h6></div>
        <div class="col text-right"><h6 class="m-0"><?php echo $solicitante ?></h6></div>
      </div>
      <div class="card-body p-0">
        <ul class="list-group list-group-small list-group-flush">
          <?php 
          foreach ($productos_pedido as $i => $producto)
          {
            $notas = array();
            $cant = $producto['cant'];
            $total_producto = $producto['valor_unitario']*$producto['cant'];
            $nombre_producto = $producto['descripcion'];

            $estado = $producto['estado'];

            if($producto['notas'] != NULL)
              $notas = $producto['notas'];

            if(isset($producto['cod_pedido']))
            {
              $cod_pedido_2 = $producto['cod_pedido'];
              $sql_pedido = "SELECT `codigo`, `producto`, `cantidad`, `valor`, `mesa`, `solicitante`, `fecha_registro`, `fecha_entrega`, `estado`, `area` FROM `pedidos_mesas` WHERE codigo = '$cod_pedido_2'";
              $result_pedido=mysqli_query($conexion,$sql_pedido);
              $mostrar_pedido=mysqli_fetch_row($result_pedido);

              $mesero = $mostrar_pedido[5];

              $sql_e = "SELECT nombre, apellido, rol, foto FROM usuarios WHERE codigo = '$mesero'";
              $result_e=mysqli_query($conexion,$sql_e);
              $ver_e=mysqli_fetch_row($result_e);

              $mesero = $ver_e[0].' '.$ver_e[1];
            }
            else
              $mesero = '';

            if($estado == 'PENDIENTE')
            {
              $bg_tr = 'bg_pendiente';
              $color_text = 'text-danger';
            }
            else if($estado == 'PREPARANDO')
            {
              $bg_tr = 'bg_preparando';
              $btn_terminado = 'disabled=""';
            }
            else if($estado == 'DESPACHADO')
            {
              $bg_tr = 'bg_despachado';
              $btn_terminado = '';
            }
            else if($estado == 'CANCELADO')
            {
              $nombre_producto = '<s>'.$nombre_producto.'</s>';
              $bg_tr = 'bg_cancelado';
            }

            ?>
            <li class="list-group-item d-flex row px-0 m-1 p-0 <?php echo $bg_tr ?>">
              <div class="col-lg-8 col-md-8 col-sm-8 col-8">
                <h6 class="go-stats__label mb-1 text-dark"> <b><?php echo $cant ?></b> - <b><?php echo $nombre_producto ?></b></h6>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4 col-4 d-flex">
                <div class="go-stats__chart d-flex ml-auto mx-auto">
                  <?php 
                  
                  if($estado == 'DESPACHADO')
                    echo '<b>DESPACHADO</b>';
                  else if($estado == 'CANCELADO')
                    echo '<b>CANCELADO</b>';

                  ?>
                </div>
              </div>
              <?php 
              if($producto['notas'] != NULL)
              {
               ?>
               <div class="go-stats__meta pl-3">
                <span class="mr-2">
                  <?php 
                  foreach ($notas as $i => $nota)
                  {
                    echo '* '.$nota.'<br>';
                  }
                  ?>
                </span>
              </div>
              <?php 
            }
            ?>
          </li>
          <?php 
        } 
        ?>

      </ul>
    </div>
  </div>
  <?php 
  $orden ++;
} 
?>
</div>

<script type="text/javascript">

  function pedido_despachado(cod_mesa,cod_pedido,num_item,area)
  {
    document.getElementById('div_loader').style.display = 'block';
    document.getElementById("des_"+cod_pedido+"_"+num_item).disabled = true;
    $.ajax({
      type:"POST",
      data:"cod_pedido="+cod_pedido+"&num_item="+num_item+"&cod_mesa="+cod_mesa,
      url:"procesos/despachar_pedido_2.php",
      success:function(r)
      {
        datos=jQuery.parseJSON(r);
        if (datos['consulta'] == 1)
        {
          w_alert({ titulo: 'Pedido despachado', tipo: 'success' });
          $('#div_contenido').load('paginas/areas/cuadros_pedidos_admin.php/?area='+area, cerrar_loader());
        }
        else
          w_alert({ titulo: datos['consulta'], tipo: 'danger' });
        setInterval("cambios('Cocina')",10000);
        document.getElementById("des_"+cod_pedido+"_"+num_item).disabled = false;
        document.getElementById('div_loader').style.display = 'none';
      }
    });
  }

  function preparando_pedido(cod_mesa,cod_pedido,num_item,area)
  {
    document.getElementById('div_loader').style.display = 'block';
    document.getElementById("pre_"+cod_pedido+"_"+num_item).disabled = true;
    $.ajax({
      type:"POST",
      data:"cod_pedido="+cod_pedido+"&num_item="+num_item+"&cod_mesa="+cod_mesa,
      url:"procesos/preparar_pedido_2.php",
      success:function(r)
      {
        datos=jQuery.parseJSON(r);
        if (datos['consulta'] == 1)
        {
          w_alert({ titulo: 'Preparando pedido', tipo: 'success' });
          $('#div_contenido').load('paginas/areas/cuadros_pedidos_admin.php/?area='+area, cerrar_loader());
        }
        else
          w_alert({ titulo: datos['consulta'], tipo: 'danger' });
        setInterval("cambios('Cocina')",10000);
        document.getElementById("pre_"+cod_pedido+"_"+num_item).disabled = false;
        document.getElementById('div_loader').style.display = 'none';
      }
    });
  }

  function pedido_terminado(cod_pedido,area)
  {
    document.getElementById('div_loader').style.display = 'block';
    document.getElementById("ter_"+cod_pedido).disabled = true;
    $.ajax({
      type:"POST",
      data:"cod_pedido="+cod_pedido,
      url:"procesos/terminar_pedido.php",
      success:function(r)
      {
        datos=jQuery.parseJSON(r);
        if (datos['consulta'] == 1)
        {
          w_alert({ titulo: 'Pedido terminado', tipo: 'success' });
          $('#div_contenido').load('paginas/areas/cuadros_pedidos_admin.php/?area='+area, cerrar_loader());
        }
        else
          w_alert({ titulo: datos['consulta'], tipo: 'danger' });
        setInterval("cambios('Cocina')",10000);
        document.getElementById("ter_"+cod_pedido).disabled = false;
        document.getElementById('div_loader').style.display = 'none';
      }
    });
  }

</script>