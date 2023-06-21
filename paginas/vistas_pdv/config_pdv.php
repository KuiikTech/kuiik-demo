<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$fact_credito_si = '';
$fact_credito_no = '';
$impresion_auto = '';
$impresion_manual = '';
$impresion_no = '';
$cocina_auto = '';
$cocina_manual = '';
$bar_auto = '';
$bar_manual = '';
$horno_auto = '';
$horno_manual = '';
$horno2_auto = '';
$horno2_manual = '';
$acceso_creador = '';
$acceso_todos = '';
$acceso_creador_ver = '';
$especiales_si = '';
$especiales_no = '';
$whatsapp = '';
$whatsapp_si = '';
$whatsapp_no = '';
$validar_stock_si = '';
$validar_stock_no = '';
$sistema_pedidos = '';
$sistema_buffet = '';
$sistema_ninguno = '';
$vista_caja_global = '';
$vista_caja_individual = '';
$vista_caja_ninguno = '';

$sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Empresa'";
$result = mysqli_query($conexion, $sql);
$ver = mysqli_fetch_row($result);

$empresa = preg_replace("/[\r\n|\n|\r]+/", " ", $ver[2]);
$empresa = str_replace('  ', ' ', $empresa);
$empresa = json_decode($empresa, true);

$sql_creditos = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion='Facturación Créditos'";
$result_creditos = mysqli_query($conexion, $sql_creditos);
$mostrar_creditos = mysqli_fetch_row($result_creditos);

if ($mostrar_creditos[2] == 'Si')
  $fact_credito_si = 'selected';
if ($mostrar_creditos[2] == 'No')
  $fact_credito_no = 'selected';

$sql_cocina = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Control Cocina'";
$result_cocina = mysqli_query($conexion, $sql_cocina);
$mostrar_cocina = mysqli_fetch_row($result_cocina);

if ($mostrar_cocina[2] == 'Automatico')
  $cocina_auto = 'selected';
if ($mostrar_cocina[2] == 'Manual')
  $cocina_manual = 'selected';

$sql_bar = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Control Bar'";
$result_bar = mysqli_query($conexion, $sql_bar);
$mostrar_bar = mysqli_fetch_row($result_bar);

if ($mostrar_bar[2] == 'Automatico')
  $bar_auto = 'selected';
if ($mostrar_bar[2] == 'Manual')
  $bar_manual = 'selected';

$sql_horno = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Control Horno'";
$result_horno = mysqli_query($conexion, $sql_horno);
$mostrar_horno = mysqli_fetch_row($result_horno);

if ($mostrar_horno[2] == 'Automatico')
  $horno_auto = 'selected';
if ($mostrar_horno[2] == 'Manual')
  $horno_manual = 'selected';

$sql_horno2 = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Control Horno2'";
$result_horno2 = mysqli_query($conexion, $sql_horno2);
$mostrar_horno2 = mysqli_fetch_row($result_horno2);

if ($mostrar_horno2[2] == 'Automatico')
  $horno2_auto = 'selected';
if ($mostrar_horno2[2] == 'Manual')
  $horno2_manual = 'selected';

$sql_impresion = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Imprimir Facturas'";
$result_impresion = mysqli_query($conexion, $sql_impresion);
$mostrar_impresion = mysqli_fetch_row($result_impresion);

if ($mostrar_impresion[2] == 'Automática')
  $impresion_auto = 'selected';
if ($mostrar_impresion[2] == 'Manual')
  $impresion_manual = 'selected';
if ($mostrar_impresion[2] == 'Desactivada')
  $impresion_no = 'selected';

$sql_acceso = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Acceso a mesas'";
$result_acceso = mysqli_query($conexion, $sql_acceso);
$mostrar_acceso = mysqli_fetch_row($result_acceso);

if ($mostrar_acceso[2] == 'Todos')
  $acceso_todos = 'selected';
if ($mostrar_acceso[2] == 'Creador')
  $acceso_creador = 'selected';
if ($mostrar_acceso[2] == 'CreadorVer')
  $acceso_creador_ver = 'selected';

$sql_alerta = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Alerta Pedido'";
$result_alerta = mysqli_query($conexion, $sql_alerta);
$mostrar_alerta = mysqli_fetch_row($result_alerta);

$alerta = $mostrar_alerta[2];

$sql_especiales = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Ranking Productos'";
$result_especiales = mysqli_query($conexion, $sql_especiales);
$mostrar_especiales = mysqli_fetch_row($result_especiales);

if ($mostrar_especiales[2] == 'SI')
  $especiales_si = 'selected';
if ($mostrar_especiales[2] == 'NO')
  $especiales_no = 'selected';

$sql_fecha_ranking = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Fecha Ranking Ventas'";
$result_fecha_ranking = mysqli_query($conexion, $sql_fecha_ranking);
$mostrar_fecha_ranking = mysqli_fetch_row($result_fecha_ranking);

$fecha_ranking = date('Y-m-d', strtotime($mostrar_fecha_ranking[2]));

$sql_stock = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Validar Stock'";
$result_stock = mysqli_query($conexion, $sql_stock);
$mostrar_stock = mysqli_fetch_row($result_stock);

if ($mostrar_stock[2] == 'SI')
  $validar_stock_si = 'selected';
if ($mostrar_stock[2] == 'NO')
  $validar_stock_no = 'selected';

$sql_whatsapp = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'WhatsApp'";
$result_whatsapp = mysqli_query($conexion, $sql_whatsapp);
$mostrar_whatsapp = mysqli_fetch_row($result_whatsapp);

if ($mostrar_whatsapp[2] == 'SI') {
  $whatsapp_si = 'selected';
  $whatsapp = 'SI';
}
if ($mostrar_whatsapp[2] == 'NO')
  $whatsapp_no = 'selected';

$sql_whatsapp_id = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Identificador WhatsApp'";
$result_whatsapp_id = mysqli_query($conexion, $sql_whatsapp_id);
$mostrar_whatsapp_id = mysqli_fetch_row($result_whatsapp_id);

$whatsapp_id = $mostrar_whatsapp_id[2];

$sql_whatsapp_token = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Token WhatsApp'";
$result_whatsapp_token = mysqli_query($conexion, $sql_whatsapp_token);
$mostrar_whatsapp_token = mysqli_fetch_row($result_whatsapp_token);

$whatsapp_token = $mostrar_whatsapp_token[2];

$sql_sistema = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Tipo Sistema'";
$result_sistema = mysqli_query($conexion, $sql_sistema);
$mostrar_sistema = mysqli_fetch_row($result_sistema);

if ($mostrar_sistema[2] == 'Pedidos')
  $sistema_pedidos = 'selected';
if ($mostrar_sistema[2] == 'Buffet')
  $sistema_buffet = 'selected';
if ($mostrar_sistema[2] == 'Ninguno')
  $sistema_ninguno = 'selected';

$sql_vista_caja = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Vista Caja'";
$result_vista_caja = mysqli_query($conexion, $sql_vista_caja);
$mostrar_vista_caja = mysqli_fetch_row($result_vista_caja);

if ($mostrar_vista_caja[2] == 'Global')
  $vista_caja_global = 'selected';
if ($mostrar_vista_caja[2] == 'Individual')
  $vista_caja_individual = 'selected';
if ($mostrar_vista_caja[2] == 'Ninguno')
  $vista_caja_ninguno = 'selected';

?>

