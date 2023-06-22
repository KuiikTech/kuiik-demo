<div class="contenedor_pedidos" id="pedidos_pendientes"></div>
<hr>
<div class="row text-center">
    <div class="col">PEDIDOS ENTREGADOS</div>
</div>
<div class="contenedor_pedidos" id="pedidos_terminados"></div>

<script type="text/javascript">
    function cerrar_loader() {
        document.getElementById('div_loader').style.display = 'none';
    }
    //$('#div_lateral').load('reservas.php/?area=Cocina');

    $('input.moneda').keyup(function(event) {
        if (event.which >= 37 && event.which <= 40) {
            event.preventDefault();
        }

        $(this).val(function(index, value) {
            return value
                .replace(/\D/g, "")
                .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
        });
    });
    lista_pedidos('PENDIENTE');
    lista_pedidos('TERMINADO');

    function cambios(area) {
        $.ajax({
            type: "POST",
            data: "area=" + area,
            url: "procesos/cambios.php",
            success: function(r) {
                datos = jQuery.parseJSON(r);
                if (datos['consulta'] == 1) {
                    lista_pedidos('PENDIENTE');
                    lista_reservas('PENDIENTE');
                } else {
                    if (datos['consulta'] == 'Reload') {
                        document.getElementById('div_login').style.display = 'block';
                        cerrar_loader();

                    }
                }
            }
        });
    }


    async function crear_div(codigo, orden, padre) {
        try {
            if (document.getElementById("div_card_" + codigo)) {
                if (document.getElementById("orden_pedido_" + codigo).innerHTML != orden) {
                    document.getElementById("div_card_" + codigo).remove();

                    const div = document.createElement("div");
                    div.id = "div_card_" + codigo;
                    div.className = "card item m-1";
                    div.style = "order: " + orden + ";";
                    padre.appendChild(div);

                    await cargar_div(codigo, orden);
                }
            } else {
                const div = document.createElement("div");
                div.id = "div_card_" + codigo;
                div.className = "card item m-1";
                div.style = "order: " + orden + ";";
                padre.appendChild(div);
                await cargar_div(codigo, orden);
            }
        } catch (error) {
            console.log(error);
        }
    }

    function cargar_div(codigo, orden) {
        $('#div_card_' + codigo).load('paginas/areas/cuadro_pedido.php/?cod_pedido=' + codigo + '&orden=' + orden + '&area=Bar');
    }

    function lista_pedidos(tipo) {
        $.ajax({
            type: "POST",
            data: "tipo=" + tipo + "&area=Bar",
            url: "procesos/obtener_pedidos.php",
            success: function(r) {
                datos = jQuery.parseJSON(r);
                if (datos['consulta'] == 1) {

                    pedidos = datos['pedidos'];
                    cant = datos['cant'];

                    if (tipo == 'PENDIENTE')
                        padre_1 = document.getElementById('pedidos_pendientes');
                    else
                        padre_1 = document.getElementById('pedidos_terminados');

                    for (var i = 1; i < cant; i++) {
                        crear_div(pedidos[i], i, padre_1);
                    }
                } else {
                    if (datos['consulta'] == 'Reload') {
                        document.getElementById('div_login').style.display = 'block';
                        cerrar_loader();
                    } else
                        location.reload(true);
                }
            }
        });
    }

    $('#input_contraseña').keypress(function(e) {
        if (e.keyCode == 13)
            $('#btn_login').click();
    });

    $('#input_cedula').keypress(function(e) {
        if (e.keyCode == 13)
            $('#btn_login').click();
    });

    $('#btn_login').click(function() {
        document.getElementById('div_loader').style.display = 'block';
        datos = $('#form_login').serialize();
        $.ajax({
            type: "POST",
            data: datos,
            url: "procesos/login.php",
            success: function(r) {
                datos = jQuery.parseJSON(r);
                if (datos['consulta'] == 1) {
                    w_alert({
                        titulo: 'Sesión Iniciada - BIENVENIDO: ' + datos['nombre'],
                        tipo: 'success'
                    });
                    document.getElementById('input_contraseña').value = '';
                    document.getElementById('div_login').style.display = 'none';
                    document.getElementById('pc_container').style.display = 'block';
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
        document.getElementById('div_loader').style.display = 'none';
    });

    function preparando_pedido(cod_mesa, cod_pedido, num_item, area, orden) {
        document.getElementById('div_loader').style.display = 'block';
        document.getElementById("pre_" + cod_pedido + "_" + num_item).disabled = true;
        $.ajax({
            type: "POST",
            data: "cod_pedido=" + cod_pedido + "&num_item=" + num_item + "&cod_mesa=" + cod_mesa,
            url: "procesos/preparar_pedido.php",
            success: function(r) {
                datos = jQuery.parseJSON(r);
                if (datos['consulta'] == 1) {
                    w_alert({
                        titulo: 'Preparando pedido',
                        tipo: 'success'
                    });
                    cargar_div(cod_pedido, orden);
                    document.getElementById('div_loader').style.display = 'none';
                } else {
                    w_alert({
                        titulo: datos['consulta'],
                        tipo: 'danger'
                    });
                    if (datos['consulta'] == 'Reload') {
                        document.getElementById('div_login').style.display = 'block';
                        cerrar_loader();

                    }

                    document.getElementById("pre_" + cod_pedido + "_" + num_item).disabled = false;
                    document.getElementById('div_loader').style.display = 'none';
                }
            }
        });
    }

    function pedido_despachado(cod_mesa, cod_pedido, num_item, area, orden) {
        document.getElementById('div_loader').style.display = 'block';
        document.getElementById("des_" + cod_pedido + "_" + num_item).disabled = true;
        $.ajax({
            type: "POST",
            data: "cod_pedido=" + cod_pedido + "&num_item=" + num_item + "&cod_mesa=" + cod_mesa,
            url: "procesos/despachar_pedido.php",
            success: function(r) {
                datos = jQuery.parseJSON(r);
                if (datos['consulta'] == 1) {
                    w_alert({
                        titulo: 'Pedido despachado',
                        tipo: 'success'
                    });
                    document.getElementById('div_loader').style.display = 'none';
                    cargar_div(cod_pedido, orden);
                } else {
                    w_alert({
                        titulo: datos['consulta'],
                        tipo: 'danger'
                    });
                    if (datos['consulta'] == 'Reload') {
                        document.getElementById('div_login').style.display = 'block';
                        cerrar_loader();

                    }
                    document.getElementById("des_" + cod_pedido + "_" + num_item).disabled = false;
                    document.getElementById('div_loader').style.display = 'none';
                }

            }
        });
    }

    function pedido_terminado(cod_pedido, area) {
        document.getElementById('div_loader').style.display = 'block';
        document.getElementById("ter_" + cod_pedido).disabled = true;
        $.ajax({
            type: "POST",
            data: "cod_pedido=" + cod_pedido,
            url: "procesos/terminar_pedido.php",
            success: function(r) {
                datos = jQuery.parseJSON(r);
                if (datos['consulta'] == 1) {
                    w_alert({
                        titulo: 'Pedido terminado',
                        tipo: 'success'
                    });
                    lista_pedidos('PENDIENTE');
                    lista_pedidos('TERMINADO');
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
                document.getElementById("ter_" + cod_pedido).disabled = false;
                document.getElementById('div_loader').style.display = 'none';
            }
        });
    }
</script>