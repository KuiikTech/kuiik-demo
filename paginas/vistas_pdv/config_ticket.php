<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$ticket = array();

$sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Ticket'";
$result = mysqli_query($conexion, $sql);
$ver = mysqli_fetch_row($result);

if ($ver != null) {
    $ticket = preg_replace("/[\r\n|\n|\r]+/", " ", $ver[2]);
    $ticket = str_replace('  ', ' ', $ticket);
    $ticket = json_decode($ticket, true);
}

?>

<div class="card-body m-0 p-1">
    <div class="d-sm-flex align-items-center mb-1 p-2">
        <h4 class="card-title text-center">Configuracion de Ticket</h4>
    </div>
    <div class="row m-0 p-1 text-center d-flex justify-content-center align-items-center">
        <h6 class="m-0">Subir logo</h6>
        <hr class="m-0 mb-2">
        <div class="position-relative border border-4 border-primary p-1 rounded-3 w-50">
            <img class="w-100" src="recursos/logo_empresa.png" alt="..." id="logo_ticket">
            <div class="position-absolute top-100 start-50 translate-middle bg-white">
                <button class="btn btn-sm btn-outline-warning btn-round" onclick="$('#Modal_Subir_Logo_Ticket').modal('show');">Cambiar</button>
            </div>
        </div>
    </div>
    <hr>
    <?php
    foreach ($ticket as $key => $value) {
        $check = '';
        if ($value['estado'] != 'DIVISION') {
            if ($value['estado'] == 'true')
                $check = 'checked=""';
    ?>
            <div class="row m-0 p-0">
                <div class="col-md-7 text-right"><?php echo $value['etiqueta'] ?></div>
                <div class="col-md-5">
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" id="check_ticket_<?php echo $key ?>" type="checkbox" <?php echo $check ?> onchange="cambiar_estado_ticket('<?php echo $key ?>',this)" />
                    </div>
                </div>
            </div>
        <?php
        } else {
        ?>
            <div class="row m-0 p-1 text-center">
                <h6 class="m-0"><?php echo $value['etiqueta'] ?></h6>
                <hr class="m-0">
            </div>
    <?php
        }
    }
    ?>
    <hr class="m-0">
    <div class="row m-0 p-0">
        <div class="col-md-3 text-right">Mensajes:</div>
        <div class="col-md-9" id="mensajes_ticket"></div>
    </div>
    <div class="row m-0 p-0">
        <div class="col-md-3 text-right"></div>
        <div class="col-md-9">
            <div class="row m-0 p-1">
                <input type="text" class="form-control form-control-sm col" name="input_mensaje" id="input_mensaje" placeholder="" autocomplete="off">
                <button class="btn btn-sm btn-outline-primary btn-round col-auto p-1" id="btn_agregar_mensaje">
                    <span class="fas fa-plus"></span>
                </button>
            </div>
        </div>
    </div>
    <hr>
    <div class="row m-0 p-1">
        <div class="col-md-7">Generar Vista previa</div>
        <div class="col-md-3">
            <button type="button" class="btn btn-sm btn-outline-success btn-round" onclick="generar_preview()">Generar</button>
        </div>
    </div>

</div>