<div class="row m-0 p-0">
  <div class="card-group">
    <div class="card m-1 p-2" id="div_config_1">
      <div class="card-body m-0 p-1">
        <div class="d-sm-flex align-items-center mb-4 p-2">
          <h4 class="card-title text-center">Configuraciones Generales</h4>
        </div>
        <div class="row m-0 p-1">
          <div class="col-6 px-1 fw-black text-right">Impresión de facturas</div>
          <div class="col-4 px-1">
            <div class="form-group form-group-sm">
              <select class="form-control form-control-sm" id="impresion_tickets" name="impresion_tickets" onchange="cambiar_btn_color('btn_impresion_tickets')">
                <option value="Automática" <?php echo $impresion_auto ?>>Automática</option>
                <option value="Manual" <?php echo $impresion_manual ?>>Manual</option>
                <option value="Desactivada" <?php echo $impresion_no ?>>Desactivada</option>
              </select>
            </div>
          </div>
          <div class="col-2 px-1">
            <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_impresion_tickets" onclick="guardar_config_pdv('Imprimir Facturas',document.getElementById('impresion_tickets').value)" hidden><span class="fa fa-save"></span></button>
          </div>
        </div>
        <div class="row m-0 p-1" hidden>
          <div class="col-6 px-1 fw-black text-right">Facturar créditos</div>
          <div class="col-4 px-1">
            <div class="form-group form-group-sm">
              <select class="form-control form-control-sm" id="facturacion_creditos" name="facturacion_creditos" onchange="cambiar_btn_color('btn_facturacion_creditos')">
                <option value="Si" <?php echo $fact_credito_si ?>>Si</option>
                <option value="No" <?php echo $fact_credito_no ?>>No</option>
              </select>
            </div>
          </div>
          <div class="col-2 px-1">
            <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_facturacion_creditos" onclick="guardar_config_pdv('Facturación Créditos',document.getElementById('facturacion_creditos').value)" hidden><span class="fa fa-save"></span></button>
          </div>
        </div>

        <div class="row m-0 p-1">
          <div class="col-6 px-1 fw-black text-right">Acceso a mesas</div>
          <div class="col-4 px-1">
            <div class="form-group form-group-sm">
              <select class="form-control form-control-sm" id="acceso_mesas" name="acceso_mesas" onchange="cambiar_btn_color('btn_acceso_mesas')">
                <option value="Todos" <?php echo $acceso_todos ?>>Todos (Editar)</option>
                <option value="CreadorVer" <?php echo $acceso_creador_ver ?>>Todos (Ver)</option>
                <option value="Creador" <?php echo $acceso_creador ?>>Creador</option>
              </select>
            </div>
          </div>
          <div class="col-2 px-1">
            <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_acceso_mesas" onclick="guardar_config_pdv('Acceso a mesas',document.getElementById('acceso_mesas').value)" hidden><span class="fa fa-save"></span></button>
          </div>
        </div>

        <div class="row m-0 p-1">
          <div class="col-6 px-1 fw-black text-right">Vista de CAJA (Meseros)</div>
          <div class="col-4 px-1">
            <div class="form-group form-group-sm">
              <select class="form-control form-control-sm" id="vista_caja" name="vista_caja" onchange="cambiar_btn_color('btn_vista_caja')">
                <option value="Global" <?php echo $vista_caja_global ?>>Global</option>
                <option value="Individual" <?php echo $vista_caja_individual ?>>Individual</option>
                <option value="Ninguno" <?php echo $vista_caja_ninguno ?>>Ninguno</option>
              </select>
            </div>
          </div>
          <div class="col-2 px-1">
            <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_vista_caja" onclick="guardar_config_pdv('Vista Caja',document.getElementById('vista_caja').value)" hidden><span class="fa fa-save"></span></button>
          </div>
        </div>


        <div class="row m-0 p-1">
          <div class="col-6 px-1 fw-black text-right">Tiempo Limite Pedidos</div>
          <div class="col-4 px-1">
            <div class="form-group form-group-sm">
              <div class="row m-0 p-1">
                <input type="number" class="form-control form-control-sm col" id="alerta_pedidos" name="alerta_pedidos" value="<?php echo $alerta ?>" min="1" max="120" onchange="cambiar_btn_color('btn_alerta_pedidos')">
                <span class="col-auto m-0 p-0 px-1">Min</span>
              </div>
            </div>
          </div>
          <div class="col-2 px-1">
            <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_alerta_pedidos" onclick="guardar_config_pdv('Alerta pedido',document.getElementById('alerta_pedidos').value)" hidden><span class="fa fa-save"></span></button>
          </div>
        </div>

        <div class="row m-0 p-1">
          <div class="col-6 px-1 fw-black text-right">Ranking Productos</div>
          <div class="col-4 px-1">
            <div class="form-group form-group-sm">
              <select class="form-control form-control-sm" id="ranking_productos" name="ranking_productos" onchange="cambiar_btn_color('btn_ranking_productos')">
                <option value="SI" <?php echo $especiales_si ?>>SI</option>
                <option value="NO" <?php echo $especiales_no ?>>NO</option>
              </select>
            </div>
          </div>
          <div class="col-2 px-1">
            <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_ranking_productos" onclick="guardar_config_pdv('Ranking Productos',document.getElementById('ranking_productos').value)" hidden><span class="fa fa-save"></span></button>
          </div>
        </div>

        <div class="row m-0 p-1">
          <div class="col-6 px-1 fw-black text-right">Fecha Inicio Ranking</div>
          <div class="col-4 px-1">
            <div class="form-group form-group-sm">
              <input type="date" class="form-control form-control-sm" id="fecha_inicio_ranking" name="fecha_inicio_ranking" value="<?php echo $fecha_ranking ?>" onchange="cambiar_btn_color('btn_fecha_ranking')">
            </div>
          </div>
          <div class="col-2 px-1">
            <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_fecha_ranking" onclick="guardar_config_pdv('Fecha Ranking Ventas',document.getElementById('fecha_inicio_ranking').value)" hidden><span class="fa fa-save"></span></button>
          </div>
        </div>

        <div class="row m-0 p-1">
          <div class="col-6 px-1 fw-black text-right">Validar Stock &gt;0 </div>
          <div class="col-4 px-1">
            <div class="form-group form-group-sm">
              <select class="form-control form-control-sm" id="validar_stock" name="validar_stock" onchange="cambiar_btn_color('btn_validar_stock')">
                <option value="SI" <?php echo $validar_stock_si ?>>SI</option>
                <option value="NO" <?php echo $validar_stock_no ?>>NO</option>
              </select>
            </div>
          </div>
          <div class="col-2 px-1">
            <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_validar_stock" onclick="guardar_config_pdv('Validar Stock',document.getElementById('validar_stock').value)" hidden><span class="fa fa-save"></span></button>
          </div>
        </div>
        <hr class="m-0">
        <div class="row m-0 p-1">
          <div class="col-6 px-1 fw-black text-right">WhatsApp API</div>
          <div class="col-4 px-1">
            <div class="form-group form-group-sm text-center">
              <span class="text-center">NO</span>
              <select class="form-control form-control-sm" hidden id="whatsapp" name="whatsapp" onchange="cambiar_btn_color('btn_whatsapp')">
                <option value="SI" <?php echo $whatsapp_si ?>>SI</option>
                <option value="NO" <?php echo $whatsapp_no ?>>NO</option>
              </select>
            </div>
          </div>
          <div class="col-2 px-1">
            <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_whatsapp" onclick="guardar_config_pdv('WhatsApp',document.getElementById('whatsapp').value)" hidden><span class="fa fa-save"></span></button>
          </div>
        </div>
        <?php
        if ($whatsapp == 'SI') {
        ?>
          <div class="row m-0 p-1">
            <div class="col-6 px-1 fw-black text-right">Identificador de número de teléfono</div>
            <div class="col-4 px-1">
              <div class="form-group form-group-sm">
                <input type="text" class="form-control form-control-sm" id="whatsapp_id" name="whatsapp_id" value="<?php echo $whatsapp_id ?>" onchange="cambiar_btn_color('btn_identificador')">
              </div>
            </div>
            <div class="col-2 px-1">
              <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_identificador" onclick="guardar_config_pdv('Identificador WhatsApp',document.getElementById('whatsapp_id').value)" hidden><span class="fa fa-save"></span></button>
            </div>
          </div>

          <div class="row m-0 p-1">
            <div class="col-6 px-1 fw-black text-right">Token</div>
            <div class="col-4 px-1">
              <div class="form-group form-group-sm">
                <input type="text" class="form-control form-control-sm" id="token" name="token" value="<?php echo $whatsapp_token ?>" onchange="cambiar_btn_color('btn_token')">
              </div>
            </div>
            <div class="col-2 px-1">
              <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_token" onclick="guardar_config_pdv('Token WhatsApp',document.getElementById('token').value)" hidden><span class="fa fa-save"></span></button>
            </div>
          </div>
        <?php
        }
        ?>

        <div class="row m-0 p-1 mt-2 text-center">
          <label>Preparación/Despacho de pedidos</label>
          <hr class="m-0">
        </div>


        <div class="row m-0 p-1">
          <div class="col-6 px-1 fw-black text-right" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="right" title="<b class='text-info'>Sistema Pedidos:</b> Genera los pedidos y no permite procesar la venta hasta que todos los pedidos estén en estado <b class='text-success'>DESPACHADO</b> en su respectiva área.<br> <b class='text-info'>Sistema Buffet:</b> Genera el pedido despues de procesar la venta y no cuenta con resticción de estado de pedido.">Tipo Sistema</div>
          <div class="col-4 px-1">
            <div class="form-group form-group-sm">
              <select class="form-control form-control-sm" id="tipo_sistema" name="tipo_sistema" onchange="cambiar_btn_color('btn_tipo_sistema')">
                <option value="Ninguno" <?php echo $sistema_ninguno ?>>Ninguno</option>
                <option value="Pedidos" <?php echo $sistema_pedidos ?>>Pedidos</option>
                <option value="Buffet" <?php echo $sistema_buffet ?>>Buffet</option>
              </select>
            </div>
          </div>
          <div class="col-2 px-1">
            <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_tipo_sistema" onclick="guardar_config_pdv('Tipo Sistema',document.getElementById('tipo_sistema').value)" hidden><span class="fa fa-save"></span></button>
          </div>
        </div>

        <hr class="m-0">
        <div class="row m-0 p-1">
          <div class="col-6 px-1 fw-black text-right">Cocina</div>
          <div class="col-4 px-1">
            <div class="form-group form-group-sm">
              <select class="form-control form-control-sm" id="control_cocina" name="control_cocina" onchange="cambiar_btn_color('btn_control_cocina')">
                <option value="Automatico" <?php echo $cocina_auto ?>>Automático</option>
                <option value="Manual" <?php echo $cocina_manual ?>>Manual</option>
              </select>
            </div>
          </div>
          <div class="col-2 px-1">
            <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_control_cocina" onclick="guardar_config_area('Cocina',document.getElementById('control_cocina').value)" hidden><span class="fa fa-save"></span></button>
          </div>
        </div>

        <div class="row m-0 p-1">
          <div class="col-6 px-1 fw-black text-right">Bar</div>
          <div class="col-4 px-1">
            <div class="form-group form-group-sm">
              <select class="form-control form-control-sm" id="control_bar" name="control_bar" onchange="cambiar_btn_color('btn_control_bar')">
                <option value="Automatico" <?php echo $bar_auto ?>>Automático</option>
                <option value="Manual" <?php echo $bar_manual ?>>Manual</option>
              </select>
            </div>
          </div>
          <div class="col-2 px-1">
            <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_control_bar" onclick="guardar_config_area('Bar',document.getElementById('control_bar').value)" hidden><span class="fa fa-save"></span></button>
          </div>
        </div>

        <div class="row m-0 p-1">
          <div class="col-6 px-1 fw-black text-right">Horno</div>
          <div class="col-4 px-1">
            <div class="form-group form-group-sm">
              <select class="form-control form-control-sm" id="control_horno" name="control_horno" onchange="cambiar_btn_color('btn_control_horno')">
                <option value="Automatico" <?php echo $horno_auto ?>>Automático</option>
                <option value="Manual" <?php echo $horno_manual ?>>Manual</option>
              </select>
            </div>
          </div>
          <div class="col-2 px-1">
            <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_control_horno" onclick="guardar_config_area('Horno',document.getElementById('control_horno').value)" hidden><span class="fa fa-save"></span></button>
          </div>
        </div>

        <div class="row m-0 p-1">
          <div class="col-6 px-1 fw-black text-right">Horno 2</div>
          <div class="col-4 px-1">
            <div class="form-group form-group-sm">
              <select class="form-control form-control-sm" id="control_horno2" name="control_horno2" onchange="cambiar_btn_color('btn_control_horno2')">
                <option value="Automatico" <?php echo $horno2_auto ?>>Automático</option>
                <option value="Manual" <?php echo $horno2_manual ?>>Manual</option>
              </select>
            </div>
          </div>
          <div class="col-2 px-1">
            <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_control_horno2" onclick="guardar_config_area('Horno2',document.getElementById('control_horno2').value)" hidden><span class="fa fa-save"></span></button>
          </div>
        </div>

        <hr class="m-0">
        <div class="row m-0 p-1">
          <div class="col-6">Diseño de ticket</div>
          <div class="col-6">
            <button type="button" class="btn btn-sm btn-outline-info btn-round" onclick="document.getElementById('div_loader').style.display = 'block';$('#div_config_1').load('paginas/vistas_pdv/config_ticket.php', function() {cerrar_loader();});$('#div_config_2').load('paginas/vistas_pdv/vista_previa_ticket.php', function() {cerrar_loader();});">Configurar ticket</button>
          </div>
        </div>
      </div>
    </div>

    <div class="card m-1 p-2" id="div_config_2">
      <div class="card-body m-0 p-1">
        <div class="d-sm-flex align-items-center mb-4 p-2">
          <h4 class="card-title text-center">Empresa</h4>
        </div>
        <div class="row m-0 p-1">
          <div class="col-3 px-1 fw-black">Nombre</div>
          <div class="col-6 px-1">
            <div class="form-group form-group-sm"><input type="text" class="form-control form-control-sm" name="input_nombre_empresa" id="input_nombre_empresa" value="<?php echo $empresa['nombre'] ?>" autocomplete="off" onchange="cambiar_btn_color('btn_nombre_empresa')"></div>
          </div>
          <div class="col-3 px-1"><button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_nombre_empresa" onclick="guargar_info_empresa('nombre',document.getElementById('input_nombre_empresa').value)" hidden><span class="fa fa-save"></span></button></div>
        </div>
        <div class="row m-0 p-1">
          <div class="col-3 px-1 fw-black">NIT</div>
          <div class="col-6 px-1">
            <div class="form-group form-group-sm"><input type="text" class="form-control form-control-sm" name="input_nit_empresa" id="input_nit_empresa" value="<?php echo $empresa['nit'] ?>" autocomplete="off" onchange="cambiar_btn_color('btn_nit_empresa')"></div>
          </div>
          <div class="col-3 px-1"><button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_nit_empresa" onclick="guargar_info_empresa('nit',document.getElementById('input_nit_empresa').value)" hidden><span class="fa fa-save"></span></button></div>
        </div>
        <div class="row m-0 p-1">
          <div class="col-3 px-1 fw-black">Teléfono</div>
          <div class="col-6 px-1">
            <div class="form-group form-group-sm"><input type="text" class="form-control form-control-sm" name="input_telefono_empresa" id="input_telefono_empresa" value="<?php echo $empresa['telefono'] ?>" autocomplete="off" onchange="cambiar_btn_color('btn_telefono_empresa')"></div>
          </div>
          <div class="col-3 px-1"><button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_telefono_empresa" onclick="guargar_info_empresa('telefono',document.getElementById('input_telefono_empresa').value)" hidden><span class="fa fa-save"></span></button></div>
        </div>
        <div class="row m-0 p-1">
          <div class="col-3 px-1 fw-black">Dirección</div>
          <div class="col-6 px-1">
            <div class="form-group form-group-sm"><input type="text" class="form-control form-control-sm" name="input_direccion_empresa" id="input_direccion_empresa" value="<?php echo $empresa['direccion'] ?>" autocomplete="off" onchange="cambiar_btn_color('btn_direccion_empresa')"></div>
          </div>
          <div class="col-3 px-1"><button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_direccion_empresa" onclick="guargar_info_empresa('direccion',document.getElementById('input_direccion_empresa').value)" hidden><span class="fa fa-save"></span></button></div>
        </div>
        <div class="row m-0 p-1">
          <div class="col-3 px-1 fw-black">Ciudad</div>
          <div class="col-6 px-1">
            <div class="form-group form-group-sm"><input type="text" class="form-control form-control-sm" name="input_ciudad_empresa" id="input_ciudad_empresa" value="<?php echo $empresa['ciudad'] ?>" autocomplete="off" onchange="cambiar_btn_color('btn_ciudad_empresa')"></div>
          </div>
          <div class="col-3 px-1"><button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_ciudad_empresa" onclick="guargar_info_empresa('ciudad',document.getElementById('input_ciudad_empresa').value)" hidden><span class="fa fa-save"></span></button></div>
        </div>
        <hr>
        <div class="row g-2 mb-4">
          <div class="col-6">
            <h5 class="text-center">Imagen Cuadrada</h5>
            <div class="position-relative border border-4 border-primary p-1 rounded-3">
              <img class="w-100" src="recursos/logo_restaurante.png" alt="...">
              <div class="position-absolute top-100 start-50 translate-middle bg-white">
                <button class="btn btn-sm btn-outline-warning btn-round" onclick="$('#Modal_Subir_Logo').modal('show');">Cambiar</button>
              </div>
            </div>
          </div>
          <div class="col-6">
            <h5 class="text-center">Imagen Rectangular</h5>
            <div class="position-relative border border-4 border-primary p-1 rounded-3">
              <img class="w-100" src="recursos/logo_restaurante_horiz.png" alt="...">
              <div class="position-absolute top-100 start-50 translate-middle bg-white">
                <button class="btn btn-sm btn-outline-warning btn-round" onclick="$('#Modal_Subir_Logo_H').modal('show');">Cambiar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<br>
