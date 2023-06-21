<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$cod_compra = $_GET['cod_compra'];
$palabras = '';

if (isset($_GET['palabras']))
  $palabras = $_GET['palabras'];

$sql = "SELECT `codigo`, `productos`, `proveedor`, `creador`, `estado`, `fecha_registro`, `observaciones`, `pagos`, `notas`, `factura` FROM `compras` WHERE codigo = '$cod_compra'";
$result = mysqli_query($conexion, $sql);
$mostrar = mysqli_fetch_row($result);

$productos_compra = array();
if ($mostrar[1] != '')
  $productos_compra = json_decode($mostrar[1], true);
$proveedor = array();
if ($mostrar[2] != '')
  $proveedor = json_decode($mostrar[2], true);
$creador = $mostrar[3];
$observaciones = $mostrar[6];
$estado = $mostrar[4];

$fecha_registro = date('d-m-Y h:i A', strtotime($mostrar[5]));

$sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
$result_e = mysqli_query($conexion, $sql_e);
$ver_e = mysqli_fetch_row($result_e);
if ($ver_e != null) {
  $nombre_aux = explode(' ', $ver_e[0]);
  $apellido_aux = explode(' ', $ver_e[1]);
  $creador = $nombre_aux[0] . ' ' . $apellido_aux[0];
}

$costo_total = 0;

if ($estado == '')
  $estado_v = '<b class="text-danger">PENDIENTE</b>';
else if ($estado == 'CRÉDITO')
  $estado_v = '<b class="text-warning">CRÉDITO</b>';
else
  $estado_v = '<b>' . $estado . '</b>';

$pagos = array();
if ($mostrar[7] != '')
  $pagos = json_decode($mostrar[7], true);

$notas = array();
if ($mostrar[8] != '')
  $notas = json_decode($mostrar[8], true);

$url_factura = '';
if ($mostrar[9] != '')
  $url_factura = json_decode($mostrar[9], true);

$busqueda = explode('***', $palabras);

?>
<div class="modal-header text-center p-2">
  <h5 class="modal-title">Detalles de compra</h5>
</div>
<div class="modal-body p-2">
  <div class="row m-0 p-1">
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
    <div class="row mb-0">
      <span class="col-lg-4 col-md-4 col-sm-4 col-xs-4 col-4 text-right text-truncate"> Observaciones: </span>
      <span class="col-lg-8 col-md-8 col-sm-8 col-xs-8 col-8 text-left" id="div_obs" ondblclick="document.getElementById('div_obs_edit').hidden = false;document.getElementById('div_obs').hidden = true;"><b> <?php echo str_replace("\n", '<br>', $observaciones) ?> </b></span>
      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 col-8 text-right" id="div_obs_edit" hidden>
        <textarea class="form-control form-control-sm p-2" name="input_observacion_edit" id="input_observacion_edit" rows="4"><?php echo $observaciones ?></textarea>
        <button class="btn btn-sm btn-outline-info btn-round my-1" id="btn_guardar_obs">Guardar</button>
      </div>
    </div>
    <p class="row mb-0">
      <span class="col-lg-4 col-md-4 col-sm-4 col-xs-4 col-4 text-right text-truncate"> Factura: </span>
      <span class="col-lg-8 col-md-8 col-sm-8 col-xs-8 col-8 text-left">
        <?php
        if ($mostrar[9] != '') {
          $url_imagen = 'soportes/compras/' . $url_factura['nombre'];
        ?>
          <a href="#" title="Ver factura" class="btn btn-sm btn-outline-info btn-round p-0 px-1" onclick="$('#Modal_Ver_Soporte').modal('show');document.getElementById('contenedor_soporte').src = '<?php echo $url_imagen ?>';">
            <span class="fa fa-image"></span>
          </a>
          <?php
          if ($estado != 'PAGADO') {
          ?>
            <a href="#" title="Eliminar factura" class="btn btn-sm btn-danger btn-round p-0 px-1" onclick="$('#cod_compra_eliminar_2').val(<?php echo $cod_compra ?>);$('#Modal_Eliminar_Factura').modal('show');">
              <span class="fa fa-times"></span>
            </a>
          <?php
          }
        } else {
          if ($estado != 'PAGADO') {
          ?>
            <a href="#" class="alert-link text-warning" onclick="$('#cod_compra_upload_2').val(<?php echo $cod_compra ?>);$('#Modal_Subir_Factura').modal('show');">
              <small>Subir Factura</small>
            </a>
        <?php
          }
        }
        ?>
      </span>
    </p>
    <div class="row mb-0">
      <span class="col-lg-4 col-md-4 col-sm-4 col-xs-4 col-4 text-right text-truncate"> Soporte de pago: </span>
      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 col-8 text-left">
        <div class="table-responsive text-dark text-center p-0">
          <table class="table text-dark table-sm w-100" id="tabla_pagos_servicio">
            <thead>
              <tr class="text-center bg-300 text-dark">
                <th width="30px" class="table-plus text-dark datatable-nosort p-0">#</th>
                <th class="p-0">Método</th>
                <th class="p-0">Valor</th>
                <th class="p-0" width="80px">Creador/Resp</th>
                <th class="p-0" width="120px"></th>
              </tr>
            </thead>
            <tbody class="overflow-auto">
              <?php
              $num_item = 1;
              $total_pagos = 0;
              foreach ($pagos as $i => $item) {
                $tipo = '---';
                if (isset($item['tipo']))
                  $tipo = $item['tipo'];
                $valor = 0;
                if (isset($item['valor']))
                  $valor = $item['valor'];
                $creador = '---';
                if (isset($item['creador']))
                  $creador = $item['creador'];
                $responsable = '---';
                if (isset($item['usuario']))
                  $responsable = $item['usuario'];
                $url_soporte = '';
                if (isset($item['nombre']))
                  $url_soporte = $item['nombre'];

                $fecha_creacion = '---';
                if (isset($item['fecha_creacion']))
                  $fecha_creacion = date('d-m-Y h:i A', strtotime($item['fecha_creacion']));

                $fecha_subida = '---';
                if (isset($item['fecha']))
                  $fecha_subida = date('d-m-Y h:i A', strtotime($item['fecha']));

                $sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
                $result_e = mysqli_query($conexion, $sql_e);
                $ver_e = mysqli_fetch_row($result_e);
                if ($ver_e != null) {
                  $nombre_aux = explode(' ', $ver_e[0]);
                  $apellido_aux = explode(' ', $ver_e[1]);
                  $creador = $nombre_aux[0];  //.' '.$apellido_aux[0];
                }

                $sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$responsable'";
                $result_e = mysqli_query($conexion, $sql_e);
                $ver_e = mysqli_fetch_row($result_e);

                if ($ver_e != null) {
                  $nombre_aux = explode(' ', $ver_e[0]);
                  $apellido_aux = explode(' ', $ver_e[1]);
                  $responsable = $nombre_aux[0];  //.' '.$apellido_aux[0];
                }

                $total_pagos += $valor;
              ?>
                <tr role="row" class="odd">
                  <td class="text-center p-0 text-muted"><?php echo $num_item ?></td>
                  <td class="text-center p-0"><?php echo $tipo ?></td>
                  <td class="text-right p-0"><b>$<?php echo number_format($valor, 0, '.', '.') ?></b></td>
                  <td class="text-center p-0 lh-1">
                    <small title="<?php echo $fecha_creacion ?>"><?php echo $creador ?></small>
                    <br>
                    <small title="<?php echo $fecha_subida ?>"><?php echo $responsable ?></small>
                  </td>
                  <td class="text-center p-0">
                    <?php
                    if ($url_soporte != '') {
                        $url_imagen = 'soportes/compras/' . $url_soporte;
                    ?>
                      <a href="#" title="Ver soporte" class="btn btn-sm btn-outline-info btn-round p-0 px-1" onclick="$('#Modal_Ver_Soporte').modal('show');document.getElementById('contenedor_soporte').src = '<?php echo $url_imagen ?>';">
                        <span class="fa fa-image"></span>
                      </a>
                    <?php
                    } else {
                    ?>
                      <a href="#" class="alert-link text-warning" onclick="$('#cod_compra_upload').val(<?php echo $cod_compra ?>);$('#item_upload').val(<?php echo $i ?>);$('#Modal_Subir_Soporte').modal('show');">
                        <small>Subir Soporte</small>
                      </a>
                    <?php
                    }
                    if ($estado != 'PAGADO') {
                    ?>
                      <a href="#" title="Eliminar soporte" class="btn btn-sm btn-danger btn-round p-0 px-1" onclick="$('#cod_compra_eliminar').val(<?php echo $cod_compra ?>);$('#item_eliminar').val(<?php echo $i ?>);$('#Modal_Eliminar_Pago').modal('show');">
                        <span class="fa fa-times"></span>
                      </a>
                    <?php
                    } ?>
                  </td>
                </tr>
              <?php
                $num_item++;
              }
              ?>
              <tr class="bg-200 text-dark">
                <td class="text-right p-0" colspan="2"><b>Total pagos</b></td>
                <td class="text-right p-0"><b>$<?php echo number_format($total_pagos, 0, '.', '.') ?></b></td>
                <td colspan="3"></td>
              </tr>
              <?php
              if ($estado != 'PAGADO') {
              ?>
                <tr>
                  <td class="text-center p-1 text-muted"><?php echo $num_item ?></td>
                  <td class="text-center">
                    <select class="form-control form-control-sm" id="input_metodo_pago" name="input_metodo_pago">
                      <option value="">Seleccione uno...</option>
                      <option value="Efectivo">Efectivo</option>
                      <option value="Nequi">Nequi</option>
                      <option value="Bancolombia">Bancolombia</option>
                      <option value="Daviplata">Daviplata</option>
                    </select>
                  </td>
                  <td class="text-center">
                    <input type="text" class="form-control form-control-sm moneda" id="input_valor_pago_compra" name="input_valor_pago_compra" placeholder="Valor" autocomplete="off">
                  </td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-success btn-round p-0 px-1" id="btn_agregar_pago_compra">+</button>
                  </td>
                  <td></td>
                </tr>
              <?php
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="row m-0 my-2" id="div_tabla_productos">
      <div class="border-top text-center px-2">
        <h4>Lista de productos</h4>
        <table class="table text-dark table-sm w-100 mb-0" id="tabla_inventario_<?php echo $codigo ?>">
          <thead>
            <tr class="text-center bg-300 text-dark">
              <th width="10px" class="p-0">#</th>
              <th class="p-0">Producto</th>
              <th width="120px" class="p-0">Valor Público</th>
              <th width="80px" class="p-0">Costo</th>
              <th width="120px" class="p-0">Cantidad</th>
              <th width="120px" class="p-0">Costo Total</th>
            </tr>
          </thead>
          <tbody class="overflow-auto text-dark">
            <?php
            $total_productos = 0;
            $num_item = 1;
            foreach ($productos_compra as $i => $item) {
              $cod_producto = $item['codigo'];
              $descripcion = $item['descripcion'];

              $costo_total = 0;

              foreach ($busqueda as $i => $palabra)
                $descripcion = ucwords(str_ireplace($palabra, '??//' . ucwords($palabra) . '))//', $descripcion));

              $descripcion = ucwords(str_ireplace('??//', '<mark>', $descripcion));
              $descripcion = ucwords(str_ireplace('))//', '</mark>', $descripcion));

              $categoria = $item['categoria'];
              $cant = $item['cant'];

              $valor_venta = $item['valor_venta'];
              $costo = $item['costo'];
              if ($costo > 0)
                $costo_total = $cant * $costo;

              $total_productos += $costo_total;
            ?>
              <tr class="text-dark">
                <td class="p-1"><?php echo $num_item ?></td>
                <td class="p-1"><?php echo $descripcion ?></td>
                <td class="p-1 text-right"><?php echo '$' . number_format($valor_venta, 0, '.', '.'); ?></td>
                <td class="p-1 text-right"><?php echo '$' . number_format($costo, 0, '.', '.'); ?></td>
                <td class="p-1"><b><?php echo $cant ?></b></td>
                <td class="p-1 text-right"><b><?php echo '$' . number_format($costo_total, 0, '.', '.'); ?></b></td>
              </tr>
            <?php
              $num_item++;
            }
            ?>
            <tr class="text-dark">
              <td class="p-1" colspan="4"></td>
              <td class="p-1 text-right bg-info border-info border-2"><b>Total Compra</b></td>
              <td class="p-1 text-right border-info border-2"><b><?php echo '$' . number_format($total_productos, 0, '.', '.'); ?></b></td>
              <td></td>
            </tr>
            <tr class="text-center bg-300 text-dark">
              <th width="10px" class="p-0">#</th>
              <th class="p-0" colspan="2">Descripción</th>
              <th width="200px" class="p-0">Creador/Fecha</th>
              <th width="150px" class="p-0">Tipo</th>
              <th width="120px" class="p-0">Valor</th>
              <th width="50px" class="p-0"></th>
            </tr>
            <?php
            $num_item = 1;
            foreach ($notas as $i => $nota) {
              $tipo = $nota['tipo'];
              $valor = $nota['valor'];
              $fecha_creacion = date('d-m-Y h:i A', strtotime($nota['fecha_creacion']));
              $creador = $nota['creador'];
              $observacion = $nota['observacion'];
              $local = $nota['local'];

              $sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
              $result_e = mysqli_query($conexion, $sql_e);
              $ver_e = mysqli_fetch_row($result_e);
              if ($ver_e != null) {
                $nombre_aux = explode(' ', $ver_e[0]);
                $apellido_aux = explode(' ', $ver_e[1]);
                $creador = $nombre_aux[0];  //.' '.$apellido_aux[0];
              }

              if ($tipo == 'Nota Crédito') {
                $text_nota = 'text-danger';
                $valor *= -1;
              }
              if ($tipo == 'Nota Débito')
                $text_nota = 'text-success';

              $total_productos += $valor;
            ?>
              <tr class="text-dark">
                <td class="p-0"><?php echo $num_item ?></td>
                <td class="p-0 text-left" colspan="2"><?php echo $observacion; ?></td>
                <td class="text-center p-0 lh-1">
                  <b><small><?php echo $creador ?></small></b>
                  <br>
                  <small><?php echo $fecha_creacion ?></small>
                </td>
                <td class="p-0 text-right <?php echo $text_nota ?>">
                  <b><?php echo $tipo; ?></b>
                </td>
                <td class="p-0 text-right <?php echo $text_nota ?>"><b><?php echo '$' . number_format($valor, 0, '.', '.'); ?></b></td>
                <td class="text-center p-0">
                  <a href="#" title="Eliminar Nota" class="btn btn-sm btn-danger btn-round p-0 px-1" onclick="$('#cod_compra_eliminar_3').val(<?php echo $cod_compra ?>);$('#item_eliminar_3').val(<?php echo $i ?>);$('#Modal_Eliminar_Nota').modal('show');">
                    <span class="fa fa-times"></span>
                  </a>
                </td>
              </tr>
            <?php
              $num_item++;
            }
            ?>
            <tr class="text-dark">
              <td class="p-1" colspan="4"></td>
              <td class="p-1 text-right bg-success border-success border-2"><b>Total A pagar</b></td>
              <td class="p-1 text-right border-success border-2"><b><?php echo '$' . number_format($total_productos, 0, '.', '.'); ?></b></td>
            </tr>
            <?php
            if ($estado != 'PAGADO') {
            ?>
              <tr>
                <td class="text-center" colspan="2">
                  <select class="form-control form-control-sm" id="input_tipo_nota" name="input_tipo_nota">
                    <option value="">Seleccione Tipo de nota...</option>
                    <option value="Nota Crédito">Nota Crédito</option>
                    <option value="Nota Débito">Nota Débito</option>
                  </select>
                </td>
                <td class="text-center" width="100px">
                  <input type="text" class="form-control form-control-sm moneda" id="input_valor_nota" name="input_valor_nota" placeholder="Valor" autocomplete="off">
                </td>
                <td class="text-left" colspan="3">
                  <input type="text" class="form-control form-control-sm" name="input_observacion" id="input_observacion" placeholder="Descripcion de la nota">
                </td>
                <td class="text-center">
                  <button type="button" class="btn btn-sm btn-outline-success btn-round p-0 px-1" id="btn_agregar_nota_compra">+</button>
                </td>
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
          <input type="number" name="cod_compra_upload" id="cod_compra_upload" hidden="">
          <input type="number" name="item_upload" id="item_upload" hidden="">
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

<!-- Modal Subir factura-->
<div class="modal fade" id="Modal_Subir_Factura" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="overflow-y: scroll;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header text-center">
        <h3 class="modal-title">Cargar factura de compra</h3>
      </div>

      <div class="modal-body">
        <form id="frm_factura" enctype="multipart/form-data">
          <input type="number" name="cod_compra_upload_2" id="cod_compra_upload_2" hidden="">
          <div class="row">
            <div class="col">
              <div class="custom-file">
                <label class="form-label" for="archivo_factura">Seleccione un archivo (png, jpeg, jpg)</label>
                <input class="form-control form-control-sm" name="archivo_factura" id="archivo_factura" type="file" />
              </div>
              <div class="progress progress-sm mb-3">
                <div id="progress_bar_upload_2" class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
            <div class="col-auto my-auto">
              <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_subir_2">Subir</button>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-body">
        <button type="button" class="btn btn-sm btn-secondary btn-round" id="btn_cancelar_subir" onclick="$('#Modal_Subir_Factura').modal('toggle');">Cancelar</button>
      </div>

    </div>
  </div>
</div>

<!-- Modal Eliminar Pago-->
<div class="modal fade" id="Modal_Eliminar_Pago" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header text-center p-2">
        <h5 class="modal-title">Seguro desea eliminar este Pago?</h5>
      </div>
      <div class="modal-body">
        <input type="number" name="cod_compra_eliminar" id="cod_compra_eliminar" hidden="">
        <input type="number" name="item_eliminar" id="item_eliminar" hidden="">
        <div class="row">
          <div class="col text-center">
            <button type="button" class="btn btn-sm btn-secondary btn-round btn-block px-5" data-bs-dismiss="modal" id="close_Modal_Eliminar_Pago">NO</button>
          </div>
          <div class="col text-center">
            <button type="button" class="btn btn-sm btn-outline-primary btn-round btn-block" id="btnEliminarPago">SI, Eliminar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Eliminar Nota-->
<div class="modal fade" id="Modal_Eliminar_Nota" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header text-center p-2">
        <h5 class="modal-title">Seguro desea eliminar este Nota?</h5>
      </div>
      <div class="modal-body">
        <input type="number" name="cod_compra_eliminar_3" id="cod_compra_eliminar_3" hidden="">
        <input type="number" name="item_eliminar_3" id="item_eliminar_3" hidden="">
        <div class="row">
          <div class="col text-center">
            <button type="button" class="btn btn-sm btn-secondary btn-round btn-block px-5" data-bs-dismiss="modal" id="close_Modal_Eliminar_Nota">NO</button>
          </div>
          <div class="col text-center">
            <button type="button" class="btn btn-sm btn-outline-primary btn-round btn-block" id="btnEliminarNota">SI, Eliminar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Eliminar Factura-->
<div class="modal fade" id="Modal_Eliminar_Factura" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header text-center p-2">
        <h5 class="modal-title">Seguro desea eliminar esta Factura?</h5>
      </div>
      <div class="modal-body">
        <input type="number" name="cod_compra_eliminar_2" id="cod_compra_eliminar_2" hidden="">
        <div class="row">
          <div class="col text-center">
            <button type="button" class="btn btn-sm btn-secondary btn-round btn-block px-5" data-bs-dismiss="modal" id="close_Modal_Eliminar_Factura">NO</button>
          </div>
          <div class="col text-center">
            <button type="button" class="btn btn-sm btn-outline-primary btn-round btn-block" id="btnEliminarFactura">SI, Eliminar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $('input.moneda').keyup(function(event) {
    if (event.which >= 37 && event.which <= 40) {
      event.preventDefault();
    }
    $(this).val(function(index, value) {
      return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
    });
  });

  var barra_estado = document.getElementById('progress_bar_upload');

  $('#btn_subir').click(function() {
    document.getElementById("btn_subir").disabled = true;
    barra_estado.classList.remove('bg-success');
    barra_estado.classList.add('bg-info');

    var datos = new FormData($("#frm_soporte")[0]);

    var peticion = new XMLHttpRequest();

    peticion.upload.addEventListener("progress", barra_progreso, false);
    peticion.addEventListener("load", proceso_completo, false);
    peticion.addEventListener("error", error_carga, false);
    peticion.addEventListener("abort", carga_abortada, false);

    peticion.open("POST", "procesos/subir_soporte.php");
    peticion.send(datos);
  });

  function barra_progreso(event) {
    barra_estado.style.width = '0';
    porcentaje = Math.round((event.loaded / event.total) * 100);
    barra_estado.style.width = porcentaje + '%';
  }

  function proceso_completo(event) {
    datos = jQuery.parseJSON(event.target.responseText);
    if (datos['consulta'] == 1) {
      $('#frm_soporte')[0].reset();
      barra_estado.classList.remove('bg-info');
      barra_estado.classList.add('bg-success');

      document.getElementById("btn_subir").disabled = false;
      w_alert({
        titulo: 'Soporte Subido Correctamente',
        tipo: 'success'
      });
      document.getElementById('div_loader').style.display = 'block';
      $('#div_ver_compra').load('paginas/detalles/detalles_compra.php/?cod_compra=<?php echo $cod_compra ?>', function() {
        cerrar_loader();
      });
      $("#Modal_Subir_Soporte").modal('toggle');
      $('.modal-backdrop').remove();
    } else {
      w_alert({
        titulo: datos['consulta'],
        tipo: 'danger'
      });
      document.getElementById("btn_subir").disabled = false;
    }
  }

  function error_carga(event) {
    w_alert({
      titulo: 'Error al cargar el soporte',
      tipo: 'danger'
    });
    document.getElementById("btn_subir").disabled = false;
  }

  function carga_abortada(event) {
    w_alert({
      titulo: 'Carga de soporte cancelada',
      tipo: 'danger'
    });
    document.getElementById("btn_subir").disabled = false;
  }


  var barra_estado_2 = document.getElementById('progress_bar_upload_2');

  $('#btn_subir_2').click(function() {
    document.getElementById("btn_subir_2").disabled = true;
    barra_estado_2.classList.remove('bg-success');
    barra_estado_2.classList.add('bg-info');

    var datos = new FormData($("#frm_factura")[0]);

    var peticion = new XMLHttpRequest();

    peticion.upload.addEventListener("progress", barra_progreso_2, false);
    peticion.addEventListener("load", proceso_completo_2, false);
    peticion.addEventListener("error", error_carga_2, false);
    peticion.addEventListener("abort", carga_abortada_2, false);

    peticion.open("POST", "procesos/subir_factura_compra.php");
    peticion.send(datos);
  });

  function barra_progreso_2(event) {
    barra_estado_2.style.width = '0';
    porcentaje = Math.round((event.loaded / event.total) * 100);
    barra_estado_2.style.width = porcentaje + '%';
  }

  function proceso_completo_2(event) {
    datos = jQuery.parseJSON(event.target.responseText);
    if (datos['consulta'] == 1) {
      $('#frm_factura')[0].reset();
      barra_estado_2.classList.remove('bg-info');
      barra_estado_2.classList.add('bg-success');

      document.getElementById("btn_subir_2").disabled = false;
      w_alert({
        titulo: 'Factura Subida Correctamente',
        tipo: 'success'
      });
      document.getElementById('div_loader').style.display = 'block';
      $('#div_ver_compra').load('paginas/detalles/detalles_compra.php/?cod_compra=<?php echo $cod_compra ?>', function() {
        cerrar_loader();
      });
      $("#Modal_Subir_Factura").modal('toggle');
      $('.modal-backdrop').remove();
    } else {
      w_alert({
        titulo: datos['consulta'],
        tipo: 'danger'
      });
      document.getElementById("btn_subir_2").disabled = false;
    }
  }

  function error_carga_2(event) {
    w_alert({
      titulo: 'Error al cargar la factura',
      tipo: 'danger'
    });
    document.getElementById("btn_subir_2").disabled = false;
  }

  function carga_abortada_2(event) {
    w_alert({
      titulo: 'Carga de facrua cancelada',
      tipo: 'danger'
    });
    document.getElementById("btn_subir_2").disabled = false;
  }

  $('#btn_agregar_pago_compra').click(function() {
    document.getElementById('div_loader').style.display = 'block';
    document.getElementById("btn_agregar_pago_compra").disabled = true;
    input_metodo_pago = document.getElementById("input_metodo_pago").value;
    input_valor_pago = document.getElementById("input_valor_pago_compra").value;
    if (input_metodo_pago != '' && input_valor_pago != '') {
      $.ajax({
        type: "POST",
        data: "cod_compra=<?php echo $cod_compra ?>&input_metodo_pago=" + input_metodo_pago + "&input_valor_pago=" + input_valor_pago,
        url: "procesos/agregar_pago_compra.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            w_alert({
              titulo: 'Pago agregado con exito',
              tipo: 'success'
            });
            document.getElementById('div_loader').style.display = 'block';
            $('#div_ver_compra').load('paginas/detalles/detalles_compra.php/?cod_compra=<?php echo $cod_compra ?>', function() {
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
        }
      });
    } else {
      if (input_metodo_pago == '') {
        w_alert({
          titulo: 'Seleccione el metodo de pago',
          tipo: 'danger'
        });
        document.getElementById("input_metodo_pago").focus();
      } else if (input_valor_pago == '') {
        w_alert({
          titulo: 'Ingrese el valor del pago',
          tipo: 'danger'
        });
        document.getElementById("input_valor_pago").focus();
      }
    }

    cerrar_loader();
    document.getElementById("btn_agregar_pago_compra").disabled = false;
  });

  $('#btn_agregar_nota_compra').click(function() {
    document.getElementById('div_loader').style.display = 'block';
    document.getElementById("btn_agregar_nota_compra").disabled = true;
    input_tipo_nota = document.getElementById("input_tipo_nota").value;
    input_valor_nota = document.getElementById("input_valor_nota").value;
    input_observacion = document.getElementById("input_observacion").value;
    if (input_tipo_nota != '' && input_valor_nota != '' && input_observacion != '') {
      $.ajax({
        type: "POST",
        data: "cod_compra=<?php echo $cod_compra ?>&input_tipo_nota=" + input_tipo_nota + "&input_valor_nota=" + input_valor_nota + "&input_observacion=" + input_observacion,
        url: "procesos/agregar_nota_compra.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            w_alert({
              titulo: 'Nota agregado con exito',
              tipo: 'success'
            });
            document.getElementById('div_loader').style.display = 'block';
            $('#div_ver_compra').load('paginas/detalles/detalles_compra.php/?cod_compra=<?php echo $cod_compra ?>', function() {
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
        }
      });
    } else {
      if (input_tipo_nota == '') {
        w_alert({
          titulo: 'Seleccione el tipo de nota',
          tipo: 'danger'
        });
        document.getElementById("input_tipo_nota").focus();
      } else if (input_valor_nota == '') {
        w_alert({
          titulo: 'Ingrese el valor de la nota',
          tipo: 'danger'
        });
        document.getElementById("input_valor_nota").focus();
      } else if (input_observacion == '') {
        w_alert({
          titulo: 'Ingrese la observacion de la nota',
          tipo: 'danger'
        });
        document.getElementById("input_observacion").focus();
      }
    }

    cerrar_loader();
    document.getElementById("btn_agregar_nota_compra").disabled = false;
  });

  $('#btnEliminarPago').click(function() {
    document.getElementById('div_loader').style.display = 'block';
    cod_compra = document.getElementById("cod_compra_eliminar").value;
    item = document.getElementById("item_eliminar").value;
    $.ajax({
      type: "POST",
      data: "cod_compra=" + cod_compra + "&item=" + item,
      url: "procesos/eliminar_pago_compra.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          w_alert({
            titulo: 'Pago eliminado con exito',
            tipo: 'success'
          });
          $('#div_ver_compra').load('paginas/detalles/detalles_compra.php/?cod_compra=<?php echo $cod_compra ?>', function() {
            cerrar_loader();
          });
          $('#close_Modal_Eliminar_Pago').click();
        } else {
          w_alert({
            titulo: datos['consulta'],
            tipo: 'danger'
          });
          if (datos['consulta'] == 'Reload') {
            document.getElementById('div_login').style.display = 'block';
            cerrar_loader();
          }
          cerrar_loader();
        }
      }
    });
    cerrar_loader();
  });

  $('#btnEliminarFactura').click(function() {
    document.getElementById('div_loader').style.display = 'block';
    cod_compra = document.getElementById("cod_compra_eliminar_2").value;
    $.ajax({
      type: "POST",
      data: "cod_compra=" + cod_compra,
      url: "procesos/eliminar_factura_compra.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          w_alert({
            titulo: 'Factura eliminada con exito',
            tipo: 'success'
          });
          $('#div_ver_compra').load('paginas/detalles/detalles_compra.php/?cod_compra=<?php echo $cod_compra ?>', function() {
            cerrar_loader();
          });
          $('#close_Modal_Eliminar_Factura').click();
        } else {
          w_alert({
            titulo: datos['consulta'],
            tipo: 'danger'
          });
          if (datos['consulta'] == 'Reload') {
            document.getElementById('div_login').style.display = 'block';
            cerrar_loader();
          }
          cerrar_loader();
        }
      }
    });
    cerrar_loader();
  });

  $('#btnEliminarNota').click(function() {
    document.getElementById('div_loader').style.display = 'block';
    cod_compra = document.getElementById("cod_compra_eliminar_3").value;
    item = document.getElementById("item_eliminar_3").value;
    $.ajax({
      type: "POST",
      data: "cod_compra=" + cod_compra + "&item=" + item,
      url: "procesos/eliminar_nota_compra.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          w_alert({
            titulo: 'Nota eliminada con exito',
            tipo: 'success'
          });
          $('#div_ver_compra').load('paginas/detalles/detalles_compra.php/?cod_compra=<?php echo $cod_compra ?>', function() {
            cerrar_loader();
          });
          $('#close_Modal_Eliminar_Nota').click();
        } else {
          w_alert({
            titulo: datos['consulta'],
            tipo: 'danger'
          });
          if (datos['consulta'] == 'Reload') {
            document.getElementById('div_login').style.display = 'block';
            cerrar_loader();
          }
          cerrar_loader();
        }
      }
    });
    cerrar_loader();
  });

  $('#btn_guardar_obs').click(function() {
    document.getElementById('div_loader').style.display = 'block';
    input_observacion = document.getElementById("input_observacion_edit").value;
    $.ajax({
      type: "POST",
      data: "cod_compra=<?php echo $cod_compra ?>&input_observacion=" + input_observacion,
      url: "procesos/editar_obs_compra.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          w_alert({
            titulo: 'Observacion editada con exito',
            tipo: 'success'
          });
          $('#div_ver_compra').load('paginas/detalles/detalles_compra.php/?cod_compra=<?php echo $cod_compra ?>', function() {
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
          cerrar_loader();
        }
      }
    });
    cerrar_loader();
  });
</script>