<!-- Modal Subir Logo-->
<div class="modal fade" id="Modal_Subir_Logo_Ticket" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="overflow-y: scroll;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header text-center">
                <h3 class="modal-title">Cargar Logo Ticket</h3>
            </div>
            <div class="modal-body">
                <form id="frm_logo_t" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col">
                            <div class="custom-file">
                                <label class="form-label" for="archivo_logo_t">Seleccione un archivo (png, jpeg, jpg)</label>
                                <input class="form-control form-control-sm" name="archivo_logo_t" id="archivo_logo_t" type="file" accept="image/*" multiple="" />
                            </div>
                            <div class="progress progress-sm mb-3">
                                <div id="progress_bar_upload_t" class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="col-auto my-auto">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btn_subir_t">Subir</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-body">
                <button type="button" class="btn btn-sm btn-secondary btn-round" id="btn_cancelar_subir_t" onclick="$('#Modal_Subir_Logo_Ticket').modal('toggle');">Cancelar</button>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    $('#mensajes_ticket').load('paginas/vistas_pdv/mensajes_ticket.php', function() {
        cerrar_loader();
    });
    document.getElementById('logo_ticket').onerror = imagen_defecto;

    function imagen_defecto(e) {
        e.target.src = 'recursos/logo_empresa.jpg';
    }

    $('#btn_agregar_mensaje').click(function() {
        document.getElementById('div_loader').style.display = 'block';
        mensaje = document.getElementById('input_mensaje').value;
        $.ajax({
            type: "POST",
            data: "mensaje=" + mensaje,
            url: "procesos/agregar_mensaje_ticket.php",
            success: function(r) {
                datos = jQuery.parseJSON(r);
                if (datos['consulta'] == 1) {
                    w_alert({
                        titulo: 'Mensaje agregado Correctamente',
                        tipo: 'success'
                    });
                    document.getElementById('input_mensaje').value = '';
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
    });

    function cambiar_estado_ticket(item, input) {
        document.getElementById('div_loader').style.display = 'block';
        $.ajax({
            type: "POST",
            data: "item=" + item + "&estado=" + input.checked,
            url: "procesos/cambiar_config_ticket.php",
            success: function(r) {
                datos = jQuery.parseJSON(r);
                if (datos['consulta'] == 1) {
                    w_alert({
                        titulo: 'Ticket modificado Correctamente',
                        tipo: 'success'
                    });
                    $('#div_config_1').load('paginas/vistas_pdv/config_ticket.php', function() {
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

    function generar_preview() {
        document.getElementById('div_loader').style.display = 'block';
        $.ajax({
            type: "POST",
            url: "procesos/preview_ticket_pdf.php",
            success: function(r) {
                datos = jQuery.parseJSON(r);
                if (datos['consulta'] == 1) {
                    $('#div_config_2').load('paginas/vistas_pdv/vista_previa_ticket.php', function() {
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

    // Subir Logo Ticket

    var barra_estado = document.getElementById('progress_bar_upload_t');

    $('#btn_subir_t').click(function() {
        document.getElementById("btn_subir_t").disabled = true;
        barra_estado.classList.remove('bg-success');
        barra_estado.classList.add('bg-info');

        var datos = new FormData($("#frm_logo_t")[0]);

        var peticion = new XMLHttpRequest();

        peticion.upload.addEventListener("progress", barra_progreso_t, false);
        peticion.addEventListener("load", proceso_completo_t, false);
        peticion.addEventListener("error", error_carga_t, false);
        peticion.addEventListener("abort", carga_abortada_t, false);

        peticion.open("POST", "procesos/subir_logo_ticket.php");
        peticion.send(datos);
    });

    function barra_progreso_t(event) {
        barra_estado.style.width = '0';
        porcentaje = Math.round((event.loaded / event.total) * 100);
        barra_estado.style.width = porcentaje + '%';
    }

    function proceso_completo_t(event) {
        datos = jQuery.parseJSON(event.target.responseText);
        if (datos['consulta'] == 1) {
            $('#frm_logo_t')[0].reset();
            barra_estado.classList.remove('bg-info');
            barra_estado.classList.add('bg-success');

            document.getElementById("btn_subir_t").disabled = false;
            w_alert({
                titulo: 'Logo cargado Correctamente',
                tipo: 'success'
            });

            $("#Modal_Subir_Logo_Ticket").modal('toggle');
            setTimeout(function() {
                document.getElementById('div_loader').style.display = 'block';
                $('#div_config_1').load('paginas/vistas_pdv/config_ticket.php', function() {
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

            document.getElementById("btn_subir_t").disabled = false;
        }
    }

    function error_carga_t(event) {
        w_alert({
            titulo: 'Error al cargar el soporte',
            tipo: 'danger'
        });
        document.getElementById("btn_subir_t").disabled = false;
    }

    function carga_abortada_t(event) {
        w_alert({
            titulo: 'Carga de soporte cancelada',
            tipo: 'danger'
        });
        document.getElementById("btn_subir_t").disabled = false;
    }
</script>