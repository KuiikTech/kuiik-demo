<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$cod_producto = $_GET['cod_producto'];

$sql = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode`, `movimientos`, `especial`, `alerta` FROM `productos` WHERE codigo = '$cod_producto'";
$result = mysqli_query($conexion, $sql);
$mostrar = mysqli_fetch_row($result);

$total = 0;

$descripción = $mostrar[1];
$valor = '$' . number_format($mostrar[3], 0, '.', '.');
$inventario = $mostrar[4];
$cod_categoria = $mostrar[5];
$imagen = $mostrar[6];
$fecha_registro = $mostrar[7];
$area = $mostrar[8];
$tipo = $mostrar[9];
$estado = $mostrar[10];
$barcode = $mostrar[11];
$movimientos = $mostrar[12];
$especial = $mostrar[13];
$alerta = $mostrar[14];

if ($tipo == 'Preparación')
    $inventario = '---';

$sql_cat = "SELECT `cod_categoria`, `nombre` FROM `categorias_productos` WHERE cod_categoria='$cod_categoria'";
$result_cat = mysqli_query($conexion, $sql_cat);
$mostrar_cat = mysqli_fetch_row($result_cat);

$categoria = '';
if ($mostrar_cat != NULL)
    $categoria =  $mostrar_cat[1];

if ($especial == 'SI') {
    $especial = 'SI';
    $bg_button = 'btn-success';
} else {
    $especial = 'NO';
    $bg_button = 'btn-danger';
}
?>
<div class="modal-header text-center">
    <h5 class="modal-title">Detalles de producto (N° <?php echo str_pad($cod_producto, 3, "0", STR_PAD_LEFT) ?>)</h5>
    <div class="dropdown">
        <button type="button" class="btn btn-outline-dark btn-round p-1" id="menu_producto" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            <i class="fas fa-bars"></i>
        </button>
        <div class="dropdown-menu dropdown-menu_producto" aria-labelledby="menu_producto">
            <a class="dropdown-item" onclick="$('#fechas_informe').load('paginas/detalles/fechas_informe.php/?cod_producto=<?php echo $cod_producto ?>', function(){cerrar_loader();});">Ver Informe</a>
        </div>
    </div>
</div>
<div class="modal-body pt-2">
    <p class="row mb-0">
        <span class="col-lg-3 text-right"> Descripción: </span>
        <span class="col-lg-9 text-left"><b> <?php echo $descripción ?> </b></span>
    </p>
    <p class="row mb-0">
        <span class="col-lg-3 text-right"> Tipo: </span>
        <span class="col-lg-9 text-left"><b> <?php echo $tipo ?> </b></span>
    </p>
    <p class="row mb-0">
        <span class="col-lg-3 text-right"> Valor: </span>
        <span class="col-lg-9 text-left"><b> <?php echo $valor ?> </b></span>
    </p>
    <p class="row mb-0">
        <span class="col-lg-3 text-right"> Inventario: </span>
        <span class="col-lg-9 text-left"><b id="b_inventario"> <?php echo $inventario ?> </b></span>
    </p>
    <div class="row mb-0">
        <span class="col-lg-3 text-right"> Alerta: </span>
        <span class="col-lg-9 text-left" id="span_alerta"><b id="b_alerta" onclick="$('#div_input_alerta').removeAttr('hidden'); $('#span_alerta').attr('hidden', '');"> <?php echo $alerta ?> </b></span>
        <div class="col-lg-3 text-left" id="div_input_alerta" hidden>
            <input type="number" class="form-control form-control-sm" id="input_alerta" value="<?php echo $alerta ?>" onchange="cambiar_alerta('<?php echo $cod_producto ?>', this.value)">
        </div>
    </div>
    <p class="row mb-0">
        <span class="col-lg-3 text-right"> Categoría: </span>
        <span class="col-lg-9 text-left"><b> <?php echo $categoria ?> </b></span>
    </p>
    <p class="row mb-0">
        <span class="col-lg-3 text-right"> Barcode: </span>
        <span class="col-lg-9 text-left"><b> <?php echo $barcode ?> </b></span>
    </p>
    <p class="row mb-0">
        <span class="col-lg-3 text-right"> Especial: </span>
        <span class="col-lg-9 text-left">
            <button class="btn btn-sm <?php echo $bg_button ?> btn-round px-2" id="btn_especial_<?php echo $cod_producto ?>" onclick="cambiar_especial('<?php echo $cod_producto ?>')">
                <?php echo $especial ?>
            </button>
        </span>
    </p>
    <p class="row mb-0">
        <span class="col-lg-3 text-right"> Estado: </span>
        <span class="col-lg-9 text-left"><b> <?php echo $estado ?> </b></span>
    </p>
    <p class="row mb-0">
        <span class="col-lg-3 text-right"> Fecha de registro: </span>
        <span class="col-lg-9 text-left"><b> <?php echo $fecha_registro ?> </b></span>
    </p>

    <div id="fechas_informe" class="m-0 p-1"></div>
    <div id="contenido_informe" class="m-0 p-1"></div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-round p-1" data-bs-dismiss="modal">Cerrar</button>
</div>

<script type="text/javascript">
    $('.select2').select2();

    document.getElementById('div_loader').style.display = 'block';
    $('#contenido_informe').load('paginas/detalles/informe_producto.php/?cod_producto=<?php echo $cod_producto ?>', function() {
        cerrar_loader();
    });

    function cambiar_especial(cod_producto) {
        $.ajax({
            type: "POST",
            data: "cod_producto=" + cod_producto,
            url: "procesos/cambiar_especial.php",
            success: function(r) {
                datos = jQuery.parseJSON(r);
                if (datos['consulta'] == 1) {
                    document.getElementById('btn_especial_' + cod_producto).innerHTML = datos['especial'];
                    document.getElementById('btn_especial_' + cod_producto).classList.remove("btn-success");
                    document.getElementById('btn_especial_' + cod_producto).classList.remove("btn-danger");
                    if (datos['especial'] == 'SI')
                        document.getElementById('btn_especial_' + cod_producto).classList.add("btn-success");
                    else
                        document.getElementById('btn_especial_' + cod_producto).classList.add("btn-danger");
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
    }

    function cambiar_alerta(cod_producto, alerta) {
        $.ajax({
            type: "POST",
            data: "cod_producto=" + cod_producto + "&alerta=" + alerta,
            url: "procesos/cambiar_alerta.php",
            success: function(r) {
                datos = jQuery.parseJSON(r);
                if (datos['consulta'] == 1) {
                    document.getElementById('b_alerta').innerHTML = datos['alerta'];
                    document.getElementById('div_input_alerta').setAttribute('hidden', '');
                    document.getElementById('span_alerta').removeAttribute('hidden');
                    document.getElementById('input_alerta').value = datos['alerta'];
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
    }
</script>