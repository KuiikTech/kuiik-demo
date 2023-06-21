<?php
date_default_timezone_set('America/Bogota');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();
$obj_2 = new conectar();

$fecha = date('Y-m-d');
$fecha_h = date('Y-m-d G:i:s');

$conexion = $obj_2->conexion();

if (isset($_SESSION['usuario_restaurante'])) {
    $usuario = $_SESSION['usuario_restaurante'];

    $verificacion = 1;

    if ($_POST['ciudad_proveedor'] == '')
        $verificacion = 'Ingrese la ciudad del proveedor';
    if ($_POST['telefono_proveedor'] == '')
        $verificacion = 'Ingrese el número de telefono del proveedor';
    if ($_POST['nombre_proveedor'] == '')
        $verificacion = 'Ingrese el nombre del proveedor';

    if ($verificacion == 1) {
        $telefono = $_POST['telefono_proveedor'];
        $sql = "SELECT `codigo`, `nombre`, `telefono`, `ciudad`, `fecha_registro` FROM `proveedores` WHERE telefono = '$telefono' AND estado != 'ELIMINADO'";
        $result = mysqli_query($conexion, $sql);
        $ver = mysqli_fetch_row($result);

        if ($ver == null) {
            $datos = array(
                ucwords($_POST['nombre_proveedor']),
                $_POST['telefono_proveedor'],
                ucwords($_POST['ciudad_proveedor'])
            );

            $sql = "INSERT INTO `proveedores`(`nombre`, `telefono`, `ciudad`, `fecha_registro`) VALUES (
                '$datos[0]',
			'$datos[1]',
			'$datos[2]',
			'$fecha')";

            $verificacion = mysqli_query($conexion, $sql);
        } else
            $verificacion = 'Ya se encuentra registrado un proveedor con ese número de telefono';
    }
} else
    $verificacion = 'Reload';

$datos = array(
    'consulta' => $verificacion
);
echo json_encode($datos);
