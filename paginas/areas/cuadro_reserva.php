<?php
date_default_timezone_set('America/Bogota');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$area = $_GET['area'];
$orden = $_GET['orden'];

$cod_reserva = $_GET['cod_reserva'];

$sql = "SELECT `codigo`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_registro`, `cod_cliente`, `pagos`, `fecha_llegada`, `descuentos`, `code`, `creador` FROM `reservas` WHERE codigo = '$cod_reserva' order by nombre ASC";
$result = mysqli_query($conexion, $sql);
$mostrar = mysqli_fetch_row($result);

$cod_reserva = $mostrar[0];
$nombre = $mostrar[1];
$descripcion = $mostrar[2];
$productos = array();
if ($mostrar[3] != '')
    $productos = json_decode($mostrar[3], true);
$estado_reserva = $mostrar[4];
$fecha_registro = $mostrar[5];
$cod_cliente = $mostrar[6];

$cliente = array(
    'codigo' => '',
    'id' => '',
    'nombre' => '',
    'telefono' => '',
);

if ($cod_cliente != '') {
    $sql = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro` FROM `clientes` WHERE codigo = '$cod_cliente'";
    $result = mysqli_query($conexion, $sql);
    $ver = mysqli_fetch_row($result);

    $cliente = array(
        'codigo' => $ver[0],
        'id' => $ver[1],
        'nombre' => $ver[2],
        'telefono' => $ver[3]
    );
}

$pagos = array();
if ($mostrar[7] != '')
    $pagos = json_decode($mostrar[7], true);

$fecha_llegada = 'Sin fecha';
if ($mostrar[8] != null)
    $fecha_llegada = date('d-m-Y h:i A', strtotime($mostrar[8]));
$descuentos = $mostrar[9];
$code = $mostrar[10];

if ($estado_reserva == 'PENDIENTE')
    $bg_card = 'bg-danger';
if ($estado_reserva == 'CANCELADA')
    $bg_card = 'bg-info';
if ($estado_reserva == 'PROCESADA')
    $bg_card = 'bg-success';

if ($mostrar[3] != '') {
?>
    <div class="card-header border-bottom p-1 d-flex <?php echo $bg_card ?>">
        <h6 class="m-0 text-white">Cliente: <b><?php echo $cliente['nombre'] ?></b></h6>
    </div>
    <div class="card-body p-0">
        <ul class="list-group list-group-small list-group-flush">
            <?php
            $total_reserva = 0;
            foreach ($productos as $i => $producto) {
                $cod_producto = $producto['codigo'];
                $cant = $producto['cant'];
                $nombre_producto = $producto['descripcion'];
                $valor_unitario = $producto['valor_unitario'];
                $estado = $producto['estado'];

                $bg_tr = '';
                $bg_dot = '';
                if ($estado == 'PENDIENTE')
                    $bg_dot = 'bg-danger';
                if ($estado == 'PREPARANDO')
                    $bg_dot = 'bg-warning';
                if ($estado == 'DESPACHADO')
                    $bg_dot = 'bg-success';

                $valor_total = $cant * $valor_unitario;
                $total_reserva += $valor_total;

                $valor_unitario = number_format($valor_unitario, 0, '.', '.');
                $valor_total = '$' . number_format($valor_total, 0, '.', '.');
            ?>
                <li class="list-group-item d-flex row px-0 m-1 p-1 <?php echo $bg_tr ?>" id="tr_pedido_<?php echo $cod_reserva ?>_<?php echo $i ?>">
                    <h6 class="go-stats__label mb-1 text-dark text-truncate"><b><?php echo $cant ?></b> -<b><?php echo $nombre_producto ?></b> [$<?php echo number_format($valor_unitario, 0, '.', '.') ?>]</h6>
                </li>
            <?php
            }
            ?>
        </ul>
    </div>
    <div class="card-footer">
        <h6 class="m-0"><?php echo $fecha_llegada ?> [<b class="mb-0" id="tiempo_<?php echo $cod_reserva ?>"></b>]</h6>
    </div>

    <?php
    if ($mostrar[8] != null) {
        $fecha_llegada = date("M d Y G:i:s", strtotime($mostrar[8]));
    ?>
        <script type="text/javascript">
            countdown('<?php echo $fecha_llegada ?> GMT-0500', 'tiempo_<?php echo $cod_reserva ?>', 'FECHA PASADA');
        </script>
<?php
    }
}
?>