<!-- Tabla Configuración de resoluciones -->
<div class="card m-1">
  <div class="card-body">
    <div class="d-sm-flex align-items-center mb-4">
      <h4 class="card-title text-center">Configuración de Resolución</h4>
    </div>
    <table class="table text-dark table-sm Data_Table" id="tabla_resolucion">
      <thead>
        <tr class="text-center">
          <th>cod</th>
          <th><span class="requerido">*</span>Resolución</th>
          <th>Prefijo</th>
          <th>Sufijo</th>
          <th><span class="requerido">*</span>Inicio</th>
          <th><span class="requerido">*</span>Fin</th>
          <th>Actual</th>
          <th><span class="requerido">*</span>Fecha</th>
          <th><span class="requerido">*</span>Vigencia (meses)</th>
          <th></th>
        </tr>
      </thead>
      <tbody class="overflow-auto">
        <?php
        $sql = "SELECT `codigo`, `prefijo`, `sufijo`, `inicio`, `fin`, `actual`, `fecha_resolucion`, `estado`, `numero`, `vigencia`, `fecha_registro` FROM `resoluciones`";
        $result = mysqli_query($conexion, $sql);
        while ($mostrar = mysqli_fetch_row($result)) {
          $codigo = $mostrar[0];
          $prefijo = $mostrar[1];
          $sufijo = $mostrar[2];
          $inicio = $mostrar[3];
          $fin = $mostrar[4];
          $actual = $mostrar[5];
          $fecha_resolucion = $mostrar[6];
          $estado = $mostrar[7];
          $numero = $mostrar[8];
          $vigencia = $mostrar[9];
        ?>
          <tr>
            <td class="text-center"><?php echo str_pad($codigo, 3, "0", STR_PAD_LEFT) ?></td>
            <td class="text-center"><?php echo $numero ?></td>
            <td class="text-center"><?php echo $prefijo ?></td>
            <td class="text-center"><?php echo $sufijo ?></td>
            <td class="text-center"><?php echo $inicio ?></td>
            <td class="text-center"><?php echo $fin ?></td>
            <td class="text-center"><?php echo $actual ?></td>
            <td class="text-center"><?php echo $fecha_resolucion ?></td>
            <td class="text-center"><?php echo $vigencia ?></td>
            <td class="text-center p-1">
              <?php
              if ($estado == 'ACTIVO')
                echo 'ACTIVO';
              else {
                if ($actual != $fin) {
              ?>
                  <button class="btn btn-sm btn-outline-success btn-round px-2" onclick="activar_resolucion('<?php echo $mostrar[0] ?>')">ACTIVAR</button>
              <?php
                } else
                  echo 'TERMINADO';
              }
              ?>

            </td>
          </tr>
        <?php
        }
        ?>

        <tr>
          <td class="text-center"></td>
          <td class="text-center">
            <input type="text" class="form-control form-control-sm" id="num_resolucion" name="num_resolucion">
          </td>
          <td class="text-center">
            <input type="text" class="form-control form-control-sm" id="prefijo" name="prefijo">
          </td>
          <td class="text-center">
            <input type="text" class="form-control form-control-sm" id="sufijo" name="sufijo">
          </td>
          <td class="text-center">
            <input type="number" class="form-control form-control-sm" id="inicio" name="inicio">
          </td>
          <td class="text-center">
            <input type="number" class="form-control form-control-sm" id="fin" name="fin">
          </td>
          <td class="text-center">-</td>
          <td class="text-center">
            <input type="date" class="form-control form-control-sm" id="fecha_resolucion" name="fecha_resolucion">
          </td>
          <td class="text-center">
            <input type="number" class="form-control form-control-sm" id="vigencia" name="vigencia">
          </td>
          <td class="text-center p-1">
            <button class="btn btn-success btn-round p-0 px-1" id="btn_agregar_resolucion">
              <span class="fa fa-plus"></span>
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<br>


<div class="row m-0 p-0">
  <div class="card-group">
    <!-- Tabla Configuración de Mesas -->
    <div class="card m-1 p-2">
      <div class="card-body m-0 p-1">
        <div class="d-sm-flex align-items-center mb-4">
          <h4 class="card-title col">Configuración de mesas</h4>

          <div class="col text-right">
            <button class="btn btn-sm btn-outline-primary ml-auto mb-3 mb-sm-0 btn-round" data-bs-toggle="modal" data-bs-target="#Modal_Nueva_Mesa">
              <i class="icon-plus btn-icon-prepend"></i>Agregar Mesa
            </button>
          </div>
        </div>
        <table class="table text-dark table-sm" id="tabla_mesas" width="100%">
          <thead>
            <tr class="text-center">
              <th>Cod</th>
              <th>
                Nombre
                <br>
                Descripción
              </th>
              <th>Salon</th>
              <th>Estado</th>
              <th>Apertura</th>
              <th></th>
            </tr>
          </thead>
          <tbody class="overflow-auto">
            <?php
            $sql = "SELECT `cod_mesa`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_apertura`, `salon` FROM `mesas` ORDER BY `cod_mesa` ASC";
            $result = mysqli_query($conexion, $sql);
            while ($mostrar = mysqli_fetch_row($result)) {
              $cod_mesa = $mostrar[0];
              $nombre = $mostrar[1];
              $descripcion = $mostrar[2];
              $estado = $mostrar[4];
              $apertura = $mostrar[5];

              $cod_salon = $mostrar[6];
              $salon = 'Sin asignar';

              if ($cod_salon != null) {
                $sql2 = "SELECT `nombre` FROM `salones` WHERE `codigo` = '$cod_salon'";
                $result2 = mysqli_query($conexion, $sql2);
                $mostrar2 = mysqli_fetch_row($result2);
                if ($mostrar2 != null)
                  $salon = $mostrar2[0];
              }
            ?>
              <tr>
                <td class="text-center"><?php echo str_pad($mostrar[0], 3, "0", STR_PAD_LEFT) ?></td>
                <td>
                  <b><?php echo $nombre ?></b>
                  <br>
                  <?php echo $descripcion ?>
                </td>
                <td><?php echo $salon ?></td>
                <td class="text-center"><?php echo $estado ?></td>
                <td class="text-center"><?php echo $apertura ?></td>
                <td class="text-center">
                  <button class="btn btn-outline-warning btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Editar" onclick="actualizar_mesa('<?php echo $mostrar[0] ?>')">
                    <span class="fa fa-edit"></span>
                  </button>
                  <button class="btn btn-outline-danger btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Eliminar" onclick="$('#cod_mesa_delete').val(<?php echo $mostrar[0] ?>);">
                    <span class="fa fa-trash"></span>
                  </button>
                </td>
              </tr>
            <?php
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tabla Configuración de Salones -->
    <div class="card m-1 p-2">
      <div class="card-body m-0 p-1">
        <div class="d-sm-flex align-items-center mb-4">
          <h4 class="card-title col">Configuración de Salones</h4>
          <div class="col text-right">
            <button class="btn btn-sm btn-outline-primary ml-auto mb-3 mb-sm-0 btn-round" data-bs-toggle="modal" data-bs-target="#Modal_Nuevo_Salon">
              <i class="icon-plus btn-icon-prepend"></i>Agregar Salon
            </button>
          </div>
        </div>
        <table class="table text-dark table-sm" id="tabla_salones" width="100%">
          <thead>
            <tr class="text-center">
              <th>#</th>
              <th>Orden</th>
              <th>Nombre</th>
              <th>Color</th>
              <th>Estado</th>
              <th></th>
            </tr>
          </thead>
          <tbody class="overflow-auto">
            <?php
            $sql = "SELECT `codigo`, `nombre`, `estado`, `color` FROM `salones` ORDER BY `orden` ASC";
            $result = mysqli_query($conexion, $sql);
            $num_item = 1;
            while ($mostrar = mysqli_fetch_row($result)) {
              $cod_salon = $mostrar[0];
              $nombre = $mostrar[1];
              $estado = $mostrar[2];
              $color = $mostrar[3];

              if ($color == 'danger') {
                $color = '<span class="badge rounded-pill bg-danger">Rojo</span>';
              }
              if ($color == 'success') {
                $color = '<span class="badge rounded-pill bg-success">Verde</span>';
              }
              if ($color == 'warning') {
                $color = '<span class="badge rounded-pill bg-warning">Amarillo</span>';
              }
              if ($color == 'info') {
                $color = '<span class="badge rounded-pill bg-info">Azul</span>';
              }
              if ($color == 'secondary') {
                $color = '<span class="badge rounded-pill bg-secondary">Gris</span>';
              }
            ?>
              <tr class="align-middle">
                <td class="text-center"><?php echo str_pad($num_item, 3, "0", STR_PAD_LEFT) ?></td>
                <td class="text-center p-1">
                  <button class="btn btn-outline-success btn-round p-0 px-1" onclick="cambiar_orden('Subir','<?php echo $cod_salon ?>')">
                    <span class="fa fa-arrow-up"></span>
                  </button>
                  <br>
                  <button class="btn btn-outline-danger btn-round p-0 px-1" onclick="cambiar_orden('Bajar','<?php echo $cod_salon ?>')">
                    <span class="fa fa-arrow-down"></span>
                  </button>
                </td>
                <td><b><?php echo $nombre ?></b></td>
                <td class="text-center"><?php echo $color ?></td>
                <td class="text-center"><?php echo $estado ?></td>
                <td class="text-center">
                  <button class="btn btn-outline-warning btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Editar_Salon" onclick="actualizar_salon('<?php echo $mostrar[0] ?>')">
                    <span class="fa fa-edit"></span>
                  </button>
                  <button class="btn btn-outline-danger btn-round p-1" data-bs-toggle="modal" data-bs-target="#Modal_Eliminar_Salon" onclick="$('#cod_salon_delete').val(<?php echo $mostrar[0] ?>);">
                    <span class="fa fa-trash"></span>
                  </button>
                </td>
              </tr>
            <?php
              $num_item++;
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal Nueva mesa-->
<div class="modal fade" id="Modal_Nueva_Mesa" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h5 class="modal-title">Agregar Nueva Mesa</h5>
      </div>
      <div class="modal-body">
        <form id="frmnuevo" autocomplete="off">
          <div class="form-group form-group-sm">
            <div class="form-line">
              <label>Nombre:</label>
              <input type="text" class="form-control" id="nombre_mesa" name="nombre_mesa">
            </div>
          </div>
          <div class="form-group form-group-sm">
            <div class="form-line">
              <label>Descripción:</label>
              <input type="text" class="form-control" id="descripcion_mesa" name="descripcion_mesa" placeholder="Opcional">
            </div>
          </div>
          <div class="form-group form-group-sm">
            <div class="form-line">
              <label>Salon:</label>
              <select class="form-control" name="cod_salon_mesa" id="cod_salon_mesa">
                <option value="">Ningún Salon</option>
                <?php
                $sql = "SELECT `codigo`, `nombre` FROM `salones`";
                $result = mysqli_query($conexion, $sql);
                while ($mostrar = mysqli_fetch_row($result)) {
                  $cod_salon = $mostrar[0];
                  $nombre = $mostrar[1];
                ?>
                  <option value="<?php echo $cod_salon ?>"><?php echo $nombre ?></option>
                <?php
                }
                ?>
              </select>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-round " data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-outline-primary btn-round" id="btnAgregar">Agregar Mesa</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Nuevo salon-->
<div class="modal fade" id="Modal_Nuevo_Salon" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h5 class="modal-title">Agregar Nuevo Salon</h5>
      </div>
      <div class="modal-body">
        <form id="frmnuevoS" autocomplete="off">
          <div class="form-group form-group-sm">
            <div class="form-line">
              <label>Nombre:</label>
              <input type="text" class="form-control" id="nombre_salon" name="nombre_salon">
            </div>
          </div>
          <div class="form-group form-group-sm">
            <div class="form-line">
              <label>Color:</label>
              <select class="form-control" name="color_salon" id="color_salon">
                <option value="">Ninguno</option>
                <option class="text-danger" value="danger">Rojo</option>
                <option class="text-warning" value="warning">Amarillo</option>
                <option class="text-success" value="success">Verde</option>
                <option class="text-info" value="info">Azul</option>
                <option class="text-secondary" value="secondary">Gris</option>
              </select>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-round " data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-outline-primary btn-round" id="btnAgregarSalon">Agregar Salon</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Editar mesa-->
<div class="modal fade" id="Modal_Editar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h5 class="modal-title">Editar Mesa</h5>
      </div>
      <div class="modal-body">
        <form id="frmnuevo_U" autocomplete="off">
          <input type="text" name="cod_mesa_U" id="cod_mesa_U" hidden="">
          <div class="form-group form-group-sm">
            <div class="form-line">
              <label>Nombre:</label>
              <input type="text" class="form-control" id="nombre_mesa_U" name="nombre_mesa_U">
            </div>
          </div>
          <div class="form-group form-group-sm">
            <div class="form-line">
              <label>Descripción:</label>
              <input type="text" class="form-control" id="descripcion_mesa_U" name="descripcion_mesa_U" placeholder="Opcional">
            </div>
          </div>
          <div class="form-group form-group-sm">
            <div class="form-line">
              <label>Salon:</label>
              <select class="form-control" name="cod_salon_mesa_U" id="cod_salon_mesa_U">
                <option value="">Ningún Salon</option>
                <?php
                $sql = "SELECT `codigo`, `nombre` FROM `salones`";
                $result = mysqli_query($conexion, $sql);
                while ($mostrar = mysqli_fetch_row($result)) {
                  $cod_salon = $mostrar[0];
                  $nombre = $mostrar[1];
                ?>
                  <option value="<?php echo $cod_salon ?>"><?php echo $nombre ?></option>
                <?php
                }
                ?>
              </select>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-round" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-outline-primary btn-round" id="btnEditar">Editar Mesa</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Editar salon-->
<div class="modal fade" id="Modal_Editar_Salon" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h5 class="modal-title">Editar Salon</h5>
      </div>
      <div class="modal-body">
        <form id="frmnuevoS_U" autocomplete="off">
          <input type="text" name="cod_salon_U" id="cod_salon_U" hidden="">
          <div class="form-group form-group-sm">
            <div class="form-line">
              <label>Nombre:</label>
              <input type="text" class="form-control" id="nombre_salon_U" name="nombre_salon_U">
            </div>
          </div>
          <div class="form-group form-group-sm">
            <div class="form-line">
              <label>Color:</label>
              <select class="form-control" name="color_salon_U" id="color_salon_U">
                <option value="">Ninguno</option>
                <option class="text-danger" value="danger">Rojo</option>
                <option class="text-warning" value="warning">Amarillo</option>
                <option class="text-success" value="success">Verde</option>
                <option class="text-info" value="info">Azul</option>
                <option class="text-secondary" value="secondary">Gris</option>
              </select>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-round" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-outline-primary btn-round" id="btnEditarSalon">Editar Salon</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Eliminar salon-->
<div class="modal fade" id="Modal_Eliminar_Salon" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h5 class="modal-title">Seguro desea eliminar este Salon?</h5>
      </div>
      <div class="modal-body">
        <input type="number" name="cod_salon_delete" id="cod_salon_delete" hidden="">
        <div class="row m-0 p-1">
          <div class="col-auto p-1">
            <button type="button" class="btn btn-outline-secondary btn-round px-5" data-bs-dismiss="modal">NO</button>
          </div>
          <div class="col-auto p-1">
            <button type="button" class="btn btn-outline-primary btn-round px-2" id="btnEliminarSalon">SI, Eliminar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Subir Logo-->
<div class="modal fade" id="Modal_Subir_Logo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="overflow-y: scroll;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header text-center">
        <h3 class="modal-title">Cargar Logo</h3>
      </div>

      <div class="modal-body">
        <form id="frm_logo" enctype="multipart/form-data">
          <div class="row">
            <div class="col">
              <div class="custom-file">
                <label class="form-label" for="archivo_logo">Seleccione un archivo (png, jpeg, jpg)</label>
                <input class="form-control form-control-sm" name="archivo_logo" id="archivo_logo" type="file" accept="image/*" multiple="" />
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
        <button type="button" class="btn btn-sm btn-secondary btn-round" id="btn_cancelar_subir" onclick="$('#Modal_Subir_Logo').modal('toggle');">Cancelar</button>
      </div>

    </div>
  </div>
</div>

<!-- Modal Subir Logo Horizontal-->
<div class="modal fade" id="Modal_Subir_Logo_H" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="overflow-y: scroll;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header text-center">
        <h3 class="modal-title">Cargar Logo Horizontal</h3>
      </div>

      <div class="modal-body">
        <form id="frm_logo_h" enctype="multipart/form-data">
          <div class="row">
            <div class="col">
              <div class="custom-file">
                <label class="form-label" for="archivo_logo_h">Seleccione un archivo (png, jpeg, jpg)</label>
                <input class="form-control form-control-sm" name="archivo_logo_h" id="archivo_logo_h" type="file" accept="image/*" multiple="" />
              </div>
              <div class="progress progress-sm mb-3">
                <div id="progress_bar_upload_h" class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
            <div class="col-auto my-auto">
              <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_subir_h">Subir</button>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-body">
        <button type="button" class="btn btn-sm btn-secondary btn-round" id="btn_cancelar_subir_h" onclick="$('#Modal_Subir_Logo_H').modal('toggle');">Cancelar</button>
      </div>

    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $('#tabla_mesas').DataTable();
    $('#tabla_salones').DataTable();
  });

  loadTooltip();

  $('#btnAgregar').click(function() {
    document.getElementById('div_loader').style.display = 'block';
    document.getElementById("btnAgregar").disabled = true;
    datos = $('#frmnuevo').serialize();
    $.ajax({
      type: "POST",
      data: datos,
      url: "procesos/agregar.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          $('#frmnuevo')[0].reset();
          w_alert({
            titulo: 'Mesa Agregada Correctamente',
            tipo: 'success'
          });
          $("#Modal_Nueva_Mesa").modal('toggle');
          $('#div_contenido').load('paginas/vistas_pdv/config_pdv.php', cerrar_loader());
        } else {
          w_alert({
            titulo: datos['consulta'],
            tipo: 'danger'
          });
          //$('#div_contenido').load('paginas/vistas_pdv/config_pdv.php');
          document.getElementById("btnAgregar").disabled = false;
          cerrar_loader();
        }
      }
    });
  });

  $('#btnAgregarSalon').click(function() {
    document.getElementById('div_loader').style.display = 'block';
    document.getElementById("btnAgregar").disabled = true;
    datos = $('#frmnuevoS').serialize();
    $.ajax({
      type: "POST",
      data: datos,
      url: "procesos/agregar.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          $('#frmnuevoS')[0].reset();
          w_alert({
            titulo: 'Salon Agregado Correctamente',
            tipo: 'success'
          });
          $("#Modal_Nuevo_Salon").modal('toggle');
          $('#div_contenido').load('paginas/vistas_pdv/config_pdv.php', cerrar_loader());
        } else {
          w_alert({
            titulo: datos['consulta'],
            tipo: 'danger'
          });
          //$('#div_contenido').load('paginas/vistas_pdv/config_pdv.php');
          document.getElementById("btnAgregarSalon").disabled = false;
          cerrar_loader();
        }
      }
    });
  });

  $('#btnEditar').click(function() {
    document.getElementById('div_loader').style.display = 'block';
    document.getElementById("btnEditar").disabled = true;
    datos = $('#frmnuevo_U').serialize();
    $.ajax({
      type: "POST",
      data: datos,
      url: "procesos/actualizar.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          $('#frmnuevo_U')[0].reset();
          w_alert({
            titulo: 'Mesa Actualizada Correctamente',
            tipo: 'success'
          });
          $('#div_contenido').load('paginas/vistas_pdv/config_pdv.php', cerrar_loader());
          $("#Modal_Editar").modal('toggle');
        } else {
          w_alert({
            titulo: datos['consulta'],
            tipo: 'danger'
          });
          //$('#div_contenido').load('paginas/vistas_pdv/config_pdv.php');
          document.getElementById("btnEditar").disabled = false;
          cerrar_loader();
        }
      }
    });

  });

  $('#btnEditarSalon').click(function() {
    document.getElementById('div_loader').style.display = 'block';
    document.getElementById("btnEditar").disabled = true;
    datos = $('#frmnuevoS_U').serialize();
    $.ajax({
      type: "POST",
      data: datos,
      url: "procesos/actualizar.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          $('#frmnuevoS_U')[0].reset();
          w_alert({
            titulo: 'Salon Actualizado Correctamente',
            tipo: 'success'
          });
          $('#div_contenido').load('paginas/vistas_pdv/config_pdv.php', cerrar_loader());
          $("#Modal_Editar_Salon").modal('toggle');
        } else {
          w_alert({
            titulo: datos['consulta'],
            tipo: 'danger'
          });
          //$('#div_contenido').load('paginas/vistas_pdv/config_pdv.php');
          document.getElementById("btnEditarSalon").disabled = false;
          cerrar_loader();
        }
      }
    });

  });

  $('#btnEliminar').click(function() {
    document.getElementById('div_loader').style.display = 'block';
    cod_mesa = document.getElementById("cod_mesa_delete").value;
    $.ajax({
      type: "POST",
      data: "cod_mesa=" + cod_mesa,
      url: "procesos/eliminar.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          w_alert({
            titulo: 'Mesa Eliminada Correctamente',
            tipo: 'success'
          });
          $('#div_contenido').load('paginas/vistas_pdv/config_pdv.php', cerrar_loader());
          $("#Modal_Eliminar").modal('toggle');
        } else {
          w_alert({
            titulo: datos['consulta'],
            tipo: 'danger'
          });
          cerrar_loader();
        }
      }
    });
  });

  function actualizar_mesa(cod_mesa) {
    document.getElementById("btnEditar").disabled = false;
    $.ajax({
      type: "POST",
      data: "cod_mesa=" + cod_mesa,
      url: "procesos/obtener_datos.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        $('#cod_mesa_U').val(datos['cod_mesa']);
        $('#nombre_mesa_U').val(datos['nombre']);
        $('#descripcion_mesa_U').val(datos['descripcion']);
      }
    });
  }

  function actualizar_salon(cod_salon) {
    document.getElementById("btnEditarSalon").disabled = false;
    $.ajax({
      type: "POST",
      data: "cod_salon=" + cod_salon,
      url: "procesos/obtener_datos.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        $('#cod_salon_U').val(datos['codigo']);
        $('#nombre_salon_U').val(datos['nombre']);
        $('#color_salon_U').val(datos['color']);
      }
    });
  }

  $('#btn_guardar_impresion').click(function() {
    document.getElementById('div_loader').style.display = 'block';
    impresion_tickets = document.getElementById("impresion_tickets").value;
    $.ajax({
      type: "POST",
      data: "impresion_tickets=" + impresion_tickets,
      url: "procesos/cambiar_config_impresion.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          w_alert({
            titulo: 'Impresión cambiada Correctamente',
            tipo: 'success'
          });
          $('#div_contenido').load('paginas/vistas_pdv/config_pdv.php', function() {
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
  });

  $('#btn_guardar_credito').click(function() {
    document.getElementById('div_loader').style.display = 'block';
    facturacion_creditos = document.getElementById("facturacion_creditos").value;
    $.ajax({
      type: "POST",
      data: "facturacion_creditos=" + facturacion_creditos,
      url: "procesos/cambiar_config_creditos.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          w_alert({
            titulo: 'Facturación Créditos cambiada Correctamente',
            tipo: 'success'
          });
          $('#div_contenido').load('paginas/vistas_pdv/config_pdv.php', function() {
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
  });

  function guardar_config_pdv(tipo, valor) {
    document.getElementById('div_loader').style.display = 'block';
    $.ajax({
      type: "POST",
      data: "tipo=" + tipo + "&valor=" + valor,
      url: "procesos/cambiar_config_pdv.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          w_alert({
            titulo: 'Configuración cambiada Correctamente',
            tipo: 'success'
          });
          $('#div_contenido').load('paginas/vistas_pdv/config_pdv.php', function() {
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
  }

  function guardar_config_area(area, valor) {
    document.getElementById('div_loader').style.display = 'block';
    $.ajax({
      type: "POST",
      data: "area=" + area + "&valor=" + valor,
      url: "procesos/cambiar_config_area.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          w_alert({
            titulo: 'Control de ' + area + ' cambiada Correctamente',
            tipo: 'success'
          });
          $('#div_contenido').load('paginas/vistas_pdv/config_pdv.php', function() {
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
  }

  function cambiar_orden(tipo, cod_salon) {
    $.ajax({
      type: "POST",
      data: "cod_salon=" + cod_salon + "&tipo=" + tipo,
      url: "procesos/cambiar_orden_salon.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          $('#div_contenido').load('paginas/vistas_pdv/config_pdv.php', function() {
            cerrar_loader();
          });
        } else {
          w_alert({
            titulo: datos['consulta'],
            tipo: 'danger'
          });
          if (datos['consulta'] == 'Reload') {
            document.getElementById('div_login').style.display = 'block';
            document.getElementById('pc_container').style.display = 'none';
          }
        }
      }
    });
  }

  $('#btn_agregar_resolucion').click(function() {
    document.getElementById('div_loader').style.display = 'block';
    num_resolucion = document.getElementById("num_resolucion").value;
    prefijo = document.getElementById("prefijo").value;
    sufijo = document.getElementById("sufijo").value;
    inicio = document.getElementById("inicio").value;
    fin = document.getElementById("fin").value;
    fecha_resolucion = document.getElementById("fecha_resolucion").value;
    vigencia = document.getElementById("vigencia").value;
    if (num_resolucion != '' && inicio != '' && fin != '' && fecha_resolucion != '' && vigencia != '') {
      $.ajax({
        type: "POST",
        data: "num_resolucion=" + num_resolucion + "&prefijo=" + prefijo + "&sufijo=" + sufijo + "&inicio=" + inicio + "&fin=" + fin + "&fecha_resolucion=" + fecha_resolucion + "&vigencia=" + vigencia,
        url: "procesos/agregar_resolucion.php",
        success: function(r) {
          datos = jQuery.parseJSON(r);
          if (datos['consulta'] == 1) {
            w_alert({
              titulo: 'Resolución agregada Correctamente',
              tipo: 'success'
            });
            $('#div_contenido').load('paginas/vistas_pdv/config_pdv.php', function() {
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
    } else {
      if (num_resolucion == '') {
        w_alert({
          titulo: 'Ingrese el número de la resolución de facturación',
          tipo: 'danger'
        });
        document.getElementById('num_resolucion').focus();
      } else if (inicio == '') {
        w_alert({
          titulo: 'Ingrese el número de inicio de la resolución de facturación',
          tipo: 'danger'
        });
        document.getElementById('inicio').focus();
      } else if (fin == '') {
        w_alert({
          titulo: 'Ingrese el número de finalización de la resolución de facturación',
          tipo: 'danger'
        });
        document.getElementById('fin').focus();
      } else if (fecha_resolucion == '') {
        w_alert({
          titulo: 'Seleccione la fecha de la resolución de facturación',
          tipo: 'danger'
        });
        document.getElementById('fecha_resolucion').focus();
      } else if (vigencia == '') {
        w_alert({
          titulo: 'Ingrese la vigencia de la resolución de facturación.(En meses)',
          tipo: 'danger'
        });
        document.getElementById('vigencia').focus();
      }
    }
    cerrar_loader();
  });

  function activar_resolucion(cod_resolucion) {
    document.getElementById('div_loader').style.display = 'block';
    $.ajax({
      type: "POST",
      data: "cod_resolucion=" + cod_resolucion,
      url: "procesos/activar_resolucion.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          w_alert({
            titulo: 'Resolución activa',
            tipo: 'success'
          });
          $('#div_contenido').load('paginas/vistas_pdv/config_pdv.php', function() {
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
          cerrar_loader()
        }
      }
    });
  }

  function guargar_info_empresa(tipo, valor) {
    document.getElementById('div_loader').style.display = 'block';
    $.ajax({
      type: "POST",
      data: "tipo=" + tipo + "&valor=" + valor,
      url: "procesos/guargar_info_empresa.php",
      success: function(r) {
        datos = jQuery.parseJSON(r);
        if (datos['consulta'] == 1) {
          w_alert({
            titulo: 'Información guardada',
            tipo: 'success'
          });
          $('#div_contenido').load('paginas/vistas_pdv/config_pdv.php', function() {
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
          cerrar_loader()
        }
      }
    });
  }

  function cambiar_btn_color(elemento) {
    document.getElementById(elemento).classList.remove('btn-outline-primary');
    document.getElementById(elemento).classList.add('btn-outline-danger');
    document.getElementById(elemento).hidden = false;
  }

  // Subir Logo

  var barra_estado = document.getElementById('progress_bar_upload');

  $('#btn_subir').click(function() {
    document.getElementById("btn_subir").disabled = true;
    barra_estado.classList.remove('bg-success');
    barra_estado.classList.add('bg-info');

    var datos = new FormData($("#frm_logo")[0]);

    var peticion = new XMLHttpRequest();

    peticion.upload.addEventListener("progress", barra_progreso, false);
    peticion.addEventListener("load", proceso_completo, false);
    peticion.addEventListener("error", error_carga, false);
    peticion.addEventListener("abort", carga_abortada, false);

    peticion.open("POST", "procesos/subir_logo.php");
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
      $('#frm_logo')[0].reset();
      barra_estado.classList.remove('bg-info');
      barra_estado.classList.add('bg-success');

      document.getElementById("btn_subir").disabled = false;
      w_alert({
        titulo: 'Logo cargado Correctamente',
        tipo: 'success'
      });

      $("#Modal_Subir_Logo").modal('toggle');
      setTimeout(function() {
        document.getElementById('div_loader').style.display = 'block';
        $('#div_contenido').load('paginas/vistas_pdv/config_pdv.php', function() {
          cerrar_loader();
        });
      }, 500);
    } else {
      if (datos['consulta'] == 'Reload') {
        document.getElementById('div_login').style.display = 'block';
        cerrar_loader();

      } else
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

  // Subir Logo Horizontal

  var barra_estado = document.getElementById('progress_bar_upload_h');

  $('#btn_subir_h').click(function() {
    document.getElementById("btn_subir_h").disabled = true;
    barra_estado.classList.remove('bg-success');
    barra_estado.classList.add('bg-info');

    var datos = new FormData($("#frm_logo_h")[0]);

    var peticion = new XMLHttpRequest();

    peticion.upload.addEventListener("progress", barra_progreso_h, false);
    peticion.addEventListener("load", proceso_completo_h, false);
    peticion.addEventListener("error", error_carga_h, false);
    peticion.addEventListener("abort", carga_abortada_h, false);

    peticion.open("POST", "procesos/subir_logo_h.php");
    peticion.send(datos);
  });

  function barra_progreso_h(event) {
    barra_estado.style.width = '0';
    porcentaje = Math.round((event.loaded / event.total) * 100);
    barra_estado.style.width = porcentaje + '%';
  }

  function proceso_completo_h(event) {
    datos = jQuery.parseJSON(event.target.responseText);
    if (datos['consulta'] == 1) {
      $('#frm_logo_h')[0].reset();
      barra_estado.classList.remove('bg-info');
      barra_estado.classList.add('bg-success');

      document.getElementById("btn_subir_h").disabled = false;
      w_alert({
        titulo: 'Logo cargado Correctamente',
        tipo: 'success'
      });

      $("#Modal_Subir_Logo_H").modal('toggle');
      setTimeout(function() {
        document.getElementById('div_loader').style.display = 'block';
        $('#div_contenido').load('paginas/vistas_pdv/config_pdv.php', function() {
          cerrar_loader();
        });
      }, 500);
    } else {
      if (datos['consulta'] == 'Reload') {
        document.getElementById('div_login').style.display = 'block';
        cerrar_loader();

      } else
        w_alert({
          titulo: datos['consulta'],
          tipo: 'danger'
        });

      document.getElementById("btn_subir_h").disabled = false;
    }
  }

  function error_carga_h(event) {
    w_alert({
      titulo: 'Error al cargar el soporte',
      tipo: 'danger'
    });
    document.getElementById("btn_subir_h").disabled = false;
  }

  function carga_abortada_h(event) {
    w_alert({
      titulo: 'Carga de soporte cancelada',
      tipo: 'danger'
    });
    document.getElementById("btn_subir_h").disabled = false;
  }
</script>