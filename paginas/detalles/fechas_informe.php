<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$cod_producto = $_GET['cod_producto'];
?>
<div class="row text-center">
    <h5 class="text-italy">Ingrese un rango de fechas</h5>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group form-group-sm">
            <input type="date" class="form-control" id="fecha_inicial_i" name="fecha_inicial_i" value="<?php echo $fecha ?>">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group form-group-sm">
            <input type="date" class="form-control" id="fecha_final_i" name="fecha_final_i" value="<?php echo $fecha ?>">
        </div>
    </div>
    <div class="col-md-4">
        <button type="button" class="btn btn-outline-primary btn-round p-1" id="btn_filtrar_informe">Buscar</button>
    </div>
</div>

<script type="text/javascript">
    $('#btn_filtrar_informe').click(function() {
        fecha_inicial = document.getElementById("fecha_inicial_i").value;
        fecha_final = document.getElementById("fecha_final_i").value;

        if (fecha_inicial != '') {
            if (fecha_final != '') {
                document.getElementById('div_loader').style.display = 'block';
                $('#contenido_informe').load('paginas/detalles/informe_producto.php/?cod_producto=<?php echo $cod_producto ?>&fecha_inicial=' + fecha_inicial + '&fecha_final=' + fecha_final, function() {
                    cerrar_loader();
                });
            } else
                w_alert({
                    titulo: 'Ingrese la fecha final',
                    tipo: 'danger'
                });
        } else
            w_alert({
                titulo: 'Ingrese la fecha inicial',
                tipo: 'danger'
            });

    });
</script>