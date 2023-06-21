<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();

$cod_repuesto_cotizado = $_GET['cod_repuesto_cotizado'];

$sql = "SELECT `codigo`, `producto`, `proveedor`, `creador`, `fecha_registro`, `observaciones`, `estado` FROM `repuestos_cotizados`WHERE codigo = '$cod_repuesto_cotizado'";
$result=mysqli_query($conexion,$sql);
$mostrar=mysqli_fetch_row($result);

$productos_repuesto_cotizado = array();
if($mostrar[1] != '')
  $productos_repuesto_cotizado = json_decode($mostrar[1],true);
$proveedor = array();
if($mostrar[2] != '')
  $proveedor = json_decode($mostrar[2],true);
$creador = $mostrar[3];
$observaciones = $mostrar[5];
$estado = $mostrar[6];

$fecha_registro = date('d-m-Y h:i A',strtotime($mostrar[4]));

$sql_e = "SELECT nombre, apellido, rol, foto, color FROM `usuarios` WHERE codigo = '$creador'";
$result_e=mysqli_query($conexion,$sql_e);
$ver_e=mysqli_fetch_row($result_e);
if($ver_e != null)
{
  $nombre_aux = explode(' ', $ver_e[0]);
  $apellido_aux = explode(' ', $ver_e[1]);
  $creador = $nombre_aux[0].' '.$apellido_aux[0];
}

$costo_total = 0;

if($estado == '')
  $estado_v = '<b class="text-danger">PENDIENTE</b>';
else if($estado == 'CRÉDITO')
  $estado_v = '<b class="text-warning">CRÉDITO</b>';
else
  $estado_v = '<b>'.$estado.'</b>';

?>
<div class="modal-header text-center p-2">
  <h5 class="modal-title">Detalles de repuesto cotizado</h5>
</div>
<div class="modal-body p-2">
  <div class="row">
    <p class="row mb-0">
      <span class="col-lg-4 col-md-4 col-sm-4 col-xs-4 col-4 text-right text-truncate"> Proveedor: </span>
      <span class="col-lg-8 col-md-8 col-sm-8 col-xs-8 col-8 text-left"><b> <?php echo $proveedor['nombre'] ?> </b></span>
    </p>
    <p class="row mb-0">
      <span class="col-lg-4 col-md-4 col-sm-4 col-xs-4 col-4 text-right text-truncate"> Creador: </span>
      <span class="col-lg-8 col-md-8 col-sm-8 col-xs-8 col-8 text-left"><b> <?php echo $creador ?> </b></span>
    </p>
    <p class="row mb-0">
      <span class="col-lg-4 col-md-4 col-sm-4 col-xs-4 col-4 text-right text-truncate"> Fecha Registro: </span>
      <span class="col-lg-8 col-md-8 col-sm-8 col-xs-8 col-8 text-left"><b> <?php echo $fecha_registro ?> </b></span>
    </p>
    <p class="row mb-0">
      <span class="col-lg-4 col-md-4 col-sm-4 col-xs-4 col-4 text-right text-truncate"> Estado: </span>
      <span class="col-lg-8 col-md-8 col-sm-8 col-xs-8 col-8 text-left"><b> <?php echo $estado_v ?> </b></span>
    </p>
    <p class="row mb-0">
      <span class="col-lg-4 col-md-4 col-sm-4 col-xs-4 col-4 text-right text-truncate"> Observaciones: </span>
      <span class="col-lg-8 col-md-8 col-sm-8 col-xs-8 col-8 text-left"><b> <?php echo $observaciones ?> </b></span>
    </p>

  <div class="row m-0 mt-2" id="div_tabla_adelantos">
    <div class="border-top text-center px-2">
      <table class="table text-dark table-sm w-100 mb-0" id="tabla_inventario_<?php echo $codigo ?>">
        <thead>
          <tr class="text-center bg-300 text-dark">
            <th width="10px" class="p-0">#</th>
            <th class="p-0">Producto</th>
            <th width="80px" class="p-0">Marca</th>
            <th width="120px" class="p-0">Valor Público</th>
            <th width="120px" class="p-0">Valor Mayor</th>
            <th width="80px" class="p-0">Costo</th>
          </tr>
        </thead>
        <tbody class="overflow-auto text-dark">
          <?php 
          foreach ($productos_repuesto_cotizado as $i => $item)
          {
            $cod_producto = $item['codigo'];
            $descripcion = $item['descripcion'];
            $categoria = $item['categoria'];
            $cant_bp = $item['cant_bp'];
            $cant_b1 = $item['cant_b1'];
            $cant_b2 = $item['cant_b2'];

            $marca = $item['marca'];

            $valor_venta = $item['valor_venta'];
            $valor_venta_mayor = $item['valor_venta_mayor'];
            $costo = $item['costo'];

            $editar = 0;
            if($cant_bp != '')
              $editar = 1;
            if($costo > 0)
              $costo_total = $costo;
            ?>
            <tr class="text-dark">
              <td class="p-1"><?php echo $i ?></td>
              <td class="p-1"><?php echo $descripcion ?></td>
              <td class="p-1 text-center"><?php echo $marca;?></td>
              <td class="p-1 text-right"><?php echo '$'.number_format($valor_venta,0,'.','.');?></td>
              <td class="p-1 text-right"><?php echo '$'.number_format($valor_venta_mayor,0,'.','.');?></td>
              <td class="p-1 text-right"><?php echo '$'.number_format($costo,0,'.','.'); ?></td>
            </tr>
            <?php 
          } 
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</div>

