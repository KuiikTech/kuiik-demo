<?php
date_default_timezone_set('America/Bogota');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();

$fecha_h = date('Y-m-d G:i:s');

$conexion = $obj_2->conexion();
$conexion = $obj_2->conexion();

if (isset($_SESSION['usuario_restaurante'])) {
    $usuario = $_SESSION['usuario_restaurante'];

    $sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
    $result_e = mysqli_query($conexion, $sql_e);
    $ver_e = mysqli_fetch_row($result_e);

    $rol = $ver_e[5];

    if ($rol == 'Administrador') {
        $verificacion = 1;

        $cod_caja = $_POST['cod_caja'];
        $caja = $_POST['caja'];
        $item = $_POST['item'];
        $cod_unico = $_POST['cod_unico'];
        if ($caja == 1) {
            $sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `info`, `creador`, `cajero`, `finalizador`, `estado`, `kilos_fin` FROM `caja` WHERE codigo = '$cod_caja'";
        } else if ($caja == 2) {
            $sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `info`, `creador`, `cajero`, `finalizador`, `estado`, `kilos_fin` FROM `caja2` WHERE codigo = '$cod_caja'";
        } else {
            $sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `info`, `creador`, `cajero`, `finalizador`, `estado`, `kilos_fin` FROM `caja3` WHERE codigo = '$cod_caja'";
        }
        $result = mysqli_query($conexion, $sql);
        $mostrar_caja = mysqli_fetch_row($result);

        $estado = $mostrar_caja[14];

        if ($estado == 'ABIERTA') {
            $ingresos = array();
            $nuevos_ingresos = array();
            $pos = 1;
            if ($mostrar_caja[9] != '')
                $ingresos = json_decode($mostrar_caja[9], true);

            foreach ($ingresos as $j => $pago) {
                if ($j != $item) {
                    $nuevos_ingresos[$pos] = $pago;
                    $pos++;
                }
            }

            if ($pos > 1)
                $nuevos_ingresos = json_encode($nuevos_ingresos, JSON_UNESCAPED_UNICODE);
            else
                $nuevos_ingresos = '';

            if ($caja == 1) {
                $sql = "UPDATE `caja` SET 
				`ingresos`='$nuevos_ingresos'
				WHERE codigo='$cod_caja'";
            } else if ($caja == 2) {
                $sql = "UPDATE `caja2` SET 
				`ingresos`='$nuevos_ingresos'
				WHERE codigo='$cod_caja'";
            } else {
                $sql = "UPDATE `caja3` SET 
				`ingresos`='$nuevos_ingresos'
				WHERE codigo='$cod_caja'";
            }

            $verificacion = mysqli_query($conexion, $sql);
            $verificacion = 1;

            if ($verificacion == 1) {
                $sql = "SELECT `codigo`, `cod_cliente`, `cliente`, `descripcion`, `valor`, `fecha_registro`, `fecha_pago`, `fecha_ingreso`, `creador`, `cobrador`, `cajero`, `estado`, `pagos` FROM `cuentas_por_cobrar` WHERE `pagos` LIKE '%$cod_unico%'";
                $result = mysqli_query($conexion, $sql);
                $mostrar = mysqli_fetch_row($result);

                $cod_cuenta = $mostrar[0];

                $pagos = array();
                $pagos_nuevos = array();
                $pos = 1;
                if ($mostrar[12] != '')
                    $pagos = json_decode($mostrar[12], true);

                foreach ($pagos as $i => $pago) {
                    if (isset($pago['cod_unico'])) {
                        if ($pago['cod_unico'] != $cod_unico) {
                            $pagos_nuevos[$pos] = $pago;
                            $pos++;
                        }
                    }
                    else {
                        $pagos_nuevos[$pos] = $pago;
                        $pos++;
                    }
                }
                if ($pos > 1)
                    $pagos_nuevos = json_encode($pagos_nuevos, JSON_UNESCAPED_UNICODE);
                else
                    $pagos_nuevos = '';

                $sql = "UPDATE `cuentas_por_cobrar` SET `pagos`='$pagos_nuevos', `estado`='EN MORA' WHERE codigo='$cod_cuenta'";
                $verificacion = mysqli_query($conexion, $sql);
            }
        } else
            $verificacion = 'No se eliminó el pago. La caja NO se encuentra abierta';
    } else
        $verificacion = 'Solo los administradores pueden borrar pago de servicios';
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion
);
echo json_encode($datos);
