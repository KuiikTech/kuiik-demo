<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');

require_once "../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

session_set_cookie_params(7 * 24 * 60 * 60);
session_start();

if (isset($_SESSION['usuario_restaurante'])) {
    $usuario = $_SESSION['usuario_restaurante'];
?>

    <div class="card m-2">
        <div class="card-body p-2">
            <hr class="mt-1">
            <div class="row pb-1 px-1 m-0">
                <label class="col-sm-3 col-form-label p-0 text-right pt-1">Busqueda de clientes: </label>
                <div class="col-sm-6">
                    <input type="text" class="form-control form-control-sm col" name="input_busqueda" id="input_busqueda" placeholder="Cédula/NIT - Nombre - Teléfono" autocomplete="off">
                </div>
                <div class="col-sm-3">
                    <button class="btn btn-sm btn-outline-primary btn-round" id="btn_buscar_cliente"><span class="fas fa-search"></span> Buscar Clientes</button>
                </div>
            </div>
        </div>
    </div>
    <div id="div_tabla_clientes"></div>

    <!-- Modal Nuevo cliente-->
    <div class="modal fade" id="Modal_Nuevo_Cliente" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-center p-2">
                    <h5 class="modal-title">Agregar Nuevo Cliente</h5>
                </div>
                <div class="modal-body pb-1">
                    <form id="form_nuevo_cliente" class="px-0">
                        <div class="container px-0">
                            <div class="row pb-1 px-1 m-0">
                                <label class="col-sm-3 col-form-label p-0 text-right"><span class="requerido">*</span>Cédula/NIT: </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" name="input_identificacion_cliente" id="input_identificacion_cliente" autocomplete="off">
                                </div>
                            </div>

                            <div class="row pb-1 px-1 m-0">
                                <label class="col-sm-3 col-form-label p-0 text-right"><span class="requerido">*</span>Nombre: </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" name="input_nombre" id="input_nombre" autocomplete="off">
                                </div>
                            </div>

                            <div class="row pb-1 px-1 m-0">
                                <label class="col-sm-3 col-form-label p-0 text-right"><span class="requerido">*</span>Telefono: </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" name="input_telefono" id="input_telefono" autocomplete="off">
                                </div>
                            </div>

                            <div class="row pb-1 px-1 m-0">
                                <label class="col-sm-3 col-form-label p-0 text-right"><span class="requerido">*</span>Correo: </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" name="input_correo" id="input_correo" autocomplete="off">
                                </div>
                            </div>

                            <div class="row pb-1 px-1 m-0">
                                <label class="col-sm-3 col-form-label p-0 text-right">Dirección: </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" name="input_direccion" id="input_direccion" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </form>
                    <span class="requerido">*</span>Campo Requerido
                </div>
                <div class="modal-footer p-1">
                    <div class="justify-content: flex-end;"></div>
                    <button type="button" class="btn btn-sm btn-secondary btn-round" data-bs-dismiss="modal" id="close_Modal_Nuevo_Cliente">Cerrar</button>
                    <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btnAgregar">Agregar Cliente</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar cliente-->
    <div class="modal fade" id="Modal_Editar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-center p-2">
                    <h5 class="modal-title">Editar Cliente</h5>
                </div>
                <div class="modal-body">
                    <form id="form_cliente_U" autocomplete="off" class="px-0">
                        <input type="text" class="form-control form-control-sm" name="cod_cliente_U" id="cod_cliente_U" hidden="">
                        <div class="container px-0">
                            <div class="row pb-1 px-1 m-0">
                                <label class="col-sm-3 col-form-label p-0 text-right"><span class="requerido">*</span>Cédula/NIT: </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" name="identificacion_cliente_U" id="identificacion_cliente_U">
                                </div>
                            </div>

                            <div class="row pb-1 px-1 m-0">
                                <label class="col-sm-3 col-form-label p-0 text-right"><span class="requerido">*</span>Nombre: </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" name="nombre_cliente_U" id="nombre_cliente_U">
                                </div>
                            </div>

                            <div class="row pb-1 px-1 m-0">
                                <label class="col-sm-3 col-form-label p-0 text-right"><span class="requerido">*</span>Telefono: </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" name="telefono_cliente_U" id="telefono_cliente_U">
                                </div>
                            </div>

                            <div class="row pb-1 px-1 m-0">
                                <label class="col-sm-3 col-form-label p-0 text-right"><span class="requerido">*</span>Correo: </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" name="correo_cliente_U" id="correo_cliente_U">
                                </div>
                            </div>

                            <div class="row pb-1 px-1 m-0">
                                <label class="col-sm-3 col-form-label p-0 text-right">Dirección: </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" name="direccion_cliente_U" id="direccion_cliente_U">
                                </div>
                            </div>
                        </div>
                    </form>
                    <span class="requerido">*</span>Campo Requerido
                </div>
                <div class="modal-footer p-1">
                    <button type="button" class="btn btn-sm btn-secondary btn-round" data-bs-dismiss="modal" id="close_Modal_Editar">Cerrar</button>
                    <button type="button" class="btn btn-sm btn-outline-primary btn-round" id="btnEditar">Editar Cliente</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar cliente-->
    <div class="modal fade" id="Modal_Eliminar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header text-center p-2">
                    <h5 class="modal-title">Seguro desea eliminar este Cliente?</h5>
                </div>
                <div class="modal-body">
                    <input type="number" name="cod_cliente_delete" id="cod_cliente_delete" hidden="">
                    <div class="row">
                        <div class="col text-center">
                            <button type="button" class="btn btn-sm btn-secondary btn-round btn-block px-5" data-bs-dismiss="modal" id="close_Modal_Eliminar">NO</button>
                        </div>
                        <div class="col text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-round btn-block" id="btnEliminar">SI, Eliminar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ver-->
    <div class="modal fade" id="Modal_Ver" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" id="div_detalles_cliente">
            </div>
        </div>
    </div>

    <!-- Modal detalles de venta-->
    <div class="modal fade" id="Modal_venta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content sombra_modal" id="div_modal_venta"></div>
        </div>
    </div>

    <!-- Modal detalles de cuenta-->
    <div class="modal fade" id="Modal_ver_cuenta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content sombra_modal" id="div_modal_cuenta"></div>
        </div>
    </div>



    <script type="text/javascript">
        $(document).ready(function() {
            document.title = 'Clientes Especiales | Restaurante | WitSoft';
            $('.active').removeClass("active")
            document.getElementById('a_clientes_especiales').classList.add("active");

            $('#div_tabla_clientes').load('tablas/clientes_especiales.php', function() {
                cerrar_loader();
            });
        });

        $('#input_busqueda').keypress(function(e) {
            if (e.keyCode == 13)
                $('#btn_buscar_cliente').click();
        });

        $('#btn_buscar_cliente').click(function() {
            document.getElementById('div_loader').style.display = 'block';
            input_busqueda = document.getElementById("input_busqueda").value;
            input_busqueda = input_busqueda.replace(/ /g, "***");
            if (input_busqueda != '' && input_busqueda.length > 2)
                $('#div_tabla_clientes').load('tablas/clientes_especiales.php/?input_buscar=' + input_busqueda, function() {
                    cerrar_loader();
                });
            else {
                w_alert({
                    titulo: 'Ingrese al menos 3 caracteres',
                    tipo: 'danger'
                });
                document.getElementById("input_busqueda").focus();
                cerrar_loader();
            }
        });

        $('#btnAgregar').click(function() {
            document.getElementById('div_loader').style.display = 'block';
            document.getElementById("btnAgregar").disabled = true;
            datos = $("#form_nuevo_cliente").serialize();
            $.ajax({
                type: "POST",
                data: datos,
                url: "procesos/agregar.php",
                success: function(r) {
                    datos = jQuery.parseJSON(r);
                    if (datos['consulta'] == 1) {
                        $('#form_nuevo_cliente')[0].reset();
                        w_alert({
                            titulo: 'Cliente Agregado Correctamente',
                            tipo: 'success'
                        });
                        $("#input_busqueda").val(datos['id']);
                        setTimeout('$("#btn_buscar_cliente").click();', 300);
                        $("#close_Modal_Nuevo_Cliente").click();
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

                    document.getElementById("btnAgregar").disabled = false;
                    cerrar_loader();
                }
            });
        });

        $('#btnEditar').click(function() {
            document.getElementById('div_loader').style.display = 'block';
            document.getElementById("btnEditar").disabled = true;
            datos = $('#form_cliente_U').serialize();
            $.ajax({
                type: "POST",
                data: datos,
                url: "procesos/actualizar.php",
                success: function(r) {
                    datos = jQuery.parseJSON(r);
                    if (datos['consulta'] == 1) {
                        $('#form_cliente_U')[0].reset();
                        w_alert({
                            titulo: 'Cliente Actualizado Correctamente',
                            tipo: 'success'
                        });
                        $("#btn_buscar_cliente").click();
                        $("#close_Modal_Editar").click();
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

                    document.getElementById("btnEditar").disabled = false;
                    cerrar_loader();
                }
            });

        });

        $('#btnEliminar').click(function() {
            document.getElementById('div_loader').style.display = 'block';
            cod_cliente = document.getElementById("cod_cliente_delete").value;
            $.ajax({
                type: "POST",
                data: "cod_cliente=" + cod_cliente,
                url: "procesos/eliminar.php",
                success: function(r) {
                    datos = jQuery.parseJSON(r);
                    if (datos['consulta'] == 1) {
                        w_alert({
                            titulo: 'Cliente Eliminado Correctamente',
                            tipo: 'success'
                        });
                        $("#btn_buscar_cliente").click();
                        $("#close_Modal_Eliminar").click();
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

                    cerrar_loader();
                }
            });
        });

        function actualizar_cliente(cod_cliente) {
            document.getElementById('div_loader').style.display = 'block';
            document.getElementById("btnEditar").disabled = false;
            $.ajax({
                type: "POST",
                data: "cod_cliente=" + cod_cliente,
                url: "procesos/obtener_datos.php",
                success: function(r) {
                    datos = jQuery.parseJSON(r);
                    $('#cod_cliente_U').val(datos['codigo']);
                    $('#identificacion_cliente_U').val(datos['id']);
                    $('#nombre_cliente_U').val(datos['nombre']);
                    $('#correo_cliente_U').val(datos['correo']);
                    $('#telefono_cliente_U').val(datos['telefono']);
                    $('#direccion_cliente_U').val(datos['direccion']);

                    cerrar_loader();
                }
            });

        }

        function cambiar_estado(cod_cliente) {
            $.ajax({
                type: "POST",
                data: "cod_cliente=" + cod_cliente,
                url: "procesos/cambiar_estado.php",
                success: function(r) {
                    datos = jQuery.parseJSON(r);
                    if (datos['consulta'] == 1) {
                        document.getElementById('btn_estado_' + cod_cliente).innerHTML = datos['estado'];
                        document.getElementById('btn_estado_' + cod_cliente).classList.remove("btn-success");
                        document.getElementById('btn_estado_' + cod_cliente).classList.remove("btn-danger");
                        if (datos['estado'] == 'DISPONIBLE')
                            document.getElementById('btn_estado_' + cod_cliente).classList.add("btn-success");
                        else
                            document.getElementById('btn_estado_' + cod_cliente).classList.add("btn-danger");
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
        }
    </script>


<?php
} else {
?>
    <script type="text/javascript">
        window.location = "login.php";
    </script>
<?php
}
?>