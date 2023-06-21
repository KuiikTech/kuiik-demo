<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

session_start();

$usuario = $_SESSION['usuario_restaurante'];

$sql_e = "SELECT nombre, rol, foto FROM usuarios WHERE codigo = '$usuario'";
$result_e = mysqli_query($conexion, $sql_e);
$ver_e = mysqli_fetch_row($result_e);

$nombre = $ver_e[0];
$rol = $ver_e[1];
$cod_mesa = $_GET['cod_mesa'];

$sql_mesa = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura`, `cod_cliente`, `pagos` FROM `mesas` WHERE cod_mesa = '$cod_mesa'";
$result_mesa = mysqli_query($conexion, $sql_mesa);
$mostrar_mesa = mysqli_fetch_row($result_mesa);

$total_mesa = 0;

$cod_cliente = $mostrar_mesa[5];

$sql_clientes = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro` FROM `clientes` WHERE codigo != 0 order by nombre";
$result_clientes = mysqli_query($conexion, $sql_clientes);

$sql_mesas = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura` FROM `mesas` WHERE cod_mesa != '$cod_mesa'";
$result_mesas = mysqli_query($conexion, $sql_mesas);

$btn_cancelar_venta = 1;

$sql_sistema = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Tipo Sistema'";
$result_sistema = mysqli_query($conexion, $sql_sistema);
$mostrar_sistema = mysqli_fetch_row($result_sistema);

$tipo_sistema = $mostrar_sistema[2];

?>
<input type="number" name="cod_mesa_div" id="cod_mesa_div" hidden="" value="<?php echo $cod_mesa ?>">
<form id="frm_dividir">
    <div class="row m-0 p-1">
        <table class="table text-dark table-sm w-100">
            <thead>
                <tr>
                    <th></th>
                    <th><strong>Producto</strong></th>
                    <th class="text-center"><strong>Valor</strong></th>
                    <th class="text-center"><strong>Cant</strong></th>
                    <th class="text-center" width="75px"><strong>Total</strong></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($mostrar_mesa[2] != '') {
                    $productos_mesa = json_decode($mostrar_mesa[2], true);
                    foreach ($productos_mesa as $i => $producto) {
                        $cod_producto = $producto['codigo'];
                        $cant = $producto['cant'];
                        $descripcion_str = $producto['descripcion'];
                        //$descripcion = str_split($descripcion, 20);
                        $valor_unitario = $producto['valor_unitario'];

                        if (isset($producto['cod_pedido']))
                            $cod_pedido = $producto['cod_pedido'];
                        else
                            $cod_pedido = 0;

                        //$descripcion_str = '';

                        $valor_total = $cant * $valor_unitario;
                        $total_mesa += $valor_total;

                        $valor_total_2 = $valor_total;

                        $valor_unitario = number_format($valor_unitario, 0, '.', '.');
                        $valor_total = '$ ' . number_format($valor_total, 0, '.', '.');

                        //foreach ($descripcion as $j => $linea)
                        //$descripcion_str .= $linea.'<br>';

                        $estado_pedido = $producto['estado'];

                        $bg_tr = '';
                        $bg_dot = '';

                        if ($tipo_sistema == 'Pedidos') {
                            if ($estado_pedido == 'PENDIENTE')
                                $bg_dot = 'bg-danger';
                            if ($estado_pedido == 'EN ESPERA') {
                                $bg_dot = 'bg-info';
                                $bg_tr = 'alert-info-2';
                            }
                            if ($estado_pedido == 'PREPARANDO')
                                $bg_dot = 'bg-warning';
                            if ($estado_pedido == 'DESPACHADO')
                                $bg_dot = 'bg-success';
                        }

                ?>
                        <tr>
                            <td>
                                <?php
                                if ($tipo_sistema == 'Pedidos') {
                                    if ($estado_pedido != 'PENDIENTE' && $estado_pedido != 'EN ESPERA') {
                                ?>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="check_<?php echo $i ?>" id="check_<?php echo $i ?>" onchange="cambios_check(this.checked,<?php echo $valor_total_2 ?>,document.getElementById('total_a_pagar_div').innerHTML);">
                                            <label class="form-check-label" for="check_<?php echo $i ?>"></label>
                                        </div>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="check_<?php echo $i ?>" id="check_<?php echo $i ?>" onchange="cambios_check(this.checked,<?php echo $valor_total_2 ?>,document.getElementById('total_a_pagar_div').innerHTML);">
                                        <label class="form-check-label" for="check_<?php echo $i ?>"></label>
                                    </div>
                                <?php
                                }
                                ?>
                            </td>
                            <td class="p-1"><?php echo $descripcion_str ?></td>
                            <td class="text-right p-1"><?php echo $valor_unitario; ?></td>
                            <td class="text-center p-1">
                                <b><?php echo $cant ?></b>
                            </td>
                            <td class="text-right p-1"><strong><?php echo $valor_total ?></strong></td>
                            <td class="p-1">
                                <span class="dot <?php echo $bg_dot ?> m-0"></span>
                            </td>
                        </tr>
                <?php
                    }
                }
                ?>

            </tbody>
        </table>
        <table class="table mb-1">
            <tbody>
                <tr>
                    <td class="p-1">
                        <h4>TOTAL</h4>
                    </td>
                    <td class="p-1 text-right">
                        <h3 class="m-0" id="total_a_pagar_div">$0</h3>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</form>
<div class="row m-0 px-2">
    <label class="text-center col py-1 px-1">Metodo de pago:</label>
    <select class="form-control form-control-sm col" id="metodo_pago_div" name="metodo_pago_div">

        <option value="">Seleccione uno...</option>
        <option value="Efectivo">Efectivo</option>
        <option value="Tarjeta">Tarjeta</option>
        <option value="Nequi">Nequi</option>
        <option value="Bancolombia">Bancolombia</option>
        <option value="Daviplata">Daviplata</option>
        <option value="Crédito">Crédito</option>
    </select>
</div>

<hr>
<div class="row m-0">
    <div class="col">
        <div class="form-floating">
            <textarea class="form-control" placeholder="Leave a comment here" id="observaciones_div" name="observaciones_div" style="height: 100px"></textarea>
            <label for="observaciones_div">Observaciones</label>
        </div>
    </div>
</div>
<hr>
<div class="row m-0">
    <div class="col text-center">
        <button type="button" class="btn btn-sm btn-outline-secondary btn-round px-5" data-bs-dismiss="modal">NO</button>
    </div>
    <div class="col text-center">
        <button type="button" class="btn btn-sm btn-outline-primary btn-round px-5" id="btn_dividir_cuenta">SI</button>
    </div>
</div>

<script type="text/javascript">
    function cambios_check(checked, valor, total) {
        var punto = '.';
        var signo = '$';
        total = total.replace(signo, '');
        total = total.replace(punto, '');
        total = total.replace(punto, '');
        total = total.replace(punto, '');

        if (checked)
            total = parseInt(total) + valor;
        else
            total = parseInt(total) - valor;

        new_total = total.toLocaleString('de-DE');
        new_total = '$' + new_total;
        //console.log(new_total);
        document.getElementById('total_a_pagar_div').innerHTML = new_total;
    }

    $('#btn_dividir_cuenta').click(function() {
        datos = $('#frm_dividir').serialize();
        cod_mesa = document.getElementById("cod_mesa_div").value;
        cod_cliente = document.getElementById("cod_cliente").value;
        metodo_pago = document.getElementById("metodo_pago_div").value;
        observaciones = document.getElementById("observaciones_div").value;
        $.ajax({
            type: "POST",
            data: datos + "&cod_mesa=" + cod_mesa + "&cod_cliente=" + cod_cliente + "&metodo_pago=" + metodo_pago + "&observaciones=" + observaciones,
            url: "procesos/dividir_cuenta.php",
            success: function(r) {
                datos = jQuery.parseJSON(r);
                if (datos['consulta'] == 1) {
                    w_alert({
                        titulo: 'Venta procesada correctamente',
                        tipo: 'success'
                    });
                    $('.modal-backdrop').remove();
                    document.querySelector("body").style.overflow = "auto";
                    atras();

                    if (datos['config_cajon'] == 'Automatico')
                        abrir_cajon();
                    if (cod_cliente != '')
                        $('#cod_cliente_fact').val(cod_cliente).trigger('change');

                    if (datos['config_imp'] == 'Manual' || datos['config_imp'] == 'Automática') {
                        $('#cod_venta_fact').val(datos['cod_venta']);
                        if (datos['config_imp'] == 'Automática')
                            setTimeout("$('#btn_generar_factura').click();", 500)
                        else
                            $("#Modal_Generar_Factura").modal('show');
                    }

                } else
                    w_alert({
                        titulo: datos['consulta'],
                        tipo: 'danger'
                    });
            }
        });
    });
</script>