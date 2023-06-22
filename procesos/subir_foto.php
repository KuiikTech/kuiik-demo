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
	$verificacion = 1;

	$cod_servicio_upload = $_POST['cod_servicio_upload'];
	foreach ($_FILES["archivo_foto"]['tmp_name'] as $key => $tmp_name) {
		$imagen_tmp = $_FILES['archivo_foto']['tmp_name'][$key];
		if ($_FILES['archivo_foto']['name'][$key] != '') {
			if (is_uploaded_file($imagen_tmp)) {
				if ($_FILES['archivo_foto']['type'][$key] == "image/jpeg" or $_FILES['archivo_foto']['type'][$key] == "image/png") {
					if ($_FILES['archivo_foto']['size'][$key] > 5000000)
						$verificacion = 'Peso maximo de la imagen 5MB (#' . ($key + 1) . ')';
				} else
					$verificacion = 'Seleccione una imagen valida (#' . ($key + 1) . ')';
			} else
				$verificacion = 'Seleccione una imagen valida (#' . ($key + 1) . ')';
		} else
			$verificacion = 'Seleccione la foto que desea subir (#' . ($key + 1) . ')';
	}
	if ($verificacion == 1) {
		foreach ($_FILES["archivo_foto"]['tmp_name'] as $key => $tmp_name) {
			$imagen_tmp = $_FILES['archivo_foto']['tmp_name'][$key];
			$codigo = uniqid() . '_' . $key;
			$destino = __DIR__ . '/../../rancho1.Kuiik.co/fotos_servicios/' . $codigo . '.jpg';
			if (!file_exists($destino))
				$destino = __DIR__ . '/../fotos_servicios/' . $codigo . '.jpg';
			$nombre_imagen = $codigo . '.jpg';
			if (move_uploaded_file($imagen_tmp, $destino)) {
				$cod_servicio = $cod_servicio_upload;

				$sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro`, `local` FROM `servicios` WHERE codigo = '$cod_servicio'";
				$result = mysqli_query($conexion, $sql);
				$mostrar = mysqli_fetch_row($result);

				$informacion = array();

				if ($mostrar[4] != '') {
					$informacion = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[4]);
					$informacion = str_replace('	', ' ', $informacion);
					$informacion = json_decode($informacion, true);
				}

				$fotos = array();
				$pos_f = 1;
				if (isset($informacion['fotos'])) {
					$fotos = $informacion['fotos'];
					$pos_f += count($fotos);
				}

				$fotos[$pos_f] = array(
					'nombre'  => $nombre_imagen,
					'usuario'  => $usuario,
					'fecha'  => $fecha_h
				);

				$informacion['fotos'] = $fotos;

				$informacion = json_encode($informacion, JSON_UNESCAPED_UNICODE);

				$sql = "UPDATE `servicios` SET 
						`informacion`='$informacion'
						WHERE codigo='$cod_servicio'";

				$verificacion_2 = mysqli_query($conexion, $sql);

				if ($verificacion_2 != 1)
					$verificacion .= 'Error al subir la imagen' . $key;
			} else
				$verificacion .= 'Error al subir la Imagen' . $key;
		}
	}
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion
);
echo json_encode($datos);