<!-- Modal Ver soporte-->
<div class="modal fade" id="Modal_Ver_Soporte" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" id="div_modal_ver_soporte">
      <img class="container py-2" src="" id="contenedor_soporte">
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary btn-round" onclick="$('#Modal_Ver_Soporte').modal('toggle');">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Subir soporte-->
<div class="modal fade" id="Modal_Subir_Soporte" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="overflow-y: scroll;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header text-center">
        <h3 class="modal-title">Cargar soporte de Pago</h3>
      </div>

      <div class="modal-body">
        <form id="frm_soporte" enctype="multipart/form-data">
          <input type="number" name="cod_repuesto_cotizado_upload" id="cod_repuesto_cotizado_upload" hidden="">
          <div class="row">
            <div class="col">
              <div class="custom-file">
                <label class="form-label" for="archivo_soporte">Seleccione un archivo (png, jpeg, jpg)</label>
                <input class="form-control form-control-sm" name="archivo_soporte" id="archivo_soporte" type="file" />
              </div>
              <div class="progress progress-sm mb-3">
                <div id="progress_bar_upload" class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
            <div class="col-auto my-auto">
              <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_subir">Subir</button>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-body">
       <button type="button" class="btn btn-sm btn-secondary btn-round" id="btn_cancelar_subir" onclick="$('#Modal_Subir_Soporte').modal('toggle');">Cancelar</button>
     </div>

   </div>
 </div>
</div>

<script type="text/javascript">

  var barra_estado = document.getElementById('progress_bar_upload');

  $('#btn_subir').click(function()
  {
    document.getElementById("btn_subir").disabled = true;
    barra_estado.classList.remove('bg-success');
    barra_estado.classList.add('bg-info');

    var datos = new FormData($("#frm_soporte")[0]);
    
    var peticion = new XMLHttpRequest();

    peticion.upload.addEventListener("progress",barra_progreso,false);
    peticion.addEventListener("load",proceso_completo,false);
    peticion.addEventListener("error",error_carga,false);
    peticion.addEventListener("abort",carga_abortada,false);

    peticion.open("POST","procesos/subir_soporte.php");
    peticion.send(datos);
  });

  function barra_progreso(event)
  {
    barra_estado.style.width = '0';
    porcentaje = Math.round((event.loaded/event.total)*100);
    barra_estado.style.width = porcentaje+'%';
  }

  function proceso_completo(event)
  {
    datos=jQuery.parseJSON(event.target.responseText);
    if(datos['consulta'] == 1)
    {
      $('#frm_soporte')[0].reset();
      barra_estado.classList.remove('bg-info');
      barra_estado.classList.add('bg-success');

      document.getElementById("btn_subir").disabled = false;
      w_alert({ titulo: 'Soporte Subido Correctamente', tipo: 'success' });
      document.getElementById('div_loader').style.display = 'block';
      $('#div_ver_repuesto_cotizado').load('paginas/detalles/detalles_repuesto_cotizado.php/?cod_repuesto_cotizado=<?php echo $cod_repuesto_cotizado ?>', function(){cerrar_loader();});
      $("#Modal_Subir_Soporte").modal('toggle');
      $('.modal-backdrop').remove();
    }
    else
    {
      w_alert({ titulo: datos['consulta'], tipo: 'danger' });
      document.getElementById("btn_subir").disabled = false;
    }
  }

  function error_carga(event)
  {
    w_alert({ titulo: 'Error al cargar el soporte', tipo: 'danger' });
    document.getElementById("btn_subir").disabled = false;
  }

  function carga_abortada(event)
  {
    w_alert({ titulo: 'Carga de soporte cancelada', tipo: 'danger' });
    document.getElementById("btn_subir").disabled = false;
  }

</script>