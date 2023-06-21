<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$cod_producto = $_GET['cod_producto'];

if (isset($_GET['fecha_inicial'])) {
    $fecha_inicial_v = $_GET['fecha_inicial'];
    $fecha_final_v = $_GET['fecha_final'];
    $fecha_inicial = $_GET['fecha_inicial'] . ' 00:00:00';
    $fecha_final = $_GET['fecha_final'] . ' 23:59:59';
} else {
    $fecha_inicial_v = date('Y-m-d', strtotime($fecha . '- 15 day'));
    $fecha_inicial = date('Y-m-d 00:00:00', strtotime($fecha . '- 15 day'));
    $fecha_final_v = $fecha;
    $fecha_final = $fecha_h;
}

?>

<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active text-gray" id="movimientos-tab" data-bs-toggle="tab" href="#movimientos" role="tab" aria-controls="movimientos" aria-selected="true"> Movimientos </a>
    </li>

    <li class="nav-item" hidden>
        <a class="nav-link text-gray" id="ingresos-tab" data-bs-toggle="tab" href="#ingresos" role="tab" aria-controls="ingresos" aria-selected="true"> Ingreso </a>
    </li>

    <li class="nav-item" hidden>
        <a class="nav-link text-gray" id="salidas-tab" data-bs-toggle="tab" href="#salidas" role="tab" aria-controls="salidas" aria-selected="true"> Salidas </a>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane active show py-2" id="movimientos" role="tabpanel" aria-labelledby="movimientos-tab">
        <div class="row text-center">
            <b class="col">Movimientos <small>[<?php echo $fecha_inicial_v . ' / ' . $fecha_final_v ?>]</small></b>
        </div>
        <div class="row">
            <table class="table text-dark table-sm" id="tabla_movimientos">
                <thead>
                    <tr class="text-center">
                        <th class="py-1" class="table-plus text-dark datatable-nosort px-1">#</th>
                        <th class="py-1" class="px-1">Tipo Movimiento</th>
                        <th class="py-1">Cant</th>
                        <th class="py-1">Observaciones</th>
                        <th class="py-1">Creador</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode`, `movimientos` FROM `productos` WHERE codigo='$cod_producto'";
                    $result_producto = mysqli_query($conexion, $sql_producto);
                    $mostrar_producto = mysqli_fetch_row($result_producto);

                    $movimientos = array();
                    $movimientos_nuevos = array();
                    if ($mostrar_producto != NULL) {
                        if ($mostrar_producto[12] != '')
                            $movimientos = json_decode($mostrar_producto[12], true);
                    }

                    foreach ($movimientos as $i => $movimiento) {
                        $movimientos_nuevos[$i]['fecha'] = $movimiento['fecha'];
                        $movimientos_nuevos[$i]['Tipo'] = $movimiento['Tipo'];
                        $movimientos_nuevos[$i]['Cant'] = $movimiento['Cant'];
                        $movimientos_nuevos[$i]['creador'] = $movimiento['creador'];
                        $movimientos_nuevos[$i]['Observaciones'] = $movimiento['Observaciones'];
                    }

                    arsort($movimientos_nuevos);
                    $num_item = 1;
                    foreach ($movimientos_nuevos as $i => $movimiento) {
                        $tipo = $movimiento['Tipo'];
                        $cant = $movimiento['Cant'];
                        $creador = $movimiento['creador'];
                        $observaciones = $movimiento['Observaciones'];
                        $fecha_mov = date('Y-m-d G:i:s', strtotime($movimiento['fecha']));

                        if ($creador != 0) {
                            $sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$creador'";
                            $result_e = mysqli_query($conexion, $sql_e);
                            $ver_e = mysqli_fetch_row($result_e);
                            if ($ver_e != NULL) {
                                $nombre_aux = explode(' ', $ver_e[0]);
                                $apellido_aux = explode(' ', $ver_e[1]);
                                $creador = $nombre_aux[0] . ' ' . $apellido_aux[0];
                            } else
                                $creador = '?';
                        } else
                            $creador = 'Sistema';

                        $text_cant = 'text-danger';

                        if ($cant > 0)
                            $text_cant = 'text-success';

                        if ($fecha_mov > $fecha_inicial && $fecha_mov < $fecha_final) {
                    ?>
                            <tr role="row" class="odd">
                                <td class="text-center p-0"><?php echo $num_item ?></td>
                                <td class="text-left p-0"><?php echo $tipo ?></td>
                                <td class="text-center p-0 <?php echo $text_cant ?>"><b><?php echo $cant ?></b></td>
                                <td class="text-left p-0"><?php echo $observaciones ?></td>
                                <td class="text-center p-0 lh-1">
                                    <b><?php echo $creador ?></b>
                                    <br>
                                    <small><?php echo $fecha_mov ?></small>
                                </td>
                            </tr>
                    <?php
                            $num_item++;
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="row">
            <table>
                <thead>
                    <tr class="text-center">
                        <th class="text-center" colspan="4">Nuevo Movimiento</th>
                    </tr>
                </thead>
                <tbody>
                    <tr role="row" class="odd">
                        <td class="text-left p-1">
                            <select class="form-control form-control-sm" name="tipo_mov" id="tipo_mov">
                                <option value="">Seleccione tipo</option>
                                <option value="Ingreso">Ingreso</option>
                                <option value="Salida">Salida</option>
                            </select>
                        </td>
                        <td class="text-center p-1" width="120px">
                            <input type="number" class="form-control form-control-sm" name="cant_mov" id="cant_mov" placeholder="Cantidad">
                        </td>
                        <td class="text-left p-1">
                            <input type="text" class="form-control form-control-sm" name="obs_mov" id="obs_mov" placeholder="Observaciones del movimiento" autocomplete="off">
                        </td>
                        <td class="text-center p-1">
                            <button class="btn btn-outline-primary btn-round py-1" id="btn_agregar_mov">
                                <span class="fa fa-save"></span> Procesar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="tab-pane fade py-2" id="ingresos" role="tabpanel" aria-labelledby="ingresos-tab">
        <div class="row text-center">
            <b class="col">Ingresos (Compras) <small>[<?php echo $fecha_inicial_v . ' / ' . $fecha_final_v ?>]</small></b>
        </div>
        <div class="row">
            <table class="table text-dark table-sm" id="tabla_compras">
                <thead>
                    <tr class="text-center">
                        <th>#</th>
                        <th>cod</th>
                        <th>Cantidad</th>
                        <th>Registro</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody class="overflow-auto">
                    <?php
                    $num_item = 1;
                    $cant_compras = 0;
                    while (0) { // ($mostrar = mysqli_fetch_row($result)) {
                        $cod_producto = $mostrar[1];
                        $estado = $mostrar[4];
                        $fecha_autorizacion = strftime("%e %b %Y", strtotime($mostrar[8]));
                        $fecha_autorizacion = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_autorizacion));
                        $fecha_autorizacion .= date(' | h:i A', strtotime($mostrar[8]));

                        $sql_producto = "SELECT `cod_producto`, `descripción`, `tipo`, `valor`, `inventario`, `cod_categoria`, `fecha_modificacion`, `barcode` FROM `productos` WHERE cod_producto='$cod_producto'";
                        $result_producto = mysqli_query($conexion, $sql_producto);
                        $mostrar_producto = mysqli_fetch_row($result_producto);

                        $cant_compras += $mostrar[2];

                    ?>
                        <tr>
                            <td class="text-center"><?php echo $num_item ?></td>
                            <td class="text-center"><?php echo str_pad($mostrar[0], 3, "0", STR_PAD_LEFT) ?></td>
                            <td class="text-center"><b><?php echo $mostrar[2] ?></b></td>
                            <td class="text-center"><?php echo $fecha_autorizacion ?></td>
                            <td class="text-center"><?php echo $estado ?></td>
                        </tr>
                    <?php
                        $num_item++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="tab-pane fade py-2" id="salidas" role="tabpanel" aria-labelledby="salidas-tab">
        <div class="row text-center">
            <b class="col">Salidas (Ventas) en CAJA <small>[<?php echo $fecha_inicial_v . ' / ' . $fecha_final_v ?>]</small></b>
        </div>
        <div class="row">
            <table class="table text-dark table-sm" id="tabla_inventario">
                <thead>
                    <tr class="text-center">
                        <th width="50px">Cod Caja</th>
                        <th width="50px">Inicial</th>
                        <th width="50px">Final</th>
                        <th width="150px">Apertura</th>
                        <th width="150px">Cierre</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `finalizador` FROM `caja` WHERE fecha_apertura BETWEEN '$fecha_inicial' AND '$fecha_final'";
                    $result = mysqli_query($conexion, $sql);
                    while ($mostrar = mysqli_fetch_row($result)) {
                        $cajero = '---';
                        $finalizador = '---';
                        $fecha_apertura_v = '---';

                        $estado = $mostrar[12];

                        if ($estado == 'ABIERTA') {
                            $cajero = $mostrar[11];

                            $sql_e = "SELECT nombre, rol, foto FROM usuarios WHERE codigo = '$cajero'";
                            $result_e = mysqli_query($conexion, $sql_e);
                            $ver_e = mysqli_fetch_row($result_e);

                            $cajero = $ver_e[0];

                            $fecha_apertura_v = strftime("%e %b %Y", strtotime($mostrar[2]));
                            $fecha_apertura_v = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_apertura_v));
                            $fecha_apertura_v .= date(' | h:i A', strtotime($mostrar[2]));
                        }

                        if ($estado == 'CERRADA') {
                            $fecha_cierre_v = strftime("%e %b %Y", strtotime($mostrar[3]));
                            $fecha_cierre_v = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_cierre_v));
                            $fecha_cierre_v .= date(' | h:i A', strtotime($mostrar[3]));

                            $fecha_apertura_v = strftime("%e %b %Y", strtotime($mostrar[2]));
                            $fecha_apertura_v = ucfirst(iconv("ISO-8859-1", "UTF-8", $fecha_apertura_v));
                            $fecha_apertura_v .= date(' | h:i A', strtotime($mostrar[2]));

                            $finalizador = $mostrar[13];
                            $sql_e = "SELECT nombre, rol, foto FROM usuarios WHERE codigo = '$finalizador'";
                            $result_e = mysqli_query($conexion, $sql_e);
                            $ver_e = mysqli_fetch_row($result_e);

                            if ($ver_e != null)
                                $finalizador = $ver_e[0];
                        } else
                            $fecha_cierre_v = '---';

                        $inventario = json_decode($mostrar[4], true);
                        $inventario = array();
                        foreach ($inventario as $i => $producto) {
                            if ($producto['codigo'] == $cod_producto) {
                                $inventario_inicial = $producto['inventario_inicial'];
                                $inventario_final = $producto['inventario_final'];

                                if ($inventario_final == NULL) {
                                    $sql_producto = "SELECT `cod_producto`, `descripción`, `tipo`, `valor`, `inventario`, `cod_categoria`,`fecha_modificacion` FROM `productos` WHERE cod_producto='$cod_producto'";
                                    $result_producto = mysqli_query($conexion, $sql_producto);
                                    $mostrar_producto = mysqli_fetch_row($result_producto);

                                    $inventario_final = $mostrar_producto[4];
                                }
                                if ($estado == 'CREADA')
                                    $inventario_final = '---';
                    ?>
                                <tr>
                                    <td class="text-center p-1"><?php echo str_pad($mostrar[0], 3, "0", STR_PAD_LEFT) ?></td>
                                    <td class="text-center p-1"><b><?php echo $inventario_inicial ?></b></td>
                                    <td class="text-center p-1"><b><?php echo $inventario_final ?></b></td>
                                    <td class="text-center p-1"><?php echo $fecha_apertura_v ?></td>
                                    <td class="text-center p-1"><?php echo $fecha_cierre_v ?></td>
                                </tr>
                    <?php
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#tabla_movimientos').DataTable();
    $('#tabla_compras').DataTable();
    $('#tabla_inventario').DataTable();

    $('#obs_mov').keypress(function(e) {
        if (e.keyCode == 13)
            $('#btn_agregar_mov').click();
    });

    $('#cant_mov').keypress(function(e) {
        if (e.keyCode == 13)
            $('#btn_agregar_mov').click();
    });

    $('#btn_agregar_mov').click(function() {
        document.getElementById('div_loader').style.display = 'block';
        document.getElementById("btn_agregar_mov").disabled = true;
        tipo_mov = document.getElementById("tipo_mov").value;
        cant_mov = document.getElementById("cant_mov").value;
        obs_mov = document.getElementById("obs_mov").value;
        if (tipo_mov != '' && cant_mov != '' && obs_mov != '') {
            $.ajax({
                type: "POST",
                data: "cod_producto=<?php echo $cod_producto ?>" + "&tipo_mov=" + tipo_mov + "&cant_mov=" + cant_mov + "&obs_mov=" + obs_mov,
                url: "procesos/agregar_mov_producto.php",
                success: function(r) {
                    datos = jQuery.parseJSON(r);
                    if (datos['consulta'] == 1) {
                        w_alert({
                            titulo: 'Movimiento agregado con exito',
                            tipo: 'success'
                        });
                        document.getElementById('div_loader').style.display = 'block';
                        $('#contenido_informe').load('paginas/detalles/informe_producto.php/?cod_producto=<?php echo $cod_producto ?>&fecha_inicial=<?php echo $fecha_inicial_v ?>&fecha_final=<?php echo $fecha_final_v ?>', function() {
                            cerrar_loader();
                        });
                        document.getElementById("b_inventario").innerHTML = datos['inventario'];
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
        } else {
            if (tipo_mov == '') {
                w_alert({
                    titulo: 'Seleccione el tipo de movimiento',
                    tipo: 'danger'
                });
                document.getElementById("tipo_mov").focus();
            } else if (cant_mov == '') {
                w_alert({
                    titulo: 'Ingrese la cantidad',
                    tipo: 'danger'
                });
                document.getElementById("cant_mov").focus();
            } else if (obs_mov == '') {
                w_alert({
                    titulo: 'Escriba las observaciones para el movimiento',
                    tipo: 'danger'
                });
                document.getElementById("obs_mov").focus();
            }
        }

        cerrar_loader();
        document.getElementById("btn_agregar_mov").disabled = false;
    });
</script>