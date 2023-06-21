<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$fecha_inicial = $_GET['fecha_inicial'] . ' 00:00:00';
$fecha_final = $_GET['fecha_final'] . ' 23:59:59';

$meseros = array();
$total_areas = array();
$total_ventas = 0;

$sql = "SELECT `codigo`, `cliente`, `productos`, `pago`, `fecha`, `cobrador` FROM `ventas` WHERE fecha BETWEEN '$fecha_inicial' AND '$fecha_final' order by fecha ASC";
$result = mysqli_query($conexion, $sql);

while ($mostrar = mysqli_fetch_row($result)) {
    $total = 0;
    $productos_venta = json_decode($mostrar[2], true);
    foreach ($productos_venta as $i => $producto) {
        if (isset($producto['valor_unitario']) && isset($producto['cant'])) {
            $total += $producto['valor_unitario'] * $producto['cant'];
            $cod_mesero = $mostrar[5];
            $area = $producto['area'];
            $nombre_mesero = '';

            if (isset($total_areas[$area]))
                $total_areas[$area] += $producto['valor_unitario'] * $producto['cant'];
            else
                $total_areas[$area] = $producto['valor_unitario'] * $producto['cant'];

            if (isset($meseros[$cod_mesero])) {
                $meseros[$cod_mesero]['cant'] += $producto['cant'];
                $meseros[$cod_mesero]['total'] += $producto['valor_unitario'] * $producto['cant'];

                if (isset($meseros[$cod_mesero][$area])) {
                    $meseros[$cod_mesero][$area]['total'] += $producto['valor_unitario'] * $producto['cant'];
                    $meseros[$cod_mesero][$area]['cant'] += $producto['cant'];
                } else {
                    $meseros[$cod_mesero][$area]['total'] = $producto['valor_unitario'] * $producto['cant'];
                    $meseros[$cod_mesero][$area]['cant'] = $producto['cant'];
                }
            } else {
                $meseros[$cod_mesero]['cant'] = $producto['cant'];
                $meseros[$cod_mesero]['total'] = $producto['valor_unitario'] * $producto['cant'];

                $meseros[$cod_mesero][$area]['total'] = $producto['valor_unitario'] * $producto['cant'];
                $meseros[$cod_mesero][$area]['cant'] = $producto['cant'];

                $sql_e = "SELECT nombre, apellido, rol, foto FROM `usuarios` WHERE codigo = '$cod_mesero'";
                $result_e = mysqli_query($conexion, $sql_e);
                $ver_e = mysqli_fetch_row($result_e);
                if ($ver_e != null)
                    $nombre_mesero = $ver_e[0] . ' ' . $ver_e[1];
                $meseros[$cod_mesero]['nombre'] = $nombre_mesero;
            }
        }
    }
    $total_ventas += $total;
}

$nombre_tabla = 'Productos Vendidos entre ' . $_GET['fecha_inicial'] . ' y ' . $_GET['fecha_final'];

?>
<!-- Tabla Productos -->
<div class="card">
    <div class="card-body">
        <div class="d-sm-flex align-items-center mb-4">
            <h4 class="card-title text-center"><?php echo $nombre_tabla; ?></h4>
        </div>
        <div class="p-1">
            <table class="table text-dark table-sm Data_Table" id="tabla_ventas_productos" width="100%">
                <thead>
                    <tr class="text-center">
                        <th>Cod</th>
                        <th>Meseros</th>
                        <th>Areas</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="overflow-auto">
                    <?php
                    foreach ($meseros as $j => $producto) {
                    ?>
                        <tr>
                            <td class="text-center"><?php echo str_pad($j, 3, "0", STR_PAD_LEFT); ?></td>
                            <td><?php echo $producto['nombre'] ?></td>
                            <td class="text-left">
                                <table class="table table-sm table-borderless text-dark mb-0">
                                    <tbody>
                                        <?php
                                        foreach ($producto as $k => $area) {
                                            if ($k != 'nombre' && $k != 'cant' && $k != 'total') {
                                                echo '<tr><td class="p-0">' . $k . '</td><td class="text-right p-0"><b>$' . number_format($area['total'], 0, '.', '.') . '</b> (' . $area['cant'] . ' Productos)</td></tr>';
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </td>
                            <td class="text-right"><strong>$<?php echo number_format($producto['total'], 0, '.', '.') ?></strong></td>
                            <td></td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="row float-right mt-3">
            <h3>Total ventas: $<?php echo number_format($total_ventas, 0, '.', '.') ?></h3>

            <?php 
            foreach ($total_areas as $i => $area) {
                echo '<h5> - ' . $i . ': $' . number_format($area, 0, '.', '.') . '</h5>';
            }
            ?>
        </div>
    </div>
</div>

<!-- Modal detalles de venta-->
<div class="modal fade" id="Modal_venta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="div_modal_venta"></div>
    </div>
</div>

<!-- #END# Tabla Productos -->
<script type="text/javascript">
    $(document).ready(function() {
        $('.Data_Table').DataTable({
            responsive: true,
            columns: [{
                    responsivePriority: 1
                },
                {
                    responsivePriority: 2
                },
                {
                    responsivePriority: 3
                },
                {
                    responsivePriority: 4
                },
                {
                    responsivePriority: 6
                }
            ]
        });
    });

    $(".select2").select2();
</script>