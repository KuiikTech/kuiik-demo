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

require_once "../clases/permisos.php";
$obj_permisos = new permisos();

if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];

	$cod_compra_upload = $_POST['cod_compra_upload_2'];

	$imagen_factura = $_FILES['archivo_factura']['tmp_name'];

	if ($_FILES['archivo_factura']['name'] != '') {
		if (is_uploaded_file($imagen_factura)) {
			if ($_FILES['archivo_factura']['type'] == "image/jpeg" or $_FILES['archivo_factura']['type'] == "image/png") {
				if ($_FILES['archivo_factura']['size'] < 5000000) {
					$codigo = uniqid();
					$destino = __DIR__ . '/../../rancho1.witsoft.co/paginas/facturas_compras/' . $codigo . '.jpg';
					if (!file_exists($destino))
						$destino = __DIR__ . '/../soportes/compras/' . $codigo . '.jpg';
					$nombre_imagen = $codigo . '.jpg';
					if (move_uploaded_file($imagen_factura, $destino)) {

						$factura['nombre'] = $nombre_imagen;
						$factura['fecha'] = $fecha_h;
						$factura['usuario'] = $usuario;

						$factura = json_encode($factura, JSON_UNESCAPED_UNICODE);

						$sql = "UPDATE `compras` SET 
						`factura`='$factura'
						WHERE codigo='$cod_compra_upload'";

						$verificacion = mysqli_query($conexion, $sql);
					} else
						$verificacion = 'Error al subir la Imagen';
				} else
					$verificacion = 'Peso maximo de la imagen 5MB';
			} else
				$verificacion = 'Seleccione una imagen valida';
		} else
			$verificacion = 'Seleccione una imagen valida';
	} else
		$verificacion = 'Seleccione la imagen de la factura';
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion
);
echo json_encode($datos);
