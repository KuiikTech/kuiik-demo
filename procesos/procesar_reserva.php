<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();
$conexion = $obj_2->conexion();
$conexion = $obj_2->conexion();
$fecha_h = date('Y-m-d G:i:s');

$cod_mesa = 0;
$cod_reserva = 0;
$config_imp = '';

if (isset($_SESSION['usuario_restaurante'])) {
    $usuario = $_SESSION['usuario_restaurante'];

    require_once "../clases/permisos.php";
    $obj_permisos = new permisos();
    $acceso = $obj_permisos->buscar_permiso($usuario, 'PDV', 'PROCESAR');

    if ($acceso == 'SI') {
        $cod_mesa = $_POST['cod_mesa'];
        $cod_reserva = $_POST['cod_reserva'];
        $total = 0;
        $total_descuento = 0;
        $verificacion = 1;

        $sql_stock = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Validar Stock'";
        $result_stock = mysqli_query($conexion, $sql_stock);
        $mostrar_stock = mysqli_fetch_row($result_stock);

        $validar_stock = $mostrar_stock[2];

        $sql = "SELECT `codigo`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_registro`, `cod_cliente`, `pagos`, `fecha_llegada`, `descuentos`, `code`, `creador` FROM `reservas` WHERE codigo = '$cod_reserva' order by nombre ASC";
        $result = mysqli_query($conexion, $sql);
        $mostrar = mysqli_fetch_row($result);

        $cod_reserva = $mostrar[0];
        $estado_reserva = $mostrar[4];

        if ($estado_reserva == 'PENDIENTE') {
            $productos = array();
            if ($mostrar[3] != '')
                $productos = json_decode($mostrar[3], true);

            foreach ($productos as $key => $value) {
                $cod_producto = $value['codigo'];
                $cant = $value['cant'];

                $sql_producto = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo='$cod_producto'";
                $result_producto = mysqli_query($conexion, $sql_producto);
                $mostrar_producto = mysqli_fetch_row($result_producto);

                $stock = $mostrar_producto[4];
                /*  
                if ($mostrar_producto[9] == 'Producto') {
                    if ($validar_stock == 'SI') {
                        if ($cant > $stock)
                            $verificacion = 'El inventario para ' . $mostrar_producto[1] . ' es: ' . $stock;
                    }
                }
                */
            }

            if ($verificacion == 1) {
                $cod_cliente = $mostrar[6];
                $pagos = $mostrar[7];

                $productos_mesa = json_encode($productos, JSON_UNESCAPED_UNICODE);
                $sql = "UPDATE `mesas` SET 
					`productos`='$productos_mesa',
                    `fecha_apertura`='$fecha_h',";

                if ($cod_cliente == 0)
                    $sql .= "`cod_cliente`= NULL,";
                else
                    $sql .= "`cod_cliente`='$cod_cliente',";
                    
                $sql .= "`estado`='OCUPADA',
                    `pagos`='$pagos',
                    `mesero`='$usuario'
					WHERE cod_mesa='$cod_mesa'";

                $verificacion = mysqli_query($conexion, $sql);
            }
            if ($verificacion == 1) {
                $sql = "UPDATE `reservas` SET `estado` = 'PROCESADA' WHERE `codigo` = '$cod_reserva'";
                $verificacion = mysqli_query($conexion, $sql);
            }
        } else
            $verificacion = 'La reserva ya fue procesada';
    } else
        $verificacion = 'No tienes permisos para procesar ventas';
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion,
    'cod_reserva' => $cod_reserva,
    'cod_mesa' => $cod_mesa
);

echo json_encode($datos);
