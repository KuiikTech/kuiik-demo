<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$mensaje = array();

$sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Mensaje Ticket'";
$result = mysqli_query($conexion, $sql);
$ver = mysqli_fetch_row($result);

if ($ver != null) {
    if ($ver[2] != '') {
        $mensaje = preg_replace("/[\r\n|\n|\r]+/", " ", $ver[2]);
        $mensaje = str_replace('  ', ' ', $mensaje);
        $mensaje = json_decode($mensaje, true);
    }
}

foreach ($mensaje as $key => $value) {
    $title = 'Fecha creación: ' . $value['fecha_registro'] . ' | Creador: ' . $value['creador'];
?>
    <div class="row">
        <div class="col">
            <p class="text-center m-0 lh-1" title="<?php echo $title ?>"><?php echo $value['text'] ?></p>
        </div>
        <div class="col-auto">
            <a class="text-danger" href="javascript:eliminar_mensaje_ticket('<?php echo $key ?>')" title="Eliminar mensaje">
                <span class="fa fa-times fs--1"></span>
            </a>
        </div>
    </div>

<?php
}
?>

<script type="text/javascript">
    function eliminar_mensaje_ticket(item) {
        $.ajax({
            type: "POST",
            data: "item=" + item,
            url: "procesos/eliminar_mensaje_ticket.php",
            success: function(r) {
                datos = jQuery.parseJSON(r);
                if (datos['consulta'] == 1) {
                    w_alert({
                        titulo: 'Mensaje eliminado Correctamente',
                        tipo: 'success'
                    });
                    $('#mensajes_ticket').load('paginas/vistas_pdv/mensajes_ticket.php', function() {
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
</script>