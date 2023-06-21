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

	$cod_compra_upload = $_POST['cod_compra_upload'];
	$item_upload = $_POST['item_upload'];

	$imagen_soporte = $_FILES['archivo_soporte']['tmp_name'];

	if ($_FILES['archivo_soporte']['name'] != '') {
		if (is_uploaded_file($imagen_soporte)) {
			if ($_FILES['archivo_soporte']['type'] == "image/jpeg" or $_FILES['archivo_soporte']['type'] == "image/png") {
				if ($_FILES['archivo_soporte']['size'] < 5000000) {
					$codigo = uniqid();
					$destino = __DIR__ . '/../soportes/compras/' . $codigo . '.jpg';
					$nombre_imagen = $codigo . '.jpg';
					if (move_uploaded_file($imagen_soporte, $destino)) {
						$cod_compra = $cod_compra_upload;

						$sql = "SELECT `codigo`, `productos`, `proveedor`, `creador`, `fecha_registro`, `observaciones`, `estado`, `pagos` FROM `compras` WHERE codigo = '$cod_compra'";
						$result = mysqli_query($conexion, $sql);
						$mostrar = mysqli_fetch_row($result);

						$soportes = array();
						if ($mostrar[7] != '')
							$soportes = json_decode($mostrar[7], true);

						$soportes[$item_upload]['nombre'] = $nombre_imagen;
						$soportes[$item_upload]['fecha'] = $fecha_h;
						$soportes[$item_upload]['usuario'] = $usuario;

						$soportes = json_encode($soportes, JSON_UNESCAPED_UNICODE);

						$sql = "UPDATE `compras` SET 
						`pagos`='$soportes'
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
		$verificacion = 'Seleccione la imagen del soporte de pago';
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion
);
echo json_encode($datos